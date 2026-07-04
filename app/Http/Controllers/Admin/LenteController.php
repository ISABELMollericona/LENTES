<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\ImagenLente;
use App\Models\Lente;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LenteController extends Controller
{
    public function index(Request $request)
    {
        $query = Lente::with(['categoria', 'marca', 'imagenes']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('genero')) {
            $query->where('genero', $request->genero);
        }

        $lentes = $query->orderByDesc('created_at')->paginate(15);

        return view('admin.lentes.index', compact('lentes'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();

        return view('admin.lentes.create', compact('categorias', 'marcas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:lentes,codigo',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'required|exists:marcas,id',
            'genero' => 'required|in:hombre,mujer,unisex',
            'tipo_lente' => 'required|in:optical,sol,ambos',
            'tipo_montura' => 'required|in:completa,semi_al_aire,al_aire',
            'material' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|in:disponible,vendido',
            'imagen_principal' => 'nullable|image|max:2048',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|max:2048',
            'fecha_registro' => 'nullable|date',
        ]);

        if ($request->hasFile('imagen_principal')) {
            $validated['imagen_principal'] = $request->file('imagen_principal')->store('lentes', 'public');
        }

        $lente = Lente::create($validated);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $orden => $imagen) {
                $url = $imagen->store('lentes/galeria', 'public');
                $lente->imagenes()->create([
                    'url' => $url,
                    'orden' => $orden,
                ]);
            }
        }

        return redirect()->route('admin.lentes.index')
            ->with('success', 'Lente creado exitosamente.');
    }

    public function show(Lente $lente)
    {
        $lente->load(['categoria', 'marca', 'imagenes']);

        return view('admin.lentes.show', compact('lente'));
    }

    public function edit(Lente $lente)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();

        return view('admin.lentes.edit', compact('lente', 'categorias', 'marcas'));
    }

    public function update(Request $request, Lente $lente)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:lentes,codigo,' . $lente->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'required|exists:marcas,id',
            'genero' => 'required|in:hombre,mujer,unisex',
            'tipo_lente' => 'required|in:optical,sol,ambos',
            'tipo_montura' => 'required|in:completa,semi_al_aire,al_aire',
            'material' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|in:disponible,vendido',
            'imagen_principal' => 'nullable|image|max:2048',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|max:2048',
            'fecha_registro' => 'nullable|date',
        ]);

        if ($request->hasFile('imagen_principal')) {
            if ($lente->imagen_principal) {
                Storage::disk('public')->delete($lente->imagen_principal);
            }
            $validated['imagen_principal'] = $request->file('imagen_principal')->store('lentes', 'public');
        }

        $lente->update($validated);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $orden => $imagen) {
                $url = $imagen->store('lentes/galeria', 'public');
                $lente->imagenes()->create([
                    'url' => $url,
                    'orden' => $orden,
                ]);
            }
        }

        return redirect()->route('admin.lentes.index')
            ->with('success', 'Lente actualizado exitosamente.');
    }

    public function destroy(Lente $lente)
    {
        if ($lente->imagen_principal) {
            Storage::disk('public')->delete($lente->imagen_principal);
        }

        foreach ($lente->imagenes as $imagen) {
            Storage::disk('public')->delete($imagen->url);
        }

        $lente->imagenes()->delete();
        $lente->delete();

        return redirect()->route('admin.lentes.index')
            ->with('success', 'Lente eliminado exitosamente.');
    }

    public function cambiarEstado(Request $request, Lente $lente)
    {
        $request->validate(['estado' => 'required|in:disponible,vendido']);

        $lente->update(['estado' => $request->estado]);

        return redirect()->route('admin.lentes.index')
            ->with('success', 'Estado del lente actualizado.');
    }
}

<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Lente;
use App\Models\Marca;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $query = Lente::with(['categoria', 'marca', 'imagenes']);

        if ($request->filled('search')) {
            $query->buscar($request->search);
        }
        if ($request->filled('genero')) {
            $query->porGenero($request->genero);
        }
        if ($request->filled('tipo_montura')) {
            $query->porTipoMontura($request->tipo_montura);
        }
        if ($request->filled('marca_id')) {
            $query->porMarca($request->marca_id);
        }
        if ($request->filled('marca')) {
            $marcaObj = Marca::where('nombre', 'ILIKE', $request->marca)->first();
            if ($marcaObj) {
                $query->porMarca($marcaObj->id);
            }
        }
        if ($request->filled('categoria_id')) {
            $query->porCategoria($request->categoria_id);
        }
        if ($request->filled('color')) {
            $query->porColor($request->color);
        }
        if ($request->filled('precio_min') && $request->filled('precio_max')) {
            $query->porPrecio($request->precio_min, $request->precio_max);
        }

        $sort = $request->input('sort', 'reciente');
        match ($sort) {
            'precio_asc' => $query->orderBy('precio'),
            'precio_desc' => $query->orderByDesc('precio'),
            'nombre' => $query->orderBy('nombre'),
            default => $query->orderByDesc('created_at'),
        };

        $lentes = $query->paginate(12);
        $marcas = Marca::orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();

        return view('lentes.index', compact('lentes', 'marcas', 'categorias'));
    }

    public function show(Lente $lente)
    {
        $lente->load(['categoria', 'marca', 'imagenes']);
        return view('lentes.show', compact('lente'));
    }
}

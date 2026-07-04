<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Lente;
use App\Models\Marca;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
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
        if ($request->filled('categoria_id')) {
            $query->porCategoria($request->categoria_id);
        }
        if ($request->filled('color')) {
            $query->porColor($request->color);
        }
        if ($request->filled('precio_min') && $request->filled('precio_max')) {
            $query->porPrecio($request->precio_min, $request->precio_max);
        }

        $perPage = $request->input('per_page', 12);
        $lentes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->success($lentes);
    }

    public function show(Lente $lente): JsonResponse
    {
        $lente->load(['categoria', 'marca', 'imagenes']);
        return $this->success($lente);
    }

    public function categorias(): JsonResponse
    {
        return $this->success(Categoria::withCount('lentes')->get());
    }

    public function marcas(): JsonResponse
    {
        return $this->success(Marca::withCount('lentes')->get());
    }
}

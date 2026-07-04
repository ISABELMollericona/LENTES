<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    use ApiResponse;

    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pedidos = $this->orderService->historialUsuario($request->user()->id);
        return $this->success($pedidos);
    }

    public function show(Request $request, int $pedido): JsonResponse
    {
        $pedido = \App\Models\Pedido::with([
            'detallePedidos.lente.imagenes',
            'detallePedidos.lente.marca',
            'pago'
        ])
            ->where('usuario_id', $request->user()->id)
            ->findOrFail($pedido);

        return $this->success($pedido);
    }
}

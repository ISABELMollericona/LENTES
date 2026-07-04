<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lente;
use App\Services\CartService;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CartService $cartService,
        private OrderService $orderService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $items = $this->cartService->listar($request->user()->id);
        $total = $this->cartService->total($request->user()->id);

        return $this->success([
            'items' => $items,
            'total' => $total,
            'cantidad' => $items->count(),
        ]);
    }

    public function agregar(Request $request, Lente $lente): JsonResponse
    {
        try {
            $item = $this->cartService->agregar($request->user()->id, $lente->id);
            return $this->success($item->load('lente'), 'Lente agregado al carrito');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 409);
        }
    }

    public function eliminar(Request $request, int $carrito): JsonResponse
    {
        try {
            $this->cartService->eliminar($request->user()->id, $carrito);
            return $this->success(null, 'Lente eliminado del carrito');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 404);
        }
    }

    public function confirmarCompra(Request $request): JsonResponse
    {
        try {
            $pedido = $this->orderService->crearPedidoDesdeCarrito($request->user()->id);
            return $this->success($pedido, 'Compra confirmada exitosamente');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 409);
        }
    }
}

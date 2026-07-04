<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function crearPedidoDesdeCarrito(int $usuarioId): Pedido
    {
        $items = $this->cartService->listar($usuarioId);

        if ($items->isEmpty()) {
            throw new \Exception('El carrito está vacío.');
        }

        $noDisponibles = $this->cartService->validarDisponibilidad($usuarioId);
        if (!empty($noDisponibles)) {
            $nombres = collect($noDisponibles)->pluck('lente.nombre')->implode(', ');
            throw new \Exception("Los siguientes lentes ya no están disponibles: {$nombres}");
        }

        return DB::transaction(function () use ($usuarioId, $items) {
            $total = $items->sum(fn($item) => $item->lente->precio);

            $pedido = Pedido::create([
                'usuario_id' => $usuarioId,
                'codigo' => Pedido::generarCodigo(),
                'fecha_pedido' => now(),
                'total' => $total,
                'estado' => 'pendiente',
            ]);

            foreach ($items as $item) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'lente_id' => $item->lente_id,
                    'precio_unitario' => $item->lente->precio,
                ]);

                $item->lente->marcarComoVendido();
            }

            $this->cartService->vaciar($usuarioId);

            return $pedido->load('detallePedidos.lente');
        });
    }

    public function cambiarEstado(int $pedidoId, string $estado): Pedido
    {
        $pedido = Pedido::findOrFail($pedidoId);
        $pedido->update(['estado' => $estado]);

        return $pedido;
    }

    public function historialUsuario(int $usuarioId)
    {
        return Pedido::with('detallePedidos.lente', 'pago')
            ->where('usuario_id', $usuarioId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

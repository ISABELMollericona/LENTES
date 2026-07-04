<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentService
{
    public function registrarPago(int $pedidoId, string $metodoPago, float $monto): Pago
    {
        $pedido = Pedido::findOrFail($pedidoId);

        if ($pedido->pago) {
            throw new \Exception('Este pedido ya tiene un pago registrado.');
        }

        return Pago::create([
            'pedido_id' => $pedidoId,
            'metodo_pago' => $metodoPago,
            'fecha_pago' => now(),
            'monto' => $monto,
            'estado' => 'pendiente',
        ]);
    }

    public function confirmarPago(int $pagoId): Pago
    {
        $pago = Pago::findOrFail($pagoId);
        $pago->update(['estado' => 'aprobado']);

        $pago->pedido->update(['estado' => 'confirmado']);

        return $pago;
    }

    public function generarComprobante(int $pagoId)
    {
        $pago = Pago::with('pedido.usuario', 'pedido.detallePedidos.lente')->findOrFail($pagoId);

        $pdf = Pdf::loadView('pagos.comprobante', compact('pago'));
        return $pdf->download("comprobante-{$pago->pedido->codigo}.pdf");
    }
}

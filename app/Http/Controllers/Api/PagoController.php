<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Services\PaymentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    use ApiResponse;

    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function store(Request $request, Pedido $pedido): JsonResponse
    {
        if ($pedido->usuario_id !== $request->user()->id) {
            return $this->error('No autorizado.', 403);
        }

        $validated = $request->validate([
            'metodo_pago' => 'required|in:tarjeta_credito,tarjeta_debito,transferencia,efectivo',
        ]);

        try {
            $pago = $this->paymentService->registrarPago(
                $pedido->id,
                $validated['metodo_pago'],
                $pedido->total
            );

            return $this->success($pago, 'Pago registrado exitosamente', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 409);
        }
    }

    public function comprobante(Request $request, int $pago): JsonResponse
    {
        $pago = \App\Models\Pago::with('pedido')
            ->whereHas('pedido', fn($q) => $q->where('usuario_id', $request->user()->id))
            ->findOrFail($pago);

        $pdf = $this->paymentService->generarComprobante($pago->id);

        return response()->streamDownload(fn() => print($pdf->output()), "comprobante-{$pago->pedido->codigo}.pdf");
    }
}

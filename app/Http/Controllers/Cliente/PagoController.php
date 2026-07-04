<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function create(Pedido $pedido)
    {
        if ((int) $pedido->usuario_id !== (int) auth()->id()) {
            abort(403);
        }

        $pedido->load(['detallePedidos.lente.marca']);

        return view('pagos.create', compact('pedido'));
    }

    public function store(Request $request, Pedido $pedido)
    {
        if ((int) $pedido->usuario_id !== (int) auth()->id()) {
            abort(403);
        }

        $pago = $pedido->pago()->create([
            'metodo_pago' => 'qr',
            'fecha_pago'  => now(),
            'monto'       => $pedido->total,
            'estado'      => 'pendiente',
        ]);

        return redirect()->route('pagos.comprobante', $pago)
            ->with('success', 'Pago registrado. Confirmaremos tu transferencia QR pronto.');
    }

    public function comprobante(Pago $pago)
    {
        if ($pago->pedido->usuario_id !== auth()->id()) {
            abort(403);
        }

        $pago->load(['pedido.usuario', 'pedido.detallePedidos.lente.marca']);

        return view('pagos.comprobante', compact('pago'));
    }
}

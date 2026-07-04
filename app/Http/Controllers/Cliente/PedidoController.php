<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with(['detallePedidos.lente', 'pago'])
            ->where('usuario_id', auth()->id());

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $pedidos = $query->orderByDesc('created_at')->paginate(10);

        return view('pedidos.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        if ($pedido->usuario_id !== auth()->id()) {
            abort(403);
        }

        $pedido->load(['detallePedidos.lente.marca', 'pago']);

        return view('pedidos.show', compact('pedido'));
    }
}

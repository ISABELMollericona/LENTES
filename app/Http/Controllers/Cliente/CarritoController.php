<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Lente;
use App\Services\CartService;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index()
    {
        $items = $this->cartService->listar(auth()->id());
        $total = $this->cartService->total(auth()->id());

        return view('carrito.index', compact('items', 'total'));
    }

    public function agregar(Lente $lente)
    {
        try {
            $this->cartService->agregar(auth()->id(), $lente->id);
            $count = $this->cartService->contar(auth()->id());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lente agregado al carrito.',
                    'count' => $count,
                ]);
            }

            return redirect()->route('carrito.index')->with('success', 'Lente agregado al carrito.');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function eliminar(\App\Models\Carrito $carrito)
    {
        $this->cartService->eliminar(auth()->id(), $carrito->id);

        return redirect()->route('carrito.index')->with('success', 'Lente eliminado del carrito.');
    }

    /** Paso 1: validar carrito y redirigir al checkout */
    public function confirmarCompra(Request $request)
    {
        $noDisponibles = $this->cartService->validarDisponibilidad(auth()->id());

        if (!empty($noDisponibles)) {
            return back()->with('error', 'Algunos lentes ya no están disponibles. Revisa tu carrito.');
        }

        return redirect()->route('carrito.checkout');
    }

    /** Paso 2: mostrar formulario de dirección + medidas */
    public function checkout()
    {
        $items = $this->cartService->listar(auth()->id());

        if ($items->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        $total = $this->cartService->total(auth()->id());

        return view('carrito.checkout', compact('items', 'total'));
    }

    /** Paso 3: crear pedido con dirección + medidas y redirigir al pago QR */
    public function procesarCheckout(Request $request)
    {
        $items = $this->cartService->listar(auth()->id());

        if ($items->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        $validated = $request->validate([
            'direccion_entrega'    => 'required|string|min:10|max:500',
            'medidas'              => 'sometimes|array',
            'medidas.*.dp'         => 'nullable|numeric|min:40|max:80',
            'medidas.*.od_esfera'  => 'nullable|numeric|min:-20|max:20',
            'medidas.*.od_cilindro'=> 'nullable|numeric|min:-10|max:10',
            'medidas.*.od_eje'     => 'nullable|integer|min:0|max:180',
            'medidas.*.od_adicion' => 'nullable|numeric|min:0|max:4',
            'medidas.*.oi_esfera'  => 'nullable|numeric|min:-20|max:20',
            'medidas.*.oi_cilindro'=> 'nullable|numeric|min:-10|max:10',
            'medidas.*.oi_eje'     => 'nullable|integer|min:0|max:180',
            'medidas.*.oi_adicion' => 'nullable|numeric|min:0|max:4',
        ], [
            'direccion_entrega.required' => 'La dirección de entrega es obligatoria.',
            'direccion_entrega.min'      => 'La dirección debe tener al menos 10 caracteres.',
        ]);

        $total = $this->cartService->total(auth()->id());

        $pedido = auth()->user()->pedidos()->create([
            'codigo'            => \App\Models\Pedido::generarCodigo(),
            'fecha_pedido'      => now(),
            'total'             => $total,
            'estado'            => 'pendiente',
            'direccion_entrega' => $validated['direccion_entrega'],
        ]);

        foreach ($items as $item) {
            $m = $validated['medidas'][$item->lente_id] ?? [];
            $esSol = ($item->lente->tipo_lente === 'sol');

            $pedido->detallePedidos()->create([
                'lente_id'          => $item->lente_id,
                'precio_unitario'   => $item->lente->precio,
                'distancia_pupilar' => $m['dp'] ?? null,
                'od_esfera'         => $esSol ? null : ($m['od_esfera']   ?? null),
                'od_cilindro'       => $esSol ? null : ($m['od_cilindro'] ?? null),
                'od_eje'            => $esSol ? null : ($m['od_eje']       ?? null),
                'od_adicion'        => $esSol ? null : ($m['od_adicion']  ?? null),
                'oi_esfera'         => $esSol ? null : ($m['oi_esfera']   ?? null),
                'oi_cilindro'       => $esSol ? null : ($m['oi_cilindro'] ?? null),
                'oi_eje'            => $esSol ? null : ($m['oi_eje']       ?? null),
                'oi_adicion'        => $esSol ? null : ($m['oi_adicion']  ?? null),
            ]);
        }

        $this->cartService->vaciar(auth()->id());

        return redirect()->route('pagos.create', $pedido)
            ->with('success', 'Pedido creado. Escanea el QR para completar el pago.');
    }
}

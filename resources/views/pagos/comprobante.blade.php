<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante – {{ $pago->pedido->codigo }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', Arial, sans-serif; font-size: 14px; color: #1a1a1a; padding: 40px; max-width: 820px; margin: auto; }
        .header { text-align: center; border-bottom: 2px solid #D4AF37; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #D4AF37; margin: 0; font-size: 28px; letter-spacing: 2px; }
        .header p { color: #666; margin: 5px 0 0; }
        .section-title { font-size: 13px; font-weight: 700; color: #D4AF37; text-transform: uppercase; letter-spacing: 1px; margin: 22px 0 8px; border-left: 3px solid #D4AF37; padding-left: 8px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 6px 8px; }
        .info-table .label { color: #888; width: 170px; font-size: 12px; }
        .info-table .value { font-weight: 600; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 13px; }
        .items-table th { background: #1a1a1a; color: #D4AF37; padding: 10px 12px; text-align: left; }
        .items-table td { padding: 9px 12px; border-bottom: 1px solid #eee; vertical-align: top; }
        .total-row td { font-weight: 700; font-size: 15px; padding-top: 14px; border-bottom: none; }
        .total-amount { color: #D4AF37; font-size: 19px; }
        .medidas-block { font-size: 12px; color: #555; margin-top: 4px; }
        .medidas-block span { display: inline-block; margin-right: 10px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-pending { background: #f59f00; color: #fff; }
        .badge-ok { background: #2b8a3e; color: #fff; }
        .qr-note { background: #fffbe6; border: 1px solid #D4AF37; border-radius: 6px; padding: 12px 16px; margin: 16px 0; font-size: 13px; }
        .direccion-box { background: #f8f8f8; border-left: 3px solid #D4AF37; padding: 10px 14px; border-radius: 4px; margin-bottom: 18px; font-size: 13px; }
        .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; color: #aaa; font-size: 11px; }
        @media print { body { padding: 20px; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>✦ ÓPTICA GOLDEN ✦</h1>
        <p>Comprobante de Pedido</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nº Comprobante:</td>
            <td class="value">{{ $pago->id }}-{{ $pago->pedido->codigo }}</td>
            <td class="label" style="text-align:right;">Estado pago:</td>
            <td style="text-align:right;">
                <span class="badge {{ $pago->estado === 'aprobado' ? 'badge-ok' : 'badge-pending' }}">
                    {{ strtoupper($pago->estado) }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="label">Fecha:</td>
            <td class="value">{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</td>
            <td class="label" style="text-align:right;">Método:</td>
            <td class="value" style="text-align:right;"><i>Pago por QR</i></td>
        </tr>
        <tr>
            <td class="label">Cliente:</td>
            <td class="value" colspan="3">{{ $pago->pedido->usuario->nombre }} {{ $pago->pedido->usuario->apellido }}</td>
        </tr>
        <tr>
            <td class="label">Correo:</td>
            <td class="value" colspan="3">{{ $pago->pedido->usuario->email }}</td>
        </tr>
    </table>

    @if($pago->pedido->direccion_entrega)
    <div class="section-title">Dirección de entrega</div>
    <div class="direccion-box">
        📍 {{ $pago->pedido->direccion_entrega }}
    </div>
    @endif

    @if($pago->estado === 'pendiente')
    <div class="qr-note">
        <strong>⏳ Tu pago QR está pendiente de confirmación.</strong><br>
        Una vez que procesemos tu transferencia, recibirás la confirmación. Guarda este comprobante como referencia.
    </div>
    @endif

    <div class="section-title">Detalle de productos y medidas</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Medidas</th>
                <th style="text-align:right;">Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pago->pedido->detallePedidos as $detalle)
            @php $esSol = $detalle->lente->tipo_lente === 'sol'; @endphp
            <tr>
                <td>
                    <strong>{{ $detalle->lente->nombre ?? 'Lente' }}</strong><br>
                    <small style="color:#888;">{{ $detalle->lente->marca->nombre ?? '' }} · {{ $detalle->lente->codigo ?? '' }}</small>
                </td>
                <td>
                    <span class="badge" style="background:{{ $esSol ? '#8B5E00' : '#1a4a2e' }}; color:#fff;">
                        {{ $esSol ? 'Sol' : 'Óptico' }}
                    </span>
                </td>
                <td>
                    <div class="medidas-block">
                        @if($detalle->distancia_pupilar)
                            <span><strong>DP:</strong> {{ $detalle->distancia_pupilar }} mm</span>
                        @endif
                        @if(!$esSol)
                            @if($detalle->od_esfera !== null || $detalle->od_cilindro !== null)
                            <br><strong>OD:</strong>
                            @if($detalle->od_esfera !== null) <span>Esf {{ number_format($detalle->od_esfera,2) }}</span>@endif
                            @if($detalle->od_cilindro !== null)<span>Cil {{ number_format($detalle->od_cilindro,2) }}</span>@endif
                            @if($detalle->od_eje !== null)     <span>Eje {{ $detalle->od_eje }}°</span>@endif
                            @if($detalle->od_adicion !== null) <span>Adi {{ number_format($detalle->od_adicion,2) }}</span>@endif
                            @endif
                            @if($detalle->oi_esfera !== null || $detalle->oi_cilindro !== null)
                            <br><strong>OI:</strong>
                            @if($detalle->oi_esfera !== null) <span>Esf {{ number_format($detalle->oi_esfera,2) }}</span>@endif
                            @if($detalle->oi_cilindro !== null)<span>Cil {{ number_format($detalle->oi_cilindro,2) }}</span>@endif
                            @if($detalle->oi_eje !== null)     <span>Eje {{ $detalle->oi_eje }}°</span>@endif
                            @if($detalle->oi_adicion !== null) <span>Adi {{ number_format($detalle->oi_adicion,2) }}</span>@endif
                            @endif
                        @endif
                        @if(!$detalle->distancia_pupilar && ($esSol || ($detalle->od_esfera === null && $detalle->oi_esfera === null)))
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </td>
                <td style="text-align:right; font-weight:600;">Bs {{ number_format($detalle->precio_unitario, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align:right; font-weight:600; color:#888; font-size:12px;">Envío:</td>
                <td style="text-align:right; color:#2b8a3e; font-weight:600;">Gratis</td>
            </tr>
            <tr class="total-row">
                <td colspan="3" style="text-align:right;">Total:</td>
                <td class="total-amount" style="text-align:right;">Bs {{ number_format($pago->monto, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="text-align:center; margin-top:16px;">
        <button onclick="window.print()" style="background:#D4AF37; color:#fff; border:none; padding:10px 28px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer;">
            🖨 Imprimir comprobante
        </button>
        <a href="{{ route('pedidos.index') }}" style="display:inline-block; margin-left:12px; padding:10px 24px; border:1px solid #D4AF37; color:#D4AF37; border-radius:6px; font-size:14px; text-decoration:none;">
            Ver mis pedidos
        </a>
    </div>

    <div class="footer">
        <p>Óptica Golden — Todos los derechos reservados</p>
        <p>Comprobante generado el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>

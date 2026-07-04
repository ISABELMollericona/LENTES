<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedidos';

    protected $fillable = [
        'pedido_id', 'lente_id', 'precio_unitario',
        'od_esfera', 'od_cilindro', 'od_eje', 'od_adicion',
        'oi_esfera', 'oi_cilindro', 'oi_eje', 'oi_adicion',
        'distancia_pupilar',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function lente()
    {
        return $this->belongsTo(Lente::class, 'lente_id');
    }
}

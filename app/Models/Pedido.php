<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'usuario_id', 'codigo', 'fecha_pedido', 'total',
        'estado', 'observaciones', 'direccion_entrega',
    ];

    protected $casts = [
        'usuario_id'   => 'integer',
        'fecha_pedido' => 'date',
        'total'        => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'pedido_id');
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePorFecha($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_pedido', [$desde, $hasta]);
    }

    public static function generarCodigo(): string
    {
        $ultimo = self::max('id') ?? 0;
        return 'PED-' . str_pad($ultimo + 1, 8, '0', STR_PAD_LEFT);
    }
}

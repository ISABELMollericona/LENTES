<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleRecomendacion extends Model
{
    protected $table = 'detalle_recomendaciones';

    protected $fillable = [
        'recomendacion_id', 'lente_id', 'compatibilidad',
        'justificacion', 'orden'
    ];

    protected $casts = [
        'compatibilidad' => 'decimal:2',
    ];

    public function recomendacion()
    {
        return $this->belongsTo(Recomendacion::class, 'recomendacion_id');
    }

    public function lente()
    {
        return $this->belongsTo(Lente::class, 'lente_id');
    }
}

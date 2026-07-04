<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recomendacion extends Model
{
    protected $table = 'recomendaciones';

    protected $fillable = [
        'usuario_id', 'analisis_facial_id', 'forma_rostro',
        'presupuesto_max', 'uso_lentes', 'estilo',
        'color_favorito', 'tipo_montura'
    ];

    protected $casts = [
        'presupuesto_max' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function analisisFacial()
    {
        return $this->belongsTo(AnalisisFacial::class, 'analisis_facial_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleRecomendacion::class, 'recomendacion_id')->orderBy('orden');
    }

    public function chatIA()
    {
        return $this->hasMany(ChatIA::class, 'recomendacion_id');
    }

    public function lentesRecomendados()
    {
        return $this->belongsToMany(Lente::class, 'detalle_recomendaciones', 'recomendacion_id', 'lente_id')
            ->withPivot('compatibilidad', 'justificacion', 'orden')
            ->orderByPivot('orden');
    }
}

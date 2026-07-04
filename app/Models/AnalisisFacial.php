<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalisisFacial extends Model
{
    protected $table = 'analisis_faciales';

    protected $fillable = [
        'usuario_id', 'imagen_url', 'forma_rostro',
        'puntos_referencia', 'confianza', 'tiempo_procesamiento'
    ];

    protected $casts = [
        'puntos_referencia' => 'json',
        'confianza' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function recomendaciones()
    {
        return $this->hasMany(Recomendacion::class, 'analisis_facial_id');
    }
}

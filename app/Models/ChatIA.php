<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatIA extends Model
{
    protected $table = 'chat_ia';

    protected $fillable = [
        'usuario_id', 'recomendacion_id', 'sesion_id',
        'mensaje', 'respuesta', 'tipo'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function recomendacion()
    {
        return $this->belongsTo(Recomendacion::class, 'recomendacion_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenLente extends Model
{
    protected $table = 'imagenes_lentes';

    protected $fillable = ['lente_id', 'url', 'orden'];

    public function getUrlCompletaAttribute(): string
    {
        return str_starts_with($this->url, 'img/')
            ? asset($this->url)
            : asset('storage/'.$this->url);
    }

    public function lente()
    {
        return $this->belongsTo(Lente::class, 'lente_id');
    }
}

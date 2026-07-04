<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = ['nombre', 'slug', 'descripcion'];

    public function lentes()
    {
        return $this->hasMany(Lente::class, 'categoria_id');
    }
}

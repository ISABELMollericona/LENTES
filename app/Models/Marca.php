<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marcas';

    protected $fillable = ['nombre', 'slug', 'descripcion'];

    public function lentes()
    {
        return $this->hasMany(Lente::class, 'marca_id');
    }
}

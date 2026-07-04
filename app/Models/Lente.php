<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lente extends Model
{
    protected $table = 'lentes';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'categoria_id', 'genero',
        'tipo_lente', 'tipo_montura', 'material', 'color', 'marca_id',
        'precio', 'imagen_principal', 'estado', 'fecha_registro'
    ];

    protected $appends = ['imagen_url'];

    protected $casts = [
        'precio' => 'decimal:2',
        'fecha_registro' => 'date',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenLente::class, 'lente_id')->orderBy('orden');
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class, 'lente_id');
    }

    public function detalleRecomendaciones()
    {
        return $this->hasMany(DetalleRecomendacion::class, 'lente_id');
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopeVendidos($query)
    {
        return $query->where('estado', 'vendido');
    }

    public function scopePorPrecio($query, $min, $max)
    {
        return $query->whereBetween('precio', [$min, $max]);
    }

    public function scopePorGenero($query, $genero)
    {
        return $query->where('genero', $genero);
    }

    public function scopePorTipoMontura($query, $tipo)
    {
        return $query->where('tipo_montura', $tipo);
    }

    public function scopePorMarca($query, $marcaId)
    {
        return $query->where('marca_id', $marcaId);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopePorColor($query, $color)
    {
        return $query->where('color', 'LIKE', "%{$color}%");
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'LIKE', "%{$termino}%")
              ->orWhere('codigo', 'LIKE', "%{$termino}%")
              ->orWhere('descripcion', 'LIKE', "%{$termino}%");
        });
    }

    public function getImagenUrlAttribute(): string
    {
        $path = $this->imagen_principal;

        if (!$path && $this->relationLoaded('imagenes') && $this->imagenes->isNotEmpty()) {
            $path = $this->imagenes->first()->url;
        }

        if ($path) {
            return str_starts_with($path, 'img/')
                ? asset($path)
                : asset('storage/'.$path);
        }

        // Fallback determinístico: imagen del dataset basada en el ID del lente
        $index = ($this->id ?? 0) % 60;
        $carpeta = ($this->tipo_lente === 'sol') ? 'sunglasses/S' : 'eyeglasses/E';
        return asset("img/lentes/dataset/{$carpeta}_{$index}.jpg");
    }

    public function estaDisponible(): bool
    {
        return $this->estado === 'disponible';
    }

    public function marcarComoVendido(): void
    {
        $this->update(['estado' => 'vendido']);
    }
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'rol_id', 'nombre', 'apellido', 'email', 'telefono',
        'direccion', 'password', 'foto', 'estado', 'ultimo_acceso'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ultimo_acceso' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'usuario_id');
    }

    public function carritos()
    {
        return $this->hasMany(Carrito::class, 'usuario_id');
    }

    public function analisisFaciales()
    {
        return $this->hasMany(AnalisisFacial::class, 'usuario_id');
    }

    public function recomendaciones()
    {
        return $this->hasMany(Recomendacion::class, 'usuario_id');
    }

    public function chatIA()
    {
        return $this->hasMany(ChatIA::class, 'usuario_id');
    }

    public function esAdmin(): bool
    {
        return $this->role && $this->role->nombre === 'admin';
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}

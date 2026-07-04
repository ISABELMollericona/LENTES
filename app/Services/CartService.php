<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\Lente;
use Illuminate\Support\Collection;

class CartService
{
    public function agregar(int $usuarioId, int $lenteId): Carrito
    {
        $lente = Lente::findOrFail($lenteId);

        if (!$lente->estaDisponible()) {
            throw new \Exception('Este lente no está disponible.');
        }

        $existe = Carrito::where('usuario_id', $usuarioId)
            ->where('lente_id', $lenteId)
            ->exists();

        if ($existe) {
            throw new \Exception('Este lente ya está en tu carrito.');
        }

        return Carrito::create([
            'usuario_id' => $usuarioId,
            'lente_id' => $lenteId,
        ]);
    }

    public function eliminar(int $usuarioId, int $carritoId): void
    {
        $item = Carrito::where('usuario_id', $usuarioId)
            ->where('id', $carritoId)
            ->firstOrFail();

        $item->delete();
    }

    public function listar(int $usuarioId): Collection
    {
        return Carrito::with('lente.categoria', 'lente.marca', 'lente.imagenes')
            ->where('usuario_id', $usuarioId)
            ->get();
    }

    public function vaciar(int $usuarioId): void
    {
        Carrito::where('usuario_id', $usuarioId)->delete();
    }

    public function total(int $usuarioId): float
    {
        return $this->listar($usuarioId)->sum(fn($item) => $item->lente->precio);
    }

    public function validarDisponibilidad(int $usuarioId): array
    {
        $items = $this->listar($usuarioId);
        $noDisponibles = [];

        foreach ($items as $item) {
            if (!$item->lente->estaDisponible()) {
                $noDisponibles[] = $item;
            }
        }

        return $noDisponibles;
    }

    public function contar(int $usuarioId): int
    {
        return Carrito::where('usuario_id', $usuarioId)->count();
    }

    public static function contarStatic(int $usuarioId): int
    {
        return Carrito::where('usuario_id', $usuarioId)->count();
    }
}

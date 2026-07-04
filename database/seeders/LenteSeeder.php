<?php

namespace Database\Seeders;

use App\Models\Lente;
use Illuminate\Database\Seeder;

class LenteSeeder extends Seeder
{
    public function run(): void
    {
        $lentes = [
            [
                'codigo' => 'LEN-001', 'nombre' => 'Ray-Ban Clubmaster', 'categoria_id' => 1, 'marca_id' => 1,
                'genero' => 'hombre', 'tipo_lente' => 'optical', 'tipo_montura' => 'completa',
                'material' => 'Acetato', 'color' => 'Negro/Carey', 'precio' => 750.00, 'estado' => 'disponible',
                'fecha_registro' => now(),
            ],
            [
                'codigo' => 'LEN-002', 'nombre' => 'Oakley Rectangular', 'categoria_id' => 1, 'marca_id' => 2,
                'genero' => 'hombre', 'tipo_lente' => 'optical', 'tipo_montura' => 'completa',
                'material' => 'Metal', 'color' => 'Plateado', 'precio' => 680.00, 'estado' => 'disponible',
                'fecha_registro' => now(),
            ],
            [
                'codigo' => 'LEN-003', 'nombre' => 'Polaroid Aviador', 'categoria_id' => 2, 'marca_id' => 3,
                'genero' => 'unisex', 'tipo_lente' => 'sol', 'tipo_montura' => 'al_aire',
                'material' => 'Metal', 'color' => 'Dorado', 'precio' => 550.00, 'estado' => 'disponible',
                'fecha_registro' => now(),
            ],
            [
                'codigo' => 'LEN-004', 'nombre' => 'Vogue Butterfly', 'categoria_id' => 1, 'marca_id' => 4,
                'genero' => 'mujer', 'tipo_lente' => 'optical', 'tipo_montura' => 'semi_al_aire',
                'material' => 'Acetato', 'color' => 'Rosado', 'precio' => 620.00, 'estado' => 'disponible',
                'fecha_registro' => now(),
            ],
            [
                'codigo' => 'LEN-005', 'nombre' => 'Carrera Deportivo', 'categoria_id' => 4, 'marca_id' => 5,
                'genero' => 'hombre', 'tipo_lente' => 'sol', 'tipo_montura' => 'completa',
                'material' => 'Plástico TR90', 'color' => 'Azul/Neón', 'precio' => 480.00, 'estado' => 'disponible',
                'fecha_registro' => now(),
            ],
            [
                'codigo' => 'LEN-006', 'nombre' => 'Arnette Urban', 'categoria_id' => 1, 'marca_id' => 6,
                'genero' => 'hombre', 'tipo_lente' => 'optical', 'tipo_montura' => 'completa',
                'material' => 'Acetato', 'color' => 'Negro Mate', 'precio' => 420.00, 'estado' => 'disponible',
                'fecha_registro' => now(),
            ],
            [
                'codigo' => 'LEN-007', 'nombre' => 'Persol Clásico', 'categoria_id' => 1, 'marca_id' => 7,
                'genero' => 'hombre', 'tipo_lente' => 'optical', 'tipo_montura' => 'completa',
                'material' => 'Acetato', 'color' => 'Marrón', 'precio' => 890.00, 'estado' => 'disponible',
                'fecha_registro' => now(),
            ],
            [
                'codigo' => 'LEN-008', 'nombre' => 'Diesel Audaz', 'categoria_id' => 2, 'marca_id' => 8,
                'genero' => 'hombre', 'tipo_lente' => 'sol', 'tipo_montura' => 'completa',
                'material' => 'Metal/Acetato', 'color' => 'Negro/Rojo', 'precio' => 520.00, 'estado' => 'vendido',
                'fecha_registro' => now(),
            ],
            [
                'codigo' => 'LEN-009', 'nombre' => 'Ray-Ban Wayfarer', 'categoria_id' => 1, 'marca_id' => 1,
                'genero' => 'unisex', 'tipo_lente' => 'optical', 'tipo_montura' => 'completa',
                'material' => 'Acetato', 'color' => 'Negro', 'precio' => 780.00, 'estado' => 'disponible',
                'fecha_registro' => now(),
            ],
            [
                'codigo' => 'LEN-010', 'nombre' => 'Oakley Holbrook', 'categoria_id' => 2, 'marca_id' => 2,
                'genero' => 'hombre', 'tipo_lente' => 'sol', 'tipo_montura' => 'completa',
                'material' => 'Plástico O-Matter', 'color' => 'Gris', 'precio' => 650.00, 'estado' => 'disponible',
                'fecha_registro' => now(),
            ],
            [
                'codigo' => 'LEN-011', 'nombre' => 'Vogue Elegance', 'categoria_id' => 1, 'marca_id' => 4,
                'genero' => 'mujer', 'tipo_lente' => 'optical', 'tipo_montura' => 'semi_al_aire',
                'material' => 'Metal', 'color' => 'Dorado/Blanco', 'precio' => 580.00, 'estado' => 'disponible',
                'fecha_registro' => now(),
            ],
            [
                'codigo' => 'LEN-012', 'nombre' => 'Polaroid Sport', 'categoria_id' => 4, 'marca_id' => 3,
                'genero' => 'unisex', 'tipo_lente' => 'sol', 'tipo_montura' => 'completa',
                'material' => 'Plástico', 'color' => 'Azul', 'precio' => 380.00, 'estado' => 'disponible',
                'fecha_registro' => now(),
            ],
        ];

        foreach ($lentes as $lente) {
            Lente::firstOrCreate(['codigo' => $lente['codigo']], $lente);
        }
    }
}

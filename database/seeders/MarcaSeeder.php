<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            ['nombre' => 'Ray-Ban', 'slug' => 'ray-ban', 'descripcion' => 'Marca icónica de lentes desde 1937'],
            ['nombre' => 'Oakley', 'slug' => 'oakley', 'descripcion' => 'Lentes deportivos y de alto rendimiento'],
            ['nombre' => 'Polaroid', 'slug' => 'polaroid', 'descripcion' => 'Lentes con tecnología de polarización'],
            ['nombre' => 'Vogue', 'slug' => 'vogue', 'descripcion' => 'Lentes de moda y tendencia'],
            ['nombre' => 'Carrera', 'slug' => 'carrera', 'descripcion' => 'Lentes con estilo deportivo'],
            ['nombre' => 'Arnette', 'slug' => 'arnette', 'descripcion' => 'Lentes urbanos y juveniles'],
            ['nombre' => 'Persol', 'slug' => 'persol', 'descripcion' => 'Lentes artesanales italianos'],
            ['nombre' => 'Diesel', 'slug' => 'diesel', 'descripcion' => 'Lentes con estilo audaz'],
        ];

        foreach ($marcas as $marca) {
            Marca::firstOrCreate(['slug' => $marca['slug']], $marca);
        }
    }
}

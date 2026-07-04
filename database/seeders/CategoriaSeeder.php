<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Lentes Ópticos', 'slug' => 'lentes-opticos', 'descripcion' => 'Lentes para corrección visual'],
            ['nombre' => 'Lentes de Sol', 'slug' => 'lentes-de-sol', 'descripcion' => 'Lentes con protección UV'],
            ['nombre' => 'Lentes Progresivos', 'slug' => 'lentes-progresivos', 'descripcion' => 'Lentes multifocales'],
            ['nombre' => 'Lentes Deportivos', 'slug' => 'lentes-deportivos', 'descripcion' => 'Lentes para actividad física'],
            ['nombre' => 'Lentes Infantiles', 'slug' => 'lentes-infantiles', 'descripcion' => 'Lentes para niños'],
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            CategoriaSeeder::class,
            MarcaSeeder::class,
            LenteSeeder::class,
        ]);

        // Importar dataset si la carpeta existe
        $datasetPath = base_path('dataset/Glasses Dataset');
        if (is_dir($datasetPath)) {
            $this->command->info('Importando dataset de lentes...');
            \Illuminate\Support\Facades\Artisan::call('lentes:importar-dataset', [
                '--limit' => 60,
                '--tipo'  => 'all',
            ]);
            $this->command->info(\Illuminate\Support\Facades\Artisan::output());
        }
    }
}

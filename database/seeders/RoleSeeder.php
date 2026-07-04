<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['nombre' => 'admin'], ['descripcion' => 'Administrador del sistema']);
        Role::firstOrCreate(['nombre' => 'cliente'], ['descripcion' => 'Cliente registrado']);
    }
}

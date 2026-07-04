<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@opticagolden.com'],
            [
                'rol_id' => 1,
                'nombre' => 'Admin',
                'apellido' => 'Golden',
                'telefono' => '12345678',
                'direccion' => 'Oficina Central',
                'password' => Hash::make('admin123'),
                'estado' => 'activo',
            ]
        );
    }
}

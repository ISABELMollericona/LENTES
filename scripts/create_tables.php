<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Disable schema transactions globally
DB::connection()->setSchemaTransaction(false);

$migrations = [
    '2024_01_01_000001_create_roles_table' => function () {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    },
    '2024_01_01_000002_create_usuarios_table' => function () {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('email', 180)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->string('provider', 50)->nullable();
            $table->string('provider_id', 255)->nullable();
            $table->rememberToken();
            $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();
        });
    },
    '2024_01_01_000003_create_categorias_table' => function () {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    },
    '2024_01_01_000004_create_marcas_table' => function () {
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    },
    '2024_01_01_000005_create_lentes_table' => function () {
        Schema::create('lentes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('material', 100)->nullable();
            $table->string('color', 100)->nullable();
            $table->string('tipo_montura', 50)->nullable();
            $table->string('forma_rostro', 50)->nullable();
            $table->string('genero', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->string('tipo_lente', 50)->nullable();
            $table->string('codigo', 50)->nullable()->unique();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('marca_id')->nullable()->constrained('marcas')->nullOnDelete();
            $table->timestamps();
        });
    },
    '2024_01_01_000006_create_imagenes_lentes_table' => function () {
        Schema::create('imagenes_lentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lente_id')->constrained('lentes')->cascadeOnDelete();
            $table->string('url', 255);
            $table->boolean('es_principal')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    },
    '2024_01_01_000007_create_pedidos_table' => function () {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->decimal('total', 10, 2);
            $table->string('estado', 50)->default('pendiente');
            $table->text('direccion_envio')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    },
    '2024_01_01_000008_create_detalle_pedidos_table' => function () {
        Schema::create('detalle_pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('lente_id')->constrained('lentes')->cascadeOnDelete();
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    },
    '2024_01_01_000009_create_pagos_table' => function () {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->decimal('monto', 10, 2);
            $table->string('metodo_pago', 50);
            $table->string('estado', 50)->default('pendiente');
            $table->string('comprobante_url', 255)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    },
    '2024_01_01_000010_create_carritos_table' => function () {
        Schema::create('carritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('lente_id')->constrained('lentes')->cascadeOnDelete();
            $table->integer('cantidad')->default(1);
            $table->timestamps();
            $table->unique(['usuario_id', 'lente_id']);
        });
    },
    '2024_01_01_000011_create_analisis_faciales_table' => function () {
        Schema::create('analisis_faciales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('imagen_url', 255);
            $table->enum('forma_rostro', ['ovalado', 'redondo', 'cuadrado', 'rectangular', 'corazon', 'diamante'])->nullable();
            $table->json('puntos_referencia')->nullable();
            $table->decimal('confianza', 5, 2)->nullable();
            $table->integer('tiempo_procesamiento')->nullable();
            $table->timestamps();
        });
    },
    '2024_01_01_000012_create_recomendaciones_table' => function () {
        Schema::create('recomendaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('analisis_facial_id')->nullable()->constrained('analisis_faciales')->nullOnDelete();
            $table->text('mensaje');
            $table->string('tipo', 50)->nullable();
            $table->json('datos')->nullable();
            $table->timestamps();
        });
    },
    '2024_01_01_000013_create_detalle_recomendaciones_table' => function () {
        Schema::create('detalle_recomendaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recomendacion_id')->constrained('recomendaciones')->cascadeOnDelete();
            $table->foreignId('lente_id')->constrained('lentes')->cascadeOnDelete();
            $table->decimal('puntaje', 5, 2)->nullable();
            $table->text('motivo')->nullable();
            $table->timestamps();
        });
    },
    '2024_01_01_000014_create_chat_ia_table' => function () {
        Schema::create('chat_ia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->text('mensaje_usuario');
            $table->text('respuesta_ia');
            $table->string('contexto', 50)->nullable();
            $table->json('metadatos')->nullable();
            $table->timestamps();
        });
    },
    '2024_01_01_000015_add_dataset_origen_to_lentes_table' => function () {
        Schema::table('lentes', function (Blueprint $table) {
            $table->string('dataset_origen', 50)->nullable()->after('codigo');
            $table->string('imagen_dataset_url', 500)->nullable()->after('dataset_origen');
        });
    },
];

$batch = 1;
$ran = 0;

foreach ($migrations as $name => $callback) {
    $exists = DB::table('migrations')->where('migration', $name)->exists();
    if ($exists) {
        echo "Skipping (already ran): $name\n";
        continue;
    }

    try {
        echo "Running: $name... ";
        $callback();
        DB::table('migrations')->insert([
            'migration' => $name,
            'batch' => $batch,
        ]);
        echo "OK\n";
        $ran++;
    } catch (\Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
    }
}

echo "\nDone. Ran $ran migrations.\n";

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lentes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->foreignId('marca_id')->constrained('marcas')->cascadeOnDelete();
            $table->string('genero', 20)->default('unisex');
            $table->string('tipo_lente', 50)->default('optical');
            $table->string('tipo_montura', 50)->default('completa');
            $table->string('material', 100)->nullable();
            $table->string('color', 100)->nullable();
            $table->decimal('precio', 10, 2);
            $table->string('imagen_principal', 255)->nullable();
            $table->string('estado', 20)->default('disponible');
            $table->date('fecha_registro')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('genero');
            $table->index('precio');
            $table->index('marca_id');
            $table->index('categoria_id');
            $table->index('tipo_lente');
            $table->index('tipo_montura');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lentes');
    }
};

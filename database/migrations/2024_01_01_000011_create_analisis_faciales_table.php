<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analisis_faciales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('imagen_url', 255);
            $table->enum('forma_rostro', ['ovalado', 'redondo', 'cuadrado', 'rectangular', 'corazon', 'diamante'])->nullable();
            $table->json('puntos_referencia')->nullable();
            $table->decimal('confianza', 5, 2)->nullable();
            $table->integer('tiempo_procesamiento')->nullable()->comment('en milisegundos');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analisis_faciales');
    }
};

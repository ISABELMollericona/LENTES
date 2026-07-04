<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recomendaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('analisis_facial_id')->nullable()->constrained('analisis_faciales')->nullOnDelete();
            $table->enum('forma_rostro', ['ovalado', 'redondo', 'cuadrado', 'rectangular', 'corazon', 'diamante'])->nullable();
            $table->decimal('presupuesto_max', 10, 2)->nullable();
            $table->enum('uso_lentes', ['computadora', 'lectura', 'estudio', 'conducir', 'uso_diario', 'deportes', 'moda']);
            $table->enum('estilo', ['clasico', 'moderno', 'ejecutivo', 'deportivo', 'minimalista']);
            $table->string('color_favorito', 100)->nullable();
            $table->enum('tipo_montura', ['completa', 'semi_al_aire', 'al_aire']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recomendaciones');
    }
};

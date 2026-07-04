<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_recomendaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recomendacion_id')->constrained('recomendaciones')->cascadeOnDelete();
            $table->foreignId('lente_id')->constrained('lentes')->restrictOnDelete();
            $table->decimal('compatibilidad', 5, 2)->comment('porcentaje 0-100');
            $table->text('justificacion')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index(['recomendacion_id', 'lente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_recomendaciones');
    }
};

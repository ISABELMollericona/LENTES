<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('lente_id')->constrained('lentes')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['usuario_id', 'lente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carritos');
    }
};

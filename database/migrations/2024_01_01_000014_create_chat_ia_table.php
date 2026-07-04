<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_ia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('recomendacion_id')->nullable()->constrained('recomendaciones')->nullOnDelete();
            $table->string('sesion_id', 100);
            $table->text('mensaje');
            $table->text('respuesta')->nullable();
            $table->enum('tipo', ['usuario', 'sistema']);
            $table->timestamps();

            $table->index('sesion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_ia');
    }
};

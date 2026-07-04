<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->string('codigo', 20)->unique();
            $table->date('fecha_pedido');
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'confirmado', 'en_preparacion', 'entregado', 'cancelado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('fecha_pedido');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};

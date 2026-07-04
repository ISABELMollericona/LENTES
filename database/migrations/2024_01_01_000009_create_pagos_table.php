<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->unique()->constrained('pedidos')->cascadeOnDelete();
            $table->enum('metodo_pago', ['tarjeta_credito', 'tarjeta_debito', 'transferencia', 'efectivo']);
            $table->dateTime('fecha_pago')->nullable();
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'reembolsado'])->default('pendiente');
            $table->string('comprobante_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};

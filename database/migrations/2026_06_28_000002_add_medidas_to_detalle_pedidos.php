<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_pedidos', function (Blueprint $table) {
            // Ojo Derecho
            $table->decimal('od_esfera',  5, 2)->nullable()->after('precio_unitario');
            $table->decimal('od_cilindro', 5, 2)->nullable()->after('od_esfera');
            $table->smallInteger('od_eje')->nullable()->after('od_cilindro');
            $table->decimal('od_adicion', 5, 2)->nullable()->after('od_eje');
            // Ojo Izquierdo
            $table->decimal('oi_esfera',  5, 2)->nullable()->after('od_adicion');
            $table->decimal('oi_cilindro', 5, 2)->nullable()->after('oi_esfera');
            $table->smallInteger('oi_eje')->nullable()->after('oi_cilindro');
            $table->decimal('oi_adicion', 5, 2)->nullable()->after('oi_eje');
            // General
            $table->decimal('distancia_pupilar', 5, 1)->nullable()->after('oi_adicion');
        });
    }

    public function down(): void
    {
        Schema::table('detalle_pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'od_esfera', 'od_cilindro', 'od_eje', 'od_adicion',
                'oi_esfera', 'oi_cilindro', 'oi_eje', 'oi_adicion',
                'distancia_pupilar',
            ]);
        });
    }
};

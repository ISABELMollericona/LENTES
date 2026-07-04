<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lentes', function (Blueprint $table) {
            $table->string('dataset_origen', 255)->nullable()->after('imagen_principal');
        });

        Schema::create('imagenes_procesadas', function (Blueprint $table) {
            $table->id();
            $table->string('hash_imagen', 64)->unique();
            $table->text('ruta_original');
            $table->string('dataset_origen', 255)->nullable();
            $table->timestamp('fecha')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::table('lentes', function (Blueprint $table) {
            $table->dropColumn('dataset_origen');
        });

        Schema::dropIfExists('imagenes_procesadas');
    }
};

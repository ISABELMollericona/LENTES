<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imagenes_lentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lente_id')->constrained('lentes')->cascadeOnDelete();
            $table->string('url', 255);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imagenes_lentes');
    }
};

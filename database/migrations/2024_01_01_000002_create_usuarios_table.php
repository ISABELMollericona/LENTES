<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->default(2)->constrained('roles')->restrictOnDelete();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('email', 255)->unique();
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->string('password');
            $table->string('foto')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('estado', ['activo', 'suspendido'])->default('activo');
            $table->timestamp('ultimo_acceso')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};

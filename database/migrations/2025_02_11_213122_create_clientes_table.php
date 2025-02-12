<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            // Guardamos el nombre del usuario que creó el cliente.
            $table->string('creado_por');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

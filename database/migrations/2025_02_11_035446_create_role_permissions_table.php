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
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')
                  ->constrained() // asume que la tabla de roles se llama "roles"
                  ->cascadeOnDelete();
            // El nombre del recurso o pantalla permitido (ejemplo: "categories", "empresas", "productos", "roles", "users")
            $table->string('resource');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};

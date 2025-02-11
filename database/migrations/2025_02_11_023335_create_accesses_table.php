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
        Schema::create('accesses', function (Blueprint $table) {
            $table->id();
            // Relación con la tabla 'users'
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            // Relación con la tabla 'categories'
            $table->foreignId('category_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('accesses');
    }
};

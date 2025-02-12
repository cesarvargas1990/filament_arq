<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Empresa::insert([
            [
                'nombre_empresa' => 'Tech Solutions S.A.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_empresa' => 'Innova Global Ltda.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

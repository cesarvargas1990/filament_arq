<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'empresa_id',
        'creado_por',
    ];

    // Relación: Un cliente pertenece a una empresa.
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }
}

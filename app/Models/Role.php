<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Atributos asignables de forma masiva.
     */
    protected $fillable = [
        'nombre_rol',
        'empresa_id'
    ];

    /**
     * Relación: Un rol tiene muchos usuarios.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}

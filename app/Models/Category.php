<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_categoria',
        'empresa_id',
    ];

    /**
     * Relación: La categoría pertenece a una empresa.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: La categoría tiene muchos productos (muchos a muchos).
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product')
                    ->withTimestamps();
    }
}

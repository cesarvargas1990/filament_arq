<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use HasFactory;

    /**
     * Los atributos asignables de forma masiva.
     */
    protected $fillable = [
        'user_id',
        'category_id',
    ];

    /**
     * Relación: Un Access pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Un Access pertenece a una categoría.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

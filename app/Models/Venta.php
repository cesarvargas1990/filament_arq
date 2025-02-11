<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total'];

    // Un Venta pertenece a un Usuario.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Una Venta tiene muchos detalles.
    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }
}

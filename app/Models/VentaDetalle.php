<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    use HasFactory;

    protected $fillable = ['venta_id', 'product_id', 'price'];

    // Un detalle pertenece a una Venta.
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    // Un detalle pertenece a un Producto.
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

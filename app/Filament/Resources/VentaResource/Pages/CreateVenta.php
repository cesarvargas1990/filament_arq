<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    // Después de guardar, actualizamos el total sumando los precios de los detalles.
    protected function afterSave(): void
    {
        $venta = $this->record;
        $total = $venta->detalles()->sum('price');
        $venta->update(['total' => $total]);
    }
}

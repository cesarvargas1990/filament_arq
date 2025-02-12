<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Models\Venta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Vendedor')
                    ->searchable(),
                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('USD'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('Descargar PDF')
                    
                    ->action(fn ($record) => static::generarPDF($record->id))
                    ->requiresConfirmation()
                    ->color('primary'),
            ]);
    }

    public static function generarPDF($ventaId)
    {
        $venta = Venta::with(['user', 'cliente', 'detalles.product'])->findOrFail($ventaId);

        // Generar el PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.venta', compact('venta'));

        // Guardar el PDF temporalmente en storage
        $filePath = "ventas/venta_{$venta->id}.pdf";
        Storage::put("public/$filePath", $pdf->output());

        // Retornar la URL del archivo para descarga
        return response()->download(storage_path("app/private/public/$filePath"));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }
}

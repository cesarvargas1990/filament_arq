<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Models\Venta;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Asigna el usuario actual (oculto)
            Forms\Components\Hidden::make('user_id')
                ->default(fn () => auth()->id()),
            // Campo read-only para mostrar el total de la venta (se actualizará automáticamente)
            TextInput::make('total')
                ->label('Total')
                ->disabled()
                ->default(0),
            // Repeater para agregar los detalles de la venta.
            Repeater::make('detalles')
                ->relationship('detalles')
                ->schema([
                    // Selector para Producto. Se filtra para que sólo se muestren productos de las categorías asignadas al usuario.
                    Forms\Components\Select::make('product_id')
                        ->label('Producto')
                        ->relationship('product', 'nombre_producto', function ($query) {
                            $user = auth()->user();
                            // Si el usuario es vendedor, filtrar los productos por las categorías asignadas al usuario.
                            if ($user && $user->role->nombre_rol === 'vendedor') {
                                $categoryIds = $user->categories->pluck('id')->toArray();
                                return $query->whereHas('categories', function ($q) use ($categoryIds) {
                                    $q->whereIn('categories.id', $categoryIds);
                                });
                            }
                            return $query;
                        })
                        ->searchable()
                        ->required(),
                    // Campo para el precio del producto (por item)
                    TextInput::make('price')
                        ->label('Precio')
                        ->numeric()
                        ->required(),
                ])
                ->columns(2)
                ->default([]) // Comienza sin ítems
                ->columnSpan('full'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            // Mostrar el usuario que realizó la venta
            TextColumn::make('user.name')
                ->label('Vendedor')
                ->searchable(),
            // Mostrar el total de la venta
            TextColumn::make('total')
                ->label('Total')
                ->money('USD'),
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable(),
        ])
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit'   => Pages\EditVenta::route('/{record}/edit'),
        ];
    }
}

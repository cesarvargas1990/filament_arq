<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Models\Venta;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Campo oculto: usuario actual
            Hidden::make('user_id')
                ->default(fn () => auth()->id()),
            // Campo "Total": read-only; se calculará sumando los totales de cada detalle.
            // Se fuerza que se incluya en el formulario (dehydrated true) para que se guarde el valor.
            TextInput::make('total')
                ->label('Total')
                ->disabled()
                ->dehydrated(true)
                ->default(0)
                ->reactive()
                ->afterStateHydrated(function ($state, callable $set, callable $get) {
                    $detalles = $get('detalles') ?? [];
                    $sum = 0;
                    foreach ($detalles as $item) {
                        if (!empty($item['product_id']) && !empty($item['cantidad'])) {
                            $product = \App\Models\Product::find($item['product_id']);
                            if ($product) {
                                $sum += $product->precio * $item['cantidad'];
                            }
                        }
                    }
                    $set('total', $sum);
                })
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $detalles = $get('detalles') ?? [];
                    $sum = 0;
                    foreach ($detalles as $item) {
                        if (!empty($item['product_id']) && !empty($item['cantidad'])) {
                            $product = \App\Models\Product::find($item['product_id']);
                            if ($product) {
                                $sum += $product->precio * $item['cantidad'];
                            }
                        }
                    }
                    $set('total', $sum);
                }),
            // Repeater para los detalles de la venta
            Repeater::make('detalles')
                ->relationship('detalles')
                ->schema([
                    // Selector para Producto.
                    Select::make('product_id')
                        ->label('Producto')
                        ->relationship('product', 'nombre_producto', function ($query) {
                            $user = auth()->user();
                            if ($user && $user->role->nombre_rol === 'vendedor') {
                                $categoryIds = $user->categories->pluck('id')->toArray();
                                return $query->whereHas('categories', function ($q) use ($categoryIds) {
                                    $q->whereIn('categories.id', $categoryIds);
                                });
                            }
                            return $query;
                        })
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (callable $get, callable $set, $state) {
                            if ($state) {
                                $product = \App\Models\Product::find($state);
                                if ($product) {
                                    $set('price', $product->precio);
                                }
                            }
                        }),
                    // Campo para la Cantidad
                    TextInput::make('cantidad')
                        ->label('Cantidad')
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->reactive(),
                ])
                ->columns(2)
                ->default([]) // Comienza sin ítems
                ->columnSpan('full')
                ->reactive()
                ->afterStateUpdated(function (callable $get, callable $set, $state) {
                    $detalles = $get('detalles') ?? [];
                    $sum = 0;
                    foreach ($detalles as $item) {
                        if (!empty($item['product_id']) && !empty($item['cantidad'])) {
                            $product = \App\Models\Product::find($item['product_id']);
                            if ($product) {
                                $sum += $product->precio * $item['cantidad'];
                            }
                        }
                    }
                    $set('total', $sum);
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            // Muestra el usuario (vendedor) que realizó la venta
            TextColumn::make('user.name')
                ->label('Vendedor')
                ->searchable(),
            // Muestra el total de la venta
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

    // Filtra la consulta para que solo se muestren las ventas del usuario actual.
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

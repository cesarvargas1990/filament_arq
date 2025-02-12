<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;


class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Campo oculto: asigna el usuario actual.
            Hidden::make('user_id')
                ->default(fn () => auth()->id()),
            // Campo para seleccionar Cliente, con opción de crear uno nuevo mediante modal.
            Select::make('cliente_id')
                ->label('Cliente')
                ->relationship('cliente', 'nombre')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('nombre')
                        ->label('Nombre del Cliente')
                        ->required(),
                    TextInput::make('direccion')
                        ->label('Dirección'),
                    TextInput::make('telefono')
                        ->label('Teléfono'),
                ])
                ->createOptionAction(function ($state = null) {
                    // Aseguramos que $state sea un array
                    $state = $state ?? [];
                    // Si no se proporcionó un nombre, no se crea el cliente (la validación del formulario debería impedirlo)
                    if (empty($state['nombre'])) {
                        return null;
                    }
                    // Asignamos automáticamente la empresa del usuario actual y el nombre del usuario creador.
                    $state['empresa_id'] = auth()->user()->empresa_id;
                    $state['creado_por'] = auth()->user()->name;
                    $cliente = \App\Models\Cliente::create($state);
                    return $cliente->id;
                }),
            // Campo "Total": read-only; se calculará sumando los totales de cada detalle.
            TextInput::make('total')
                ->label('Total')
                ->disabled()
                ->dehydrated(true) // Se fuerza que se incluya en la solicitud
                ->default(0)
                ->reactive()
                ->afterStateHydrated(function ($state, callable $set, callable $get) {
                    if ($state && $state > 0) {
                        return;
                    }
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
            // Repeater para los detalles de la venta.
            Repeater::make('detalles')
                ->relationship('detalles')
                ->schema([
                    // Selector para Producto.
                    Select::make('product_id')
                        ->label('Producto')
                        ->relationship('product', 'nombre_producto', function ($query) {
                            $user = Auth::user();
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
                        ->preload()
                        ->afterStateUpdated(function (callable $get, callable $set, $state) {
                            if ($state) {
                                $product = \App\Models\Product::find($state);
                                if ($product) {
                                    // Opcional: se podría mostrar el precio, pero en este ejemplo el total se calcula del precio del producto.
                                }
                            }
                        }),
                    // Campo para la Cantidad.
                    TextInput::make('cantidad')
                        ->label('Cantidad')
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->reactive(),
                ])
                ->columns(2)
                ->default([]) // Comienza sin ítems.
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
                    ->icon('heroicon-o-arrow-down-tray')
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
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function getRelations(): array
    {
        return [
            // Puedes agregar RelationManagers si lo necesitas.
        ];
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

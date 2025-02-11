<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            // Campo para el nombre del producto.
            Forms\Components\TextInput::make('nombre_producto')
                ->required(),
            // Campo para el precio.
            Forms\Components\TextInput::make('precio')
                ->required()
                ->numeric(),
            // Selector múltiple para asignar categorías al producto.
            Forms\Components\Select::make('categories')
                ->label('Categorías')
                ->relationship('categories', 'nombre_categoria')
                ->multiple()      // Permite seleccionar más de una categoría
                ->searchable()    // Permite búsqueda en la lista de categorías
                ->preload(),      // Opcional: carga las opciones al inicio
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            // Columna para el nombre del producto.
            Tables\Columns\TextColumn::make('nombre_producto')
                ->searchable(),
            // Columna para el precio.
            Tables\Columns\TextColumn::make('precio')
                ->numeric()
                ->sortable(),
            // Columna para mostrar las categorías asociadas, usando TagsColumn.
            TagsColumn::make('categories.nombre_categoria')
                ->label('Categorías')
                ->separator(', '), // Opcional: separa cada etiqueta con una coma
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::check() && Auth::user()->role->nombre_rol === 'vendedor') {
            // Obtén los IDs de las categorías asignadas al usuario (vendedor)
            $categoryIds = Auth::user()->categories->pluck('id')->toArray();

            $query->whereHas('categories', function ($q) use ($categoryIds) {
                // Especifica el nombre de la tabla "categories" para evitar ambigüedades
                $q->whereIn('categories.id', $categoryIds);
            });
        }

        return $query;
    }


   
}

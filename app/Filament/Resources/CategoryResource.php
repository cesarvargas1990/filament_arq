<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            // Campo para el nombre de la categoría.
            TextInput::make('nombre_categoria')
                ->label('Categoría')
                ->required(),

            // Selector para elegir la empresa asociada a la categoría.
            Select::make('empresa_id')
                ->label('Empresa')
                ->relationship('empresa', 'nombre_empresa')
                ->searchable()
                ->required(),

            // Repeater para agregar productos desde la misma pantalla.
            Repeater::make('products')
                ->relationship('products')
                ->schema([
                    // Campo para el nombre del producto.
                    TextInput::make('nombre_producto')
                        ->label('Nombre del Producto')
                        ->required(),
                    // Campo para el precio del producto.
                    TextInput::make('precio')
                        ->label('Precio')
                        ->numeric()
                        ->required(),
                    // Campo oculto para asignar la misma empresa que la categoría.
                    Hidden::make('empresa_id')
                        ->default(fn (callable $get) => $get('empresa_id'))
                ])
                ->columns(2)
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_categoria')
                    ->label('Categoría')
                    ->searchable(),
                TextColumn::make('empresa.nombre_empresa')
                    ->label('Empresa')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }


    public static function canAccess(): bool
    {
        return Auth::user()->role->hasPermission('categories');
    }
    
}

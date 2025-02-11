<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccessResource\Pages;
use App\Models\Access;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AccessResource extends Resource
{
    protected static ?string $model = Access::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Campo para seleccionar el usuario (buscable)
            Forms\Components\Select::make('user_id')
                ->label('Usuario')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),

            // Campo para seleccionar una categoría (simple)
            Forms\Components\Select::make('category_id')
                ->label('Categoría')
                ->relationship('category', 'nombre_categoria')
                ->searchable() // opcional, para facilitar la búsqueda
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            // Muestra el nombre del usuario a través de la relación
            Tables\Columns\TextColumn::make('user.name')
                ->label('Usuario')
                ->searchable(),
            // Muestra el nombre de la categoría a través de la relación
            Tables\Columns\TextColumn::make('category.nombre_categoria')
                ->label('Categoría')
                ->searchable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([])
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
            // Aquí podrías definir RelationManagers si necesitas gestionar relaciones adicionales
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAccesses::route('/'),
            'create' => Pages\CreateAccess::route('/create'),
            'edit'   => Pages\EditAccess::route('/{record}/edit'),
        ];
    }
}

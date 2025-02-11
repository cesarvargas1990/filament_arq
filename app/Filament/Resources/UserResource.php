<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Nombre
                Forms\Components\TextInput::make('name')
                    ->required(),
                // Correo electrónico
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                // Empresa
                Forms\Components\Select::make('empresa_id')
                    ->label('Empresa')
                    ->relationship('empresa', 'nombre_empresa')
                    ->searchable()
                    ->required(),
                // Rol
                Forms\Components\Select::make('role_id')
                    ->label('Rol')
                    ->relationship('role', 'nombre_rol')
                    ->searchable()
                    ->required(),
                // Fecha de verificación de email
                Forms\Components\DateTimePicker::make('email_verified_at'),
                // Contraseña: visible solo en creación
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->visible(fn(string $context): bool => $context === 'create')
                    ->required(fn($context) => $context === 'create')
                    ->helperText('El password se establece al crear el usuario.'),
                // Asignación de categorías (relación many‑to‑many)
                Forms\Components\Select::make('categories')
                    ->label('Categorías de acceso')
                    ->relationship('categories', 'nombre_categoria')
                    ->multiple()       // Permite seleccionar varias categorías
                    ->searchable()     // Permite búsqueda en la lista de categorías
                    ->preload(),       // Opcional: carga las opciones de inmediato
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('email')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('empresa.nombre_empresa')
                ->label('Empresa')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('role.nombre_rol')
                ->label('Rol')
                ->searchable()
                ->sortable(),
            // Columna para mostrar las categorías asignadas
            Tables\Columns\TagsColumn::make('categories.nombre_categoria')
                ->label('Categorías de acceso')
                ->separator(', '),
            Tables\Columns\TextColumn::make('email_verified_at')
                ->dateTime()
                ->sortable(),
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
            // Puedes agregar RelationManagers aquí si lo deseas.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('azure_id')
                    ->label('Azure ID')
                    ->disabled(),

                TextInput::make('user_type')
                    ->label('Tipo de Usuario'),

                // --- CAMPO CORREGIDO ---
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple() // Esto ya permite seleccionar varios y los muestra como bloques
                    ->preload()
                    ->searchable(),

                Toggle::make('is_active')
                    ->label('¿Está activo?')
                    ->required()
                    ->default(true),
            ]);
    }
}

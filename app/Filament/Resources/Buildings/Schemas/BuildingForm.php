<?php

namespace App\Filament\Resources\Buildings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BuildingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('campus_id')
                    ->relationship('campus', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
            ]);
    }
}

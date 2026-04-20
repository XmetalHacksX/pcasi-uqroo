<?php

namespace App\Filament\Resources\Campuses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CampusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}

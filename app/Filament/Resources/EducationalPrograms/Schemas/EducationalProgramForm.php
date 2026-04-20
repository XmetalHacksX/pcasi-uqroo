<?php

namespace App\Filament\Resources\EducationalPrograms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class EducationalProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('academic_division_id')
                    ->label('División Académica')
                    ->relationship(
                        name: 'academicDivision',
                        titleAttribute: 'name',
                        // Traemos el nombre del campus para diferenciar las divisiones repetidas
                        modifyQueryUsing: fn(Builder $query) => $query
                            ->join('campuses', 'academic_divisions.campus_id', '=', 'campuses.id')
                            ->select('academic_divisions.*', 'campuses.name as campus_name')
                    )
                    // Formateamos la opción: "Nombre de División - Nombre de Campus"
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} - {$record->campus_name}")
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name')
                    ->label('Nombre del Programa')
                    ->required(),
            ]);
    }
}

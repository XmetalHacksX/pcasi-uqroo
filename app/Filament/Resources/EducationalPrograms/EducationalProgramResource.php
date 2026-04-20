<?php

namespace App\Filament\Resources\EducationalPrograms;

use App\Filament\Resources\EducationalPrograms\Pages\CreateEducationalProgram;
use App\Filament\Resources\EducationalPrograms\Pages\EditEducationalProgram;
use App\Filament\Resources\EducationalPrograms\Pages\ListEducationalPrograms;
use App\Filament\Resources\EducationalPrograms\Schemas\EducationalProgramForm;
use App\Filament\Resources\EducationalPrograms\Tables\EducationalProgramsTable;
use App\Models\EducationalProgram;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EducationalProgramResource extends Resource
{
    protected static ?string $model = EducationalProgram::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Catálogos Institucionales';
    protected static ?string $modelLabel = 'Programa Educativo';
    protected static ?string $pluralModelLabel = 'Programas Educativos';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return EducationalProgramForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EducationalProgramsTable::configure($table);
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
            'index' => ListEducationalPrograms::route('/'),
            'create' => CreateEducationalProgram::route('/create'),
            'edit' => EditEducationalProgram::route('/{record}/edit'),
        ];
    }
}

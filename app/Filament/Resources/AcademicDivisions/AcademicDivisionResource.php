<?php

namespace App\Filament\Resources\AcademicDivisions;

use App\Filament\Resources\AcademicDivisions\Pages\CreateAcademicDivision;
use App\Filament\Resources\AcademicDivisions\Pages\EditAcademicDivision;
use App\Filament\Resources\AcademicDivisions\Pages\ListAcademicDivisions;
use App\Filament\Resources\AcademicDivisions\Schemas\AcademicDivisionForm;
use App\Filament\Resources\AcademicDivisions\Tables\AcademicDivisionsTable;
use App\Models\AcademicDivision;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AcademicDivisionResource extends Resource
{
    protected static ?string $model = AcademicDivision::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $recordTitleAttribute = 'name';

    // 👇 CONFIGURACIÓN DE MENÚ Y TRADUCCIÓN 👇
    protected static string|\UnitEnum|null $navigationGroup = 'Catálogos Institucionales';
    protected static ?string $modelLabel = 'División Académica';
    protected static ?string $pluralModelLabel = 'Divisiones Académicas';
    protected static ?int $navigationSort = 2;
    // 👆 FIN DE CONFIGURACIÓN 👆

    public static function form(Schema $schema): Schema
    {
        return AcademicDivisionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicDivisionsTable::configure($table);
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
            'index' => ListAcademicDivisions::route('/'),
            'create' => CreateAcademicDivision::route('/create'),
            'edit' => EditAcademicDivision::route('/{record}/edit'),
        ];
    }
}

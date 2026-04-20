<?php

namespace App\Filament\Resources\Subdepartments;

use App\Filament\Resources\Subdepartments\Pages\CreateSubdepartment;
use App\Filament\Resources\Subdepartments\Pages\EditSubdepartment;
use App\Filament\Resources\Subdepartments\Pages\ListSubdepartments;
use App\Filament\Resources\Subdepartments\Schemas\SubdepartmentForm;
use App\Filament\Resources\Subdepartments\Tables\SubdepartmentsTable;
use App\Models\Subdepartment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubdepartmentResource extends Resource
{
    protected static ?string $model = Subdepartment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Catálogos Institucionales';
    protected static ?string $modelLabel = 'Subdepartamento';
    protected static ?string $pluralModelLabel = 'Subdepartamentos';
    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SubdepartmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubdepartmentsTable::configure($table);
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
            'index' => ListSubdepartments::route('/'),
            'create' => CreateSubdepartment::route('/create'),
            'edit' => EditSubdepartment::route('/{record}/edit'),
        ];
    }
}

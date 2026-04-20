<?php

namespace App\Filament\Resources\AcademicDivisions\Pages;

use App\Filament\Resources\AcademicDivisions\AcademicDivisionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAcademicDivisions extends ListRecords
{
    protected static string $resource = AcademicDivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

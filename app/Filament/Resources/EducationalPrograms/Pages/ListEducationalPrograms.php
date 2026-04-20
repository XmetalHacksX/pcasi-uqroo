<?php

namespace App\Filament\Resources\EducationalPrograms\Pages;

use App\Filament\Resources\EducationalPrograms\EducationalProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEducationalPrograms extends ListRecords
{
    protected static string $resource = EducationalProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

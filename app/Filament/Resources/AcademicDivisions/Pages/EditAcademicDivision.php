<?php

namespace App\Filament\Resources\AcademicDivisions\Pages;

use App\Filament\Resources\AcademicDivisions\AcademicDivisionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAcademicDivision extends EditRecord
{
    protected static string $resource = AcademicDivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

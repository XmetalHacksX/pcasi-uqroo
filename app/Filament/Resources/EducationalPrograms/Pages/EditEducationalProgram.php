<?php

namespace App\Filament\Resources\EducationalPrograms\Pages;

use App\Filament\Resources\EducationalPrograms\EducationalProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEducationalProgram extends EditRecord
{
    protected static string $resource = EducationalProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Subdepartments\Pages;

use App\Filament\Resources\Subdepartments\SubdepartmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubdepartment extends EditRecord
{
    protected static string $resource = SubdepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

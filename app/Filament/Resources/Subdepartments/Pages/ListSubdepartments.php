<?php

namespace App\Filament\Resources\Subdepartments\Pages;

use App\Filament\Resources\Subdepartments\SubdepartmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubdepartments extends ListRecords
{
    protected static string $resource = SubdepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

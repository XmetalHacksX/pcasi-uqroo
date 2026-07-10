<?php

namespace App\Filament\Resources\DynamicFormResource\Pages;

use App\Filament\Resources\DynamicFormResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDynamicForms extends ListRecords
{
    protected static string $resource = DynamicFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

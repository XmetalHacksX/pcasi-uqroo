<?php

namespace App\Filament\Resources\DynamicFormResource\Pages;

use App\Filament\Resources\DynamicFormResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDynamicForm extends EditRecord
{
    protected static string $resource = DynamicFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

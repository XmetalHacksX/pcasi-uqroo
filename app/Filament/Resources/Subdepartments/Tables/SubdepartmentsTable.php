<?php

namespace App\Filament\Resources\Subdepartments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SubdepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('department.name')
                    ->label('Dirección General')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Oficina / Subdepartamento')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // Aquí puedes agregar filtros después si los necesitas
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\EducationalPrograms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EducationalProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Solo dejamos una columna y le ponemos etiqueta en español
                TextColumn::make('academicDivision.name')
                    ->label('División Académica')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nombre del Programa')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([ // Corregido: en tablas se usa bulkActions para el BulkActionGroup
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

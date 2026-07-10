<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;

class TicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── SECCIÓN 1: DATOS GENERALES ──
                Section::make('Información del Ticket')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('folio')
                                ->weight('bold')
                                ->color('primary'),

                            TextEntry::make('reporter.name')
                                ->label('Reportado por')
                                ->icon('heroicon-m-user'),

                            TextEntry::make('ticket_group')
                                ->label('Área')
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    'SGC'             => 'warning',
                                    'GENERO'          => 'danger',
                                    'INFRAESTRUCTURA' => 'info',
                                    default           => 'gray',
                                }),

                            TextEntry::make('status.name')
                                ->label('Estatus actual')
                                ->badge(),

                            TextEntry::make('assignedTo.name')
                                ->label('Atendido por')
                                ->default('Aún sin asignar')
                                ->icon('heroicon-m-wrench-screwdriver'),

                            TextEntry::make('created_at')
                                ->label('Fecha de reporte')
                                ->dateTime('d/m/Y h:i A'),
                        ])
                    ]),

                // ── SECCIÓN 2: DETALLES SGC ──
                Section::make('Detalles del Reporte SGC')
                    ->visible(fn($record) => $record->ticket_group === 'SGC' && $record->ticketSgcDetail !== null)
                    ->schema(fn($record) => self::buildInfolistFieldsForGroup('SGC', 'ticketSgcDetail')),

                // ── SECCIÓN 3: DETALLES INFRAESTRUCTURA ──
                Section::make('Detalles de Infraestructura')
                    ->visible(fn($record) => $record->ticket_group === 'INFRAESTRUCTURA' && $record->ticketInfraDetail !== null)
                    ->schema(fn($record) => self::buildInfolistFieldsForGroup('INFRAESTRUCTURA', 'ticketInfraDetail')),

                // ── SECCIÓN 4: DETALLES GÉNERO (Completo) ──
                Section::make('Reporte Confidencial de Género')
                    ->visible(fn($record) => $record->ticket_group === 'GENERO' && $record->ticketGenderDetail !== null)
                    ->schema(fn($record) => self::buildInfolistFieldsForGroup('GENERO', 'ticketGenderDetail')),
            ]);
    }

    protected static function buildInfolistFieldsForGroup(string $groupName, string $relation): array
    {
        $form = \App\Models\DynamicForm::where('name', $groupName)->first();
        if (!$form) {
            return [];
        }

        $components = [];

        // Obtener todos los campos activos de este formulario
        $fields = \App\Models\DynamicFormField::whereHas('step', function ($q) use ($form) {
                $q->where('dynamic_form_id', $form->id);
            })
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $gridFields = [];

        foreach ($fields as $field) {
            $path = self::getInfolistFieldPath($relation, $field);

            // Manejar campos dinámicos booleanos
            if ($field->type === 'toggle') {
                // Agregar grid acumulado antes de un toggle/iconentry
                if (!empty($gridFields)) {
                    $components[] = Grid::make(3)->schema($gridFields);
                    $gridFields = [];
                }
                $components[] = IconEntry::make($path)
                    ->label($field->label)
                    ->boolean();
                continue;
            }

            $entry = TextEntry::make($path)
                ->label($field->label)
                ->default('No especificado');

            // Formatear select estático
            if (in_array($field->type, ['select', 'select_multiple'])) {
                if ($field->options) {
                    $entry->formatStateUsing(function ($state) use ($field) {
                        if (is_array($state)) {
                            return collect($state)->map(fn($v) => $field->options[$v] ?? $v)->implode(', ');
                        }
                        return $field->options[$state] ?? $state;
                    });
                }
                if ($field->type === 'select_multiple') {
                    $entry->badge();
                }
            } elseif ($field->type === 'select_user_type') {
                $entry->badge()
                    ->formatStateUsing(fn(?string $state): string => match($state) {
                        'administrativo' => 'Personal Administrativo',
                        'academico'      => 'Personal Académico',
                        'estudiante'     => 'Estudiante',
                        default          => 'No especificado',
                    });
            }

            // Manejar texto largo (Textareas)
            if ($field->type === 'textarea') {
                if (!empty($gridFields)) {
                    $components[] = Grid::make(3)->schema($gridFields);
                    $gridFields = [];
                }
                $entry->columnSpanFull();
                $components[] = $entry;
            } else {
                $gridFields[] = $entry;
            }
        }

        if (!empty($gridFields)) {
            $components[] = Grid::make(3)->schema($gridFields);
        }

        return $components;
    }

    protected static function getInfolistFieldPath(string $relation, \App\Models\DynamicFormField $field): string
    {
        $name = $field->name;

        // Columnas legadas de la BD
        $sgcColumns = ['reported_person_name', 'user_type', 'department_id', 'subdepartment_id', 'academic_division_id', 'educational_program_id', 'classification', 'description'];
        $generoColumns = ['reported_person_name', 'reported_person_type', 'campus_id', 'department_id', 'subdepartment_id', 'academic_division_id', 'educational_program_id', 'reported_person_details', 'manifestation_type', 'chronological_narrative', 'extended_narrative', 'has_evidence', 'witnesses_details', 'needs_psychological_support', 'communicated_to', 'communication_results'];
        $infraColumns = ['campus_id', 'building_id', 'location_id', 'issue_type', 'missing_supplies', 'description'];

        $isLegacy = false;
        if ($relation === 'ticketSgcDetail' && in_array($name, $sgcColumns)) $isLegacy = true;
        if ($relation === 'ticketGenderDetail' && in_array($name, $generoColumns)) $isLegacy = true;
        if ($relation === 'ticketInfraDetail' && in_array($name, $infraColumns)) $isLegacy = true;

        if ($isLegacy) {
            if (str_ends_with($name, '_id')) {
                $relName = match ($name) {
                    'campus_id' => 'campus',
                    'building_id' => 'building',
                    'location_id' => 'location',
                    'department_id' => 'department',
                    'subdepartment_id' => 'subdepartment',
                    'academic_division_id' => 'academicDivision',
                    'educational_program_id' => 'educationalProgram',
                    default => null,
                };
                if ($relName) {
                    return "{$relation}.{$relName}.name";
                }
            }
            return "{$relation}.{$name}";
        }

        // Es un campo dinámico extra
        return "{$relation}.extra_attributes.{$name}";
    }
}

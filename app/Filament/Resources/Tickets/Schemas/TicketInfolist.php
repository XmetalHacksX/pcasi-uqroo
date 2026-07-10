<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry; // Añadido para los booleanos (checkboxes)
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
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('ticketSgcDetail.classification')
                                ->label('Clasificación')
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    'Queja' => 'danger',
                                    'Sugerencia' => 'info',
                                    'Felicitación' => 'success',
                                    default => 'gray',
                                }),

                            TextEntry::make('ticketSgcDetail.reported_person_name')
                                ->label('Persona involucrada')
                                ->default('No especificada'),

                            TextEntry::make('ticketSgcDetail.user_type')
                                ->label('Tipo de Usuario')
                                ->badge()
                                ->color('gray')
                                ->formatStateUsing(fn(?string $state): string => match($state) {
                                    'administrativo' => 'Personal Administrativo',
                                    'academico'      => 'Personal Académico',
                                    'estudiante'     => 'Estudiante',
                                    default          => 'No especificado',
                                }),
                        ]),

                        // --- NUEVO BLOQUE: ADSCRIPCIÓN DINÁMICA SGC ---
                        // Flujo Administrativo
                        Grid::make(3)
                            ->visible(fn($record) => $record->ticketSgcDetail?->user_type === 'administrativo')
                            ->schema([
                                TextEntry::make('ticketSgcDetail.department.campus.name')
                                    ->label('Campus'),
                                TextEntry::make('ticketSgcDetail.department.name')
                                    ->label('Dirección General'),
                                TextEntry::make('ticketSgcDetail.subdepartment.name')
                                    ->label('Departamento / Oficina')
                                    ->default('No especificado'),
                            ]),

                        // Flujo Académico
                        Grid::make(3)
                            ->visible(fn($record) => $record->ticketSgcDetail?->user_type === 'academico')
                            ->schema([
                                TextEntry::make('ticketSgcDetail.academicDivision.campus.name')
                                    ->label('Campus'),
                                TextEntry::make('ticketSgcDetail.academicDivision.name')
                                    ->label('División Académica'),
                                TextEntry::make('ticketSgcDetail.subdepartment.name')
                                    ->label('Departamento Académico')
                                    ->default('No especificado'),
                            ]),

                        // Flujo Estudiante
                        Grid::make(2)
                            ->visible(fn($record) => $record->ticketSgcDetail?->user_type === 'estudiante')
                            ->schema([
                                TextEntry::make('ticketSgcDetail.academicDivision.campus.name')
                                    ->label('Campus'),
                                TextEntry::make('ticketSgcDetail.educationalProgram.name')
                                    ->label('Carrera / Programa Educativo'),
                            ]),
                        // ----------------------------------------------

                        TextEntry::make('ticketSgcDetail.description')
                            ->label('Descripción del asunto')
                            ->columnSpanFull(),
                    ]),

                // ── SECCIÓN 3: DETALLES INFRAESTRUCTURA ──
                Section::make('Detalles de Infraestructura')
                    ->visible(fn($record) => $record->ticket_group === 'INFRAESTRUCTURA' && $record->ticketInfraDetail !== null)
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('ticketInfraDetail.campus.name')
                                ->label('Campus'),

                            TextEntry::make('ticketInfraDetail.building.name')
                                ->label('Edificio'),

                            TextEntry::make('ticketInfraDetail.location.name')
                                ->label('Ubicación exacta')
                                ->default('No especificada'),

                            TextEntry::make('ticketInfraDetail.issue_type')
                                ->label('Tipo de problema')
                                ->badge(),
                        ]),

                        TextEntry::make('ticketInfraDetail.description')
                            ->label('Descripción del problema')
                            ->columnSpanFull(),

                        // Añadimos el JSON de supplies si aplica
                        TextEntry::make('ticketInfraDetail.missing_supplies')
                            ->label('Insumos faltantes')
                            ->badge() // Si es un array (JSON), lo mostrará como etiquetas
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->ticketInfraDetail?->issue_type === 'Limpieza y Sanitarios'),
                    ]),

                // ── SECCIÓN 4: DETALLES GÉNERO (Completo) ──
                Section::make('Reporte Confidencial de Género')
                    ->visible(fn($record) => $record->ticket_group === 'GENERO' && $record->ticketGenderDetail !== null)
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('ticketGenderDetail.manifestation_type')
                                ->label('Tipo de Manifestación')
                                ->badge()
                                ->color('danger'),

                            TextEntry::make('ticketGenderDetail.reported_person_name')
                                ->label('Persona reportada')
                                ->default('No especificada'),

                            TextEntry::make('ticketGenderDetail.reported_person_type')
                                ->label('Rol de la persona reportada')
                                ->default('No especificado'),
                        ]),

                        // --- NUEVO BLOQUE: ADSCRIPCIÓN DINÁMICA ---
                        // Flujo Administrativo
                        Grid::make(3)
                            ->visible(fn($record) => $record->ticketGenderDetail?->reported_person_type === 'administrativo')
                            ->schema([
                                TextEntry::make('ticketGenderDetail.department.campus.name')
                                    ->label('Campus'),
                                TextEntry::make('ticketGenderDetail.department.name')
                                    ->label('Dirección General / Área'),
                                TextEntry::make('ticketGenderDetail.subdepartment.name')
                                    ->label('Oficina / Subdepartamento')
                                    ->default('No especificada'),
                            ]),

                        // Flujo Académico
                        Grid::make(3)
                            ->visible(fn($record) => $record->ticketGenderDetail?->reported_person_type === 'academico')
                            ->schema([
                                TextEntry::make('ticketGenderDetail.academicDivision.campus.name')
                                    ->label('Campus'),
                                TextEntry::make('ticketGenderDetail.academicDivision.name')
                                    ->label('División Académica'),
                                TextEntry::make('ticketGenderDetail.educationalProgram.name')
                                    ->label('Programa Educativo'),
                            ]),

                        // Flujo Externo
                        Grid::make(1)
                            ->visible(fn($record) => $record->ticketGenderDetail?->reported_person_type === 'Externo')
                            ->schema([
                                TextEntry::make('ticketGenderDetail.campus.name')
                                    ->label('Campus'),
                            ]),

                        // Dato Extra Institucional (Aplica a ambos si lo llenan)
                        TextEntry::make('ticketGenderDetail.reported_person_details')
                            ->label('Cargo o detalles específicos')
                            ->columnSpanFull()
                            ->visible(fn($record) => filled($record->ticketGenderDetail?->reported_person_details)),
                        // ------------------------------------------

                        TextEntry::make('ticketGenderDetail.chronological_narrative')
                            ->label('Narrativa de los hechos (Cronológica)')
                            ->columnSpanFull(),

                        TextEntry::make('ticketGenderDetail.extended_narrative')
                            ->label('Narrativa Extendida / Contexto Adicional')
                            ->columnSpanFull()
                            ->default('No se proporcionó información adicional.'),

                        // Bloque de Evidencia y Testigos
                        Grid::make(2)->schema([
                            IconEntry::make('ticketGenderDetail.has_evidence')
                                ->label('¿Cuenta con evidencia física/digital?')
                                ->boolean(),

                            TextEntry::make('ticketGenderDetail.witnesses_details')
                                ->label('Detalles de testigos')
                                ->default('Sin testigos reportados.'),
                        ]),

                        // Bloque de Apoyo Psicológico y Comunicación Institucional
                        Grid::make(3)->schema([
                            IconEntry::make('ticketGenderDetail.needs_psychological_support')
                                ->label('¿Requiere apoyo psicológico?')
                                ->boolean(),

                            TextEntry::make('ticketGenderDetail.communicated_to')
                                ->label('¿Se comunicó a alguna autoridad?')
                                ->badge(),
                        ]),

                        TextEntry::make('ticketGenderDetail.communication_results')
                            ->label('Resultados de esa comunicación')
                            ->columnSpanFull()
                            ->default('No se registraron resultados de comunicación previa.'),
                    ]),
            ]);
    }
}

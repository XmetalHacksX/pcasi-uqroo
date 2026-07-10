<?php

namespace App\Filament\Resources\Tickets\Tables;

use App\Enums\RolesEnum;
use App\Enums\StatusEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('folio')
                    ->label('Folio')
                    ->searchable(),

                TextColumn::make('reporter.name')
                    ->label('Reportado por')
                    ->searchable(),

                TextColumn::make('ticket_group')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'SGC'             => 'warning',
                        'GENERO'          => 'danger',
                        'INFRAESTRUCTURA' => 'info',
                        default           => 'gray',
                    }),

                TextColumn::make('status.name')
                    ->label('Estatus')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Última actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                // --- BOTÓN 1: ATENDER (Pasa a estado 2) ---
                Action::make('tomar_ticket')
                    ->label('Atender')
                    ->icon('heroicon-m-hand-raised')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('¿Atender este ticket?')
                    ->modalDescription('El ticket será asignado a ti y su estado cambiará a "Asignado".')
                    ->visible(
                        fn($record) =>
                        is_null($record->assigned_to_id) &&
                            auth()->user()->hasAnyRole([
                                RolesEnum::SUPER_ADMIN->value, 
                                RolesEnum::ADMIN->value, 
                                RolesEnum::RESPONSABLE_SGC->value, 
                                RolesEnum::RESPONSABLE_GENERO->value, 
                                RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value
                            ])
                    )
                    ->action(function ($record) {
                        $record->update([
                            'assigned_to_id' => auth()->id(),
                            'status_id'      => StatusEnum::ASIGNADO->id(),
                        ]);
                    }),

                // --- BOTÓN 2: INICIAR PROCESO (Pasa a estado 3) ---
                Action::make('iniciar_proceso')
                    ->label('Iniciar Proceso')
                    ->icon('heroicon-m-play-circle')
                    ->color('info') // Azul para "En Proceso"
                    ->requiresConfirmation()
                    ->modalHeading('¿Iniciar atención del ticket?')
                    ->modalDescription('El estatus cambiará a "En Proceso" indicando que ya estás trabajando en la solución.')
                    ->visible(
                        fn($record) =>
                        !is_null($record->assigned_to_id) &&
                            ($record->assigned_to_id === auth()->id() || auth()->user()->hasAnyRole([RolesEnum::SUPER_ADMIN->value, RolesEnum::ADMIN->value])) &&
                            $record->status_id === StatusEnum::ASIGNADO->id()
                    )
                    ->action(function ($record) {
                        $record->update([
                            'status_id' => StatusEnum::EN_PROCESO->id(),
                        ]);
                    }),

                // --- BOTÓN 3: RESOLVER (Pasa a estado 4) ---
                Action::make('resolver_ticket')
                    ->label('Resolver')
                    ->icon('heroicon-m-check-circle')
                    ->color('success') // Verde para "Resuelto"
                    ->requiresConfirmation()
                    ->modalHeading('¿Marcar como Resuelto?')
                    ->modalDescription('El ticket se marcará como resuelto y pasará a tu historial.')
                    ->visible(
                        fn($record) =>
                        !is_null($record->assigned_to_id) &&
                            ($record->assigned_to_id === auth()->id() || auth()->user()->hasAnyRole([RolesEnum::SUPER_ADMIN->value, RolesEnum::ADMIN->value])) &&
                            in_array($record->status_id, [StatusEnum::ASIGNADO->id(), StatusEnum::EN_PROCESO->id()])
                    )
                    ->action(function ($record) {
                        $record->update([
                            'status_id' => StatusEnum::RESUELTO->id(),
                        ]);
                    }),

                // --- BOTÓN 4: DESCARGAR PDF ---
                Action::make('descargar_pdf')
                    ->label('PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('gray')
                    ->visible(
                        fn() =>
                        auth()->user()->hasAnyRole([
                            RolesEnum::SUPER_ADMIN->value,
                            RolesEnum::ADMIN->value,
                            RolesEnum::RESPONSABLE_SGC->value,
                            RolesEnum::RESPONSABLE_GENERO->value,
                            RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value
                        ])
                    )
                    ->action(function ($record) {
                        return response()->streamDownload(function () use ($record) {
                            echo \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', ['ticket' => $record])->output();
                        }, 'Ticket_' . $record->folio . '.pdf');
                    }),

                // --- BOTÓN 5: ENVIAR POR CORREO ---
                Action::make('enviar_por_correo')
                    ->label('Enviar Email')
                    ->icon('heroicon-m-envelope')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('¿Enviar comprobante PDF por correo?')
                    ->modalDescription('Se generará un PDF de inmediato y se despachará al correo de la víctima / reportador de este folio.')
                    ->visible(
                        fn() =>
                        auth()->user()->hasAnyRole([
                            RolesEnum::SUPER_ADMIN->value,
                            RolesEnum::ADMIN->value,
                            RolesEnum::RESPONSABLE_SGC->value,
                            RolesEnum::RESPONSABLE_GENERO->value,
                            RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value
                        ])
                    )
                    ->action(function ($record) {
                        if ($record->reporter?->email) {
                            \Illuminate\Support\Facades\Mail::to($record->reporter->email)
                                ->send(new \App\Mail\TicketReportMail($record));
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Correo y PDF enviados exitosamente')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('No se pudo enviar')
                                ->body('El usuario que reportó no tiene un correo registrado en el sistema.')
                                ->danger()
                                ->send();
                        }
                    }),

                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
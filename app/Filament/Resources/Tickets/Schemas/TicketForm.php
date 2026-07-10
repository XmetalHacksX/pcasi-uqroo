<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Models\UserProfile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use App\Enums\StatusEnum;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('status_id')->default(fn() => StatusEnum::NUEVO->id())->dehydrated(fn (string $operation) => $operation === 'create'),
                Hidden::make('reporter_id')->default(fn() => auth()->id())->dehydrated(fn (string $operation) => $operation === 'create'),

                Wizard::make([

                    // ──────────────────────────────────────────────
                    // PASO 1 — ¿Qué quieres reportar?
                    // ──────────────────────────────────────────────
                    Step::make('Tipo de Reporte')
                        ->description('Selecciona el tipo de trámite')
                        ->icon(Heroicon::DocumentText)
                        ->completedIcon(Heroicon::CheckCircle)
                        ->afterValidation(function () {
                            $profile = UserProfile::where('user_id', auth()->id())->first();

                            $incompleto = !$profile
                                || !$profile->phone
                                || !$profile->age
                                || !$profile->gender
                                || !$profile->sex
                                || !$profile->vulnerable_group;

                            if ($incompleto) {
                                Notification::make()
                                    ->title('Completa tu perfil primero')
                                    ->body('Antes de levantar un reporte necesitas completar tu información personal. Ve al Dashboard y llena el formulario de perfil.')
                                    ->warning()
                                    ->persistent()
                                    ->send();

                                throw new Halt();
                            }
                        })
                        ->schema([
                            Select::make('ticket_group')
                                ->options([
                                    'SGC'             => 'Queja, Sugerencia o Felicitación (SGC)',
                                    'GENERO'          => 'Reporte de Violencia de Género o Discriminación',
                                    'INFRAESTRUCTURA' => 'Reporte de Infraestructura',
                                ])
                                ->required()
                                ->live()
                                ->label('¿Qué deseas reportar?')
                                ->helperText('Selecciona la categoría que mejor describe tu solicitud.'),
                        ]),

                    // ──────────────────────────────────────────────
                    // PASO 2 — Detalles del reporte
                    // ──────────────────────────────────────────────
                    Step::make('Detalles del Reporte')
                        ->description('Completa la información de tu reporte')
                        ->icon(Heroicon::PencilSquare)
                        ->completedIcon(Heroicon::CheckCircle)
                        ->schema([

                            // ── SGC ──────────────────────────────────────
                            Section::make('Datos de la Queja, Sugerencia o Felicitación')
                                ->relationship('ticketSgcDetail')
                                ->hidden(fn(Get $get) => $get('ticket_group') !== 'SGC')
                                ->columns(2)
                                ->schema(fn() => self::buildFormFieldsForGroup('SGC')),

                            // ── GÉNERO ───────────────────────────────────
                            Section::make('Datos del Reporte de Género y Discriminación')
                                ->relationship('ticketGenderDetail')
                                ->hidden(fn(Get $get) => $get('ticket_group') !== 'GENERO')
                                ->columns(2)
                                ->schema(fn() => self::buildFormFieldsForGroup('GENERO')),

                            // ── INFRAESTRUCTURA ──────────────────────────
                            Section::make('Detalles de Infraestructura')
                                ->relationship('ticketInfraDetail')
                                ->hidden(fn(Get $get) => $get('ticket_group') !== 'INFRAESTRUCTURA')
                                ->columns(2)
                                ->schema(fn() => self::buildFormFieldsForGroup('INFRAESTRUCTURA')),
                        ]),

                ])
                    ->skippable(false)
                    ->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button type="submit" size="sm">
                        Enviar Reporte
                    </x-filament::button>
                BLADE)))
                    ->columnSpanFull(),
            ]);
    }

    protected static function buildFormFieldsForGroup(string $groupName): array
    {
        $form = \App\Models\DynamicForm::where('name', $groupName)->first();
        if (!$form) {
            return [];
        }

        $components = [];

        // Inyección de elementos fijos de diseño
        if ($groupName === 'GENERO') {
            $components[] = Placeholder::make('_aviso_genero')
                ->label('')
                ->columnSpanFull()
                ->content(new HtmlString(
                    '<div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
                    <strong>🔒 Este reporte es estrictamente confidencial</strong><br>
                    Será atendido con la discreción y seriedad que el caso requiere.
                </div>'
                ));
        }

        // Obtener todos los campos activos ordenados de este formulario
        $fields = \App\Models\DynamicFormField::whereHas('step', function ($q) use ($form) {
                $q->where('dynamic_form_id', $form->id);
            })
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($fields as $field) {
            $component = self::createFieldComponent($field);
            if ($component) {
                $components[] = $component;
            }
        }

        return $components;
    }

    protected static function createFieldComponent(\App\Models\DynamicFormField $field)
    {
        $name = $field->name;

        switch ($field->type) {
            case 'text':
                $component = TextInput::make($name);
                break;

            case 'textarea':
                $component = Textarea::make($name)->rows(5)->columnSpanFull();
                break;

            case 'toggle':
                $component = Toggle::make($name)->inline(false);
                break;

            case 'date':
                $component = \Filament\Forms\Components\DatePicker::make($name);
                break;

            case 'select':
                $component = Select::make($name)
                    ->options($field->options ?? [])
                    ->placeholder($field->placeholder ?? 'Selecciona una opción...')
                    ->live();
                break;

            case 'select_multiple':
                $component = Select::make($name)
                    ->multiple()
                    ->options($field->options ?? [])
                    ->placeholder($field->placeholder ?? 'Selecciona una o varias...')
                    ->live();
                break;

            // CAMPOS DE CATÁLOGO ESPECIALES
            case 'select_user_type':
                $component = Select::make($name)
                    ->options([
                        'administrativo' => 'Personal Administrativo',
                        'academico'      => 'Personal Académico',
                        'estudiante'     => 'Estudiante',
                    ])
                    ->live()
                    ->afterStateUpdated(fn($set) => $set('temp_campus_id', null) & $set('department_id', null) & $set('subdepartment_id', null) & $set('academic_division_id', null) & $set('educational_program_id', null));
                break;

            case 'select_campus':
                $component = Select::make($name)
                    ->options(function () {
                        $user = auth()->user();
                        if ($user && $user->campuses()->exists()) {
                            return $user->campuses->pluck('name', 'id');
                        }
                        return \App\Models\Campus::pluck('name', 'id');
                    })
                    ->live()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $set('building_id', null);
                        $set('location_id', null);
                        $set('academic_division_id', null);
                        $set('department_id', null);
                    });
                break;

            case 'select_building':
                $component = Select::make($name)
                    ->live()
                    ->disabled(fn(Get $get) => !$get('campus_id'))
                    ->afterStateUpdated(fn(callable $set) => $set('location_id', null))
                    ->options(
                        fn(Get $get) =>
                        \App\Models\Building::where('campus_id', $get('campus_id'))
                            ->pluck('name', 'id')
                    );
                break;

            case 'select_location':
                $component = Select::make($name)
                    ->disabled(fn(Get $get) => !$get('building_id'))
                    ->options(
                        fn(Get $get) =>
                        \App\Models\Location::where('building_id', $get('building_id'))
                            ->pluck('name', 'id')
                    );
                break;

            case 'select_department':
                $component = Select::make($name)
                    ->options(function (Get $get) {
                        if ($get('../../ticket_group') === 'SGC') {
                            return \App\Models\Department::where('id', '<=', 11)->pluck('name', 'id');
                        }
                        $campusId = $get('campus_id');
                        if (!$campusId) return [];
                        return \App\Models\Department::where('campus_id', $campusId)->pluck('name', 'id');
                    })
                    ->visible(function (Get $get) {
                        if ($get('../../ticket_group') === 'SGC') {
                            return $get('user_type') === 'administrativo' && $get('temp_campus_id');
                        }
                        return $get('reported_person_type') === 'administrativo';
                    })
                    ->required(function (Get $get) {
                        if ($get('../../ticket_group') === 'SGC') {
                            return $get('user_type') === 'administrativo';
                        }
                        return $get('reported_person_type') === 'administrativo';
                    })
                    ->live()
                    ->afterStateUpdated(fn($set) => $set('subdepartment_id', null))
                    ->searchable()
                    ->preload();
                break;

            case 'select_subdepartment':
                $component = Select::make($name)
                    ->options(function (Get $get) {
                        if ($get('../../ticket_group') === 'SGC') {
                            if ($get('user_type') === 'administrativo') {
                                return \App\Models\Subdepartment::where('department_id', $get('department_id'))->pluck('name', 'id');
                            }
                            if ($get('user_type') === 'academico') {
                                $division = \App\Models\AcademicDivision::find($get('academic_division_id'));
                                if (!$division) return [];
                                $dept = \App\Models\Department::where('name', $division->name)->where('campus_id', $division->campus_id)->first();
                                if (!$dept) return [];
                                return \App\Models\Subdepartment::where('department_id', $dept->id)->pluck('name', 'id');
                            }
                        }
                        return \App\Models\Subdepartment::where('department_id', $get('department_id'))->pluck('name', 'id');
                    })
                    ->visible(function (Get $get) {
                        if ($get('../../ticket_group') === 'SGC') {
                            return ($get('user_type') === 'administrativo' && $get('department_id'))
                                || ($get('user_type') === 'academico' && $get('academic_division_id'));
                        }
                        return $get('reported_person_type') === 'administrativo' && $get('department_id');
                    })
                    ->required(function (Get $get) {
                        if ($get('../../ticket_group') === 'SGC') {
                            return $get('user_type') === 'administrativo' || $get('user_type') === 'academico';
                        }
                        return false;
                    })
                    ->searchable()
                    ->preload();
                break;

            case 'select_academic_division':
                $component = Select::make($name)
                    ->options(function (Get $get) {
                        if ($get('../../ticket_group') === 'SGC') {
                            return \App\Models\AcademicDivision::where('campus_id', $get('temp_campus_id'))->pluck('name', 'id');
                        }
                        return \App\Models\AcademicDivision::where('campus_id', $get('campus_id'))->pluck('name', 'id');
                    })
                    ->visible(function (Get $get) {
                        if ($get('../../ticket_group') === 'SGC') {
                            return $get('user_type') === 'academico' && $get('temp_campus_id');
                        }
                        return $get('reported_person_type') === 'academico';
                    })
                    ->required(function (Get $get) {
                        if ($get('../../ticket_group') === 'SGC') {
                            return $get('user_type') === 'academico';
                        }
                        return $get('reported_person_type') === 'academico';
                    })
                    ->live()
                    ->afterStateUpdated(fn($set) => $set('subdepartment_id', null))
                    ->searchable()
                    ->preload();
                break;

            case 'select_educational_program':
                $component = Select::make($name)
                    ->options(function (Get $get) {
                        if ($get('../../ticket_group') === 'SGC') {
                            return \App\Models\EducationalProgram::whereHas('academicDivision', fn ($q) => $q->where('campus_id', $get('temp_campus_id')))->pluck('name', 'id');
                        }
                        return \App\Models\EducationalProgram::where('academic_division_id', $get('academic_division_id'))->pluck('name', 'id');
                    })
                    ->visible(function (Get $get) {
                        if ($get('../../ticket_group') === 'SGC') {
                            return $get('user_type') === 'estudiante' && $get('temp_campus_id');
                        }
                        return $get('reported_person_type') === 'academico' && $get('academic_division_id');
                    })
                    ->required(function (Get $get) {
                        if ($get('../../ticket_group') === 'SGC') {
                            return $get('user_type') === 'estudiante';
                        }
                        return $get('reported_person_type') === 'academico';
                    })
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $program = \App\Models\EducationalProgram::find($state);
                            $set('academic_division_id', $program?->academic_division_id);
                        } else {
                            $set('academic_division_id', null);
                        }
                    })
                    ->searchable()
                    ->preload();
                break;

            default:
                return null;
        }

        if ($component) {
            $component->label($field->label);

            if ($field->helper_text) {
                $component->helperText($field->helper_text);
            }

            if ($field->placeholder && method_exists($component, 'placeholder')) {
                $component->placeholder($field->placeholder);
            }

            if ($field->is_required) {
                $formName = $field->step->form->name;
                $component->required(fn(Get $get) => $get('../../ticket_group') === $formName);
            }

            // Visibilidades condicionales específicas heredadas
            if ($field->name === 'witnesses_details') {
                $component->hidden(fn(Get $get) => $get('witnesses') !== 'si')->columnSpanFull();
            }
            if ($field->name === 'communicated_to') {
                $component->visible(fn(Get $get) => $get('has_communicated'));
            }
            if ($field->name === 'communication_results') {
                $component->visible(fn(Get $get) => $get('has_communicated'))->columnSpanFull();
            }
        }

        return $component;
    }
}

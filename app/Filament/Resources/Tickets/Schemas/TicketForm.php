<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Models\Building;
use App\Models\Location;
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
use Illuminate\Support\Str;
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
                                ->schema([
                                    TextInput::make('reported_person_name')
                                        ->label('Nombre de la persona involucrada (Opcional)')
                                        ->columnSpanFull(),

                                    // 1. Selector de Tipo de Usuario
                                    Select::make('user_type')
                                        ->label('Tipo de Usuario')
                                        ->options([
                                            'administrativo' => 'Personal Administrativo',
                                            'academico'      => 'Personal Académico',
                                            'estudiante'     => 'Estudiante',
                                        ])
                                        ->required(fn(Get $get) => $get('../../ticket_group') === 'SGC')
                                        ->live()
                                        ->afterStateUpdated(fn($set) => $set('temp_campus_id', null) & $set('department_id', null) & $set('subdepartment_id', null) & $set('academic_division_id', null) & $set('educational_program_id', null)),

                                    // 2. Elegimos el campus para filtrar correctamente
                                    Select::make('temp_campus_id')
                                        ->label('Campus')
                                        ->options(function () {
                                            $user = auth()->user();
                                            if ($user && $user->campuses()->exists()) {
                                                return $user->campuses->pluck('name', 'id');
                                            }
                                            return \App\Models\Campus::pluck('name', 'id');
                                        })
                                        ->visible(fn(Get $get) => filled($get('user_type')))
                                        ->required(fn(Get $get) => filled($get('user_type')))
                                        ->live()
                                        ->dehydrated(false) // No se guarda en BD, solo sirve para filtrar
                                        ->afterStateUpdated(fn($set) => $set('department_id', null) & $set('subdepartment_id', null) & $set('academic_division_id', null) & $set('educational_program_id', null)),

                                    // --- FLUJO ADMINISTRATIVO ---
                                    Select::make('department_id')
                                        ->label('Dirección General')
                                        ->options(fn(Get $get) => \App\Models\Department::where('id', '<=', 11)->pluck('name', 'id'))
                                        ->visible(fn(Get $get) => $get('user_type') === 'administrativo' && $get('temp_campus_id'))
                                        ->required(fn(Get $get) => $get('user_type') === 'administrativo')
                                        ->live()
                                        ->afterStateUpdated(fn($set) => $set('subdepartment_id', null))
                                        ->searchable()
                                        ->preload(),

                                    Select::make('subdepartment_id')
                                        ->label('Departamento / Oficina')
                                        ->options(fn(Get $get) => \App\Models\Subdepartment::where('department_id', $get('department_id'))->pluck('name', 'id'))
                                        ->visible(fn(Get $get) => $get('user_type') === 'administrativo' && $get('department_id'))
                                        ->required(fn(Get $get) => $get('user_type') === 'administrativo')
                                        ->searchable()
                                        ->preload(),

                                    // --- FLUJO ACADÉMICO ---
                                    Select::make('academic_division_id')
                                        ->label('División Académica')
                                        ->options(fn(Get $get) => \App\Models\AcademicDivision::where('campus_id', $get('temp_campus_id'))->pluck('name', 'id'))
                                        ->visible(fn(Get $get) => $get('user_type') === 'academico' && $get('temp_campus_id'))
                                        ->required(fn(Get $get) => $get('user_type') === 'academico')
                                        ->live()
                                        ->afterStateUpdated(fn($set) => $set('subdepartment_id', null))
                                        ->searchable()
                                        ->preload(),

                                    Select::make('subdepartment_id')
                                        ->label('Departamento Académico')
                                        ->options(function (Get $get) {
                                            $division = \App\Models\AcademicDivision::find($get('academic_division_id'));
                                            if (!$division) return [];
                                            $dept = \App\Models\Department::where('name', $division->name)->where('campus_id', $division->campus_id)->first();
                                            if (!$dept) return [];
                                            return \App\Models\Subdepartment::where('department_id', $dept->id)->pluck('name', 'id');
                                        })
                                        ->visible(fn(Get $get) => $get('user_type') === 'academico' && $get('academic_division_id'))
                                        ->required(fn(Get $get) => $get('user_type') === 'academico')
                                        ->searchable()
                                        ->preload(),

                                    // --- FLUJO ESTUDIANTE ---
                                    Select::make('educational_program_id')
                                        ->label('Carrera / Programa Educativo')
                                        ->options(fn(Get $get) => \App\Models\EducationalProgram::whereHas('academicDivision', fn ($q) => $q->where('campus_id', $get('temp_campus_id')))->pluck('name', 'id'))
                                        ->visible(fn(Get $get) => $get('user_type') === 'estudiante' && $get('temp_campus_id'))
                                        ->required(fn(Get $get) => $get('user_type') === 'estudiante')
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
                                        ->preload(),

                                    // --- CAMPOS ORIGINALES ---
                                    Select::make('classification')
                                        ->options([
                                            'Queja'        => 'Queja',
                                            'Sugerencia'   => 'Sugerencia',
                                            'Felicitación' => 'Felicitación',
                                        ])
                                        ->required(fn(Get $get) => $get('../../ticket_group') === 'SGC')
                                        ->columnSpanFull()
                                        ->label('Los hechos corresponden a una:'),

                                    Textarea::make('description')
                                        ->required(fn(Get $get) => $get('../../ticket_group') === 'SGC')
                                        ->rows(5)
                                        ->columnSpanFull()
                                        ->label('Descripción del Asunto'),
                                ]),

                            // ── GÉNERO ───────────────────────────────────
                            Section::make('Datos del Reporte de Género y Discriminación')
                                ->relationship('ticketGenderDetail')
                                ->hidden(fn(Get $get) => $get('ticket_group') !== 'GENERO')
                                ->columns(2)
                                ->schema([
                                    Placeholder::make('_aviso_genero')
                                        ->label('')
                                        ->columnSpanFull()
                                        ->content(new HtmlString(
                                            '<div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
                                            <strong>🔒 Este reporte es estrictamente confidencial</strong><br>
                                            Será atendido con la discreción y seriedad que el caso requiere.
                                        </div>'
                                        )),

                                    TextInput::make('reported_person_name')
                                        ->label('Nombre de la persona contra quien se presenta la queja')
                                        ->required()
                                        ->columnSpanFull(),

                                    Select::make('reported_person_type')
                                        ->options([
                                            'academico' => 'Estudiante / Docente',
                                            'administrativo' => 'Personal Administrativo',
                                            'Externo' => 'Externo',
                                        ])
                                        ->label('Tipo de usuario de la persona responsable')
                                        ->required()
                                        ->live(),

                                    // ─── EL DESGLOSE DINÁMICO EMPIEZA AQUÍ ───

                                    // 1. Campus Base (Para ambos flujos)
                                    Select::make('campus_id')
                                        ->label('Campus de Adscripción')
                                        ->options(function () {
                                            $user = auth()->user();
                                            if ($user && $user->campuses()->exists()) {
                                                return $user->campuses->pluck('name', 'id');
                                            }
                                            return \App\Models\Campus::pluck('name', 'id');
                                        })
                                        ->required()
                                        ->live()
                                        ->dehydrated(fn (Get $get) => $get('reported_person_type') === 'Externo')
                                        ->afterStateUpdated(fn($set) => $set('academic_division_id', null) & $set('department_id', null)),

                                    // 2. Flujo Administrativo: Campus -> Área/Dirección -> Oficina
                                    Select::make('department_id')
                                        ->label('Área / Unidad Académica / Dirección General')
                                        ->options(fn(Get $get) => \App\Models\Department::where('campus_id', $get('campus_id'))->pluck('name', 'id'))
                                        ->visible(fn(Get $get) => $get('reported_person_type') === 'administrativo')
                                        ->required(fn(Get $get) => $get('reported_person_type') === 'administrativo')
                                        ->searchable()
                                        ->preload()
                                        ->live() // <-- ¡CRÍTICO para que aparezcan los subdepartamentos!
                                        ->afterStateUpdated(fn($set) => $set('subdepartment_id', null)), // Limpia la oficina si cambias de dirección

                                    Select::make('subdepartment_id')
                                        ->label('Oficina / Subdepartamento (Opcional)')
                                        ->options(fn(Get $get) => \App\Models\Subdepartment::where('department_id', $get('department_id'))->pluck('name', 'id'))
                                        ->visible(fn(Get $get) => $get('reported_person_type') === 'administrativo' && $get('department_id'))
                                        ->searchable()
                                        ->preload(),

                                    // 3. Flujo Académico: Campus -> División -> Carrera
                                    Select::make('academic_division_id')
                                        ->label('División Académica de Adscripción')
                                        ->options(fn(Get $get) => \App\Models\AcademicDivision::where('campus_id', $get('campus_id'))->pluck('name', 'id'))
                                        ->visible(fn(Get $get) => $get('reported_person_type') === 'academico')
                                        ->required(fn(Get $get) => $get('reported_person_type') === 'academico')
                                        ->live(),

                                    Select::make('educational_program_id')
                                        ->label('Programa Académico de Adscripción (Carrera)')
                                        ->options(fn(Get $get) => \App\Models\EducationalProgram::where('academic_division_id', $get('academic_division_id'))->pluck('name', 'id'))
                                        ->visible(fn(Get $get) => $get('reported_person_type') === 'academico' && $get('academic_division_id'))
                                        ->required(fn(Get $get) => $get('reported_person_type') === 'academico'),

                                    // Dato extra: El cargo de la persona (Ya que quitamos "oficina" de aquí)
                                    // NOTA: Asegúrate de tener esta columna en tu base de datos (yo no la vi en tu dump), 
                                    // si no la tienes, simplemente borra este TextInput o Filament marcará error al guardar.
                                    // ─── AQUÍ SIGUE TU CÓDIGO ORIGINAL SIN CAMBIOS ───

                                    TextInput::make('reported_person_details')
                                        ->label('Datos institucionales adicionales (Cargo, Oficina, etc.)')
                                        ->placeholder('Escriba el cargo o detalles específicos...')
                                        ->columnSpanFull(),

                                    Select::make('manifestation_type')
                                        ->options([
                                            'Violencia de género' => 'Violencia de género',
                                            'Acoso' => 'Acoso / Hostigamiento',
                                            'Discriminación' => 'Discriminación',
                                        ])
                                        ->required(fn(Get $get) => $get('../../ticket_group') === 'GENERO')
                                        ->columnSpanFull()
                                        ->label('¿De qué forma se manifestó el hecho?'),

                                    Textarea::make('chronological_narrative')
                                        ->required(fn(Get $get) => $get('../../ticket_group') === 'GENERO')
                                        ->rows(5)
                                        ->columnSpanFull()
                                        ->label('Narración cronológica de los hechos (tiempo, modo y lugar)'),

                                    Toggle::make('has_evidence')
                                        ->label('¿Cuenta con pruebas o evidencias?')
                                        ->inline(false),

                                    Select::make('witnesses')
                                        ->options(['si' => 'Sí hubo testigos', 'no' => 'Nadie los presenció'])
                                        ->live()
                                        ->label('¿Hubo testigos?'),

                                    Textarea::make('witnesses_details')
                                        ->rows(2)
                                        ->columnSpanFull()
                                        ->hidden(fn(Get $get) => $get('witnesses') !== 'si')
                                        ->label('Nombre y datos de contacto de los testigos'),

                                    Toggle::make('needs_psychological_support')
                                        ->label('¿Considera necesario acompañamiento profesional?')
                                        ->inline(false),

                                    Toggle::make('has_communicated')
                                        ->label('¿Has reportado este incidente previamente a alguna autoridad de la universidad?')
                                        ->inline(false)
                                        ->live()
                                        ->dehydrated(false),

                                    Select::make('communicated_to')
                                        ->multiple()
                                        ->options([
                                            'Tutor(a)'                   => 'Tutor(a)',
                                            'Docente'                    => 'Docente',
                                            'Jefe(a) de Departamento'    => 'Jefe(a) de Departamento',
                                            'Director(a) de División'    => 'Director(a) de División',
                                            'Recursos Humanos'           => 'Recursos Humanos',
                                            'Abogado General'            => 'Abogado General',
                                            'Otro'                       => 'Otro',
                                        ])
                                        ->label('Comuniqué estos hechos a:')
                                        ->visible(fn(Get $get) => $get('has_communicated')),

                                    Textarea::make('communication_results')
                                        ->rows(2)
                                        ->columnSpanFull()
                                        ->label('Resultado de la comunicación (opcional)')
                                        ->visible(fn(Get $get) => $get('has_communicated')),
                                ]),

                            // ── INFRAESTRUCTURA ──────────────────────────
                            Section::make('Detalles de Infraestructura')
                                ->relationship('ticketInfraDetail')
                                ->hidden(fn(Get $get) => $get('ticket_group') !== 'INFRAESTRUCTURA')
                                ->columns(2)
                                ->schema([

                                    // 1️⃣ Campus — muestra todos, activa la cadena
                                    Select::make('campus_id')
                                        ->relationship('campus', 'name', function (\Illuminate\Database\Eloquent\Builder $query) {
                                            $user = auth()->user();
                                            if ($user && $user->campuses()->exists()) {
                                                $query->whereIn('campuses.id', $user->campuses->pluck('id'));
                                            }
                                        })
                                        ->required(fn(Get $get) => $get('../../ticket_group') === 'INFRAESTRUCTURA')
                                        ->live() // ✅ dispara la actualización de edificio
                                        ->afterStateUpdated(fn(callable $set) => $set('building_id', null) & $set('location_id', null))
                                        ->label('Campus'),

                                    // 2️⃣ Edificio — filtrado por campus seleccionado
                                    Select::make('building_id')
                                        ->required(fn(Get $get) => $get('../../ticket_group') === 'INFRAESTRUCTURA')
                                        ->live() // ✅ dispara la actualización de ubicación
                                        ->disabled(fn(Get $get) => !$get('campus_id'))
                                        ->afterStateUpdated(fn(callable $set) => $set('location_id', null))
                                        ->options(
                                            fn(Get $get) =>
                                            Building::where('campus_id', $get('campus_id'))
                                                ->pluck('name', 'id')
                                        )
                                        ->label('Edificio'),

                                    // 3️⃣ Ubicación — filtrada por edificio seleccionado
                                    Select::make('location_id')
                                        ->disabled(fn(Get $get) => !$get('building_id'))
                                        ->options(
                                            fn(Get $get) =>
                                            Location::where('building_id', $get('building_id'))
                                                ->pluck('name', 'id')
                                        )
                                        ->label('Ubicación específica (aula, baño, pasillo...)'),

                                    Select::make('issue_type')
                                        ->options([
                                            'Falla Red'     => 'Falla de Red / Internet',
                                            'Limpieza'      => 'Limpieza',
                                            'Mantenimiento' => 'Mantenimiento General',
                                            'Electricidad'  => 'Electricidad / Iluminación',
                                            'Otro'          => 'Otro',
                                        ])
                                        ->required(fn(Get $get) => $get('../../ticket_group') === 'INFRAESTRUCTURA')
                                        ->label('Tipo de falla'),

                                    Textarea::make('description')
                                        ->required(fn(Get $get) => $get('../../ticket_group') === 'INFRAESTRUCTURA')
                                        ->rows(5)
                                        ->columnSpanFull()
                                        ->label('Descripción detallada de la falla'),
                                ]),
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
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DynamicFormResource\Pages\CreateDynamicForm;
use App\Filament\Resources\DynamicFormResource\Pages\EditDynamicForm;
use App\Filament\Resources\DynamicFormResource\Pages\ListDynamicForms;
use App\Models\DynamicForm;
use BackedEnum;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DynamicFormResource extends Resource
{
    protected static ?string $model = DynamicForm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Administración';
    protected static ?string $modelLabel = 'Formulario Dinámico';
    protected static ?string $pluralModelLabel = 'Formularios Dinámicos';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General del Formulario')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Identificador Interno (Código)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($record) => $record !== null)
                            ->dehydrated()
                            ->maxLength(255),

                        TextInput::make('title')
                            ->label('Título del Formulario')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),

                Section::make('Pasos y Preguntas del Formulario')
                    ->description('Crea los pasos y añade las preguntas que verán los usuarios. Puedes arrastrar y soltar para reordenar.')
                    ->schema([
                        Repeater::make('steps')
                            ->relationship('steps')
                            ->orderColumn('sort_order')
                            ->label('Secciones / Pasos')
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Nueva Sección')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Título de la Sección')
                                            ->required(),

                                        TextInput::make('description')
                                            ->label('Descripción / Subtítulo'),
                                    ]),

                                Repeater::make('fields')
                                    ->relationship('fields')
                                    ->orderColumn('sort_order')
                                    ->label('Preguntas / Campos')
                                    ->collapsible()
                                    ->grid(1)
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Nueva Pregunta')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Columna BD (Identificador único)')
                                                    ->required()
                                                    ->disabled(fn ($record) => $record !== null && in_array($record->name, [
                                                        'reported_person_name', 'reported_person_type', 'campus_id', 'department_id',
                                                        'subdepartment_id', 'academic_division_id', 'educational_program_id',
                                                        'reported_person_details', 'manifestation_type', 'chronological_narrative',
                                                        'has_evidence', 'witnesses', 'witnesses_details', 'needs_psychological_support',
                                                        'has_communicated', 'communicated_to', 'communication_results', 'building_id',
                                                        'location_id', 'issue_type', 'missing_supplies', 'classification', 'description'
                                                    ]))
                                                    ->dehydrated()
                                                    ->placeholder('ej. telefono_contacto'),

                                                TextInput::make('label')
                                                    ->label('Pregunta / Etiqueta')
                                                    ->required(),

                                                Select::make('type')
                                                    ->label('Tipo de Campo')
                                                    ->required()
                                                    ->options([
                                                        'text' => 'Texto Corto',
                                                        'textarea' => 'Texto Largo',
                                                        'select' => 'Selección Única (Estática)',
                                                        'select_multiple' => 'Selección Múltiple (Estática)',
                                                        'toggle' => 'Interruptor Sí/No',
                                                        'date' => 'Fecha',
                                                        'select_campus' => '🏫 Selector de Campus',
                                                        'select_building' => '🏢 Selector de Edificio (dependiente)',
                                                        'select_location' => '📍 Selector de Ubicación (dependiente)',
                                                        'select_department' => '📂 Selector de Dirección (dependiente)',
                                                        'select_subdepartment' => '💼 Selector de Oficina (dependiente)',
                                                        'select_academic_division' => '🎓 Selector de División Académica (dependiente)',
                                                        'select_educational_program' => '📚 Selector de Carrera (dependiente)',
                                                        'select_user_type' => '👥 Selector de Tipo de Usuario',
                                                    ])
                                                    ->live(),
                                            ]),

                                        KeyValue::make('options')
                                            ->label('Opciones del Selector')
                                            ->keyLabel('Valor Guardado (ej. si, no, queja)')
                                            ->valueLabel('Texto Visible (ej. Sí, No, Queja)')
                                            ->visible(fn (Get $get) => in_array($get('type'), ['select', 'select_multiple']))
                                            ->columnSpanFull(),

                                        Grid::make(4)
                                            ->schema([
                                                TextInput::make('placeholder')
                                                    ->label('Texto de marcador (Placeholder)'),

                                                TextInput::make('helper_text')
                                                    ->label('Texto de ayuda'),

                                                Toggle::make('is_required')
                                                    ->label('¿Obligatorio?')
                                                    ->default(false)
                                                    ->inline(false),

                                                Toggle::make('is_active')
                                                    ->label('¿Activo?')
                                                    ->default(true)
                                                    ->inline(false),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Identificador')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50),

                TextColumn::make('steps_count')
                    ->label('Pasos')
                    ->counts('steps'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDynamicForms::route('/'),
            'create' => CreateDynamicForm::route('/create'),
            'edit' => EditDynamicForm::route('/{record}/edit'),
        ];
    }
}

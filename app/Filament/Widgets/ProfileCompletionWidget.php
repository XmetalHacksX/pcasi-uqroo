<?php

namespace App\Filament\Widgets;

use App\Models\UserProfile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;
// Imports para la funcionalidad de Actions (Modales)
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;

class ProfileCompletionWidget extends Widget implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    protected string $view = 'filament.widgets.profile-completion-widget';

    protected int | string | array $columnSpan = 'full';

    public ?array $data = [];
    public bool $profileComplete = false;

    public function mount(): void
    {
        $profile = UserProfile::where('user_id', auth()->id())->first();

        $this->profileComplete = $this->isProfileComplete($profile);

        if (!$this->profileComplete) {
            $this->profileForm->fill([
                'phone'            => $profile?->phone,
                'age'              => $profile?->age,
                'gender'           => $profile?->gender,
                'sex'              => $profile?->sex,
                'vulnerable_group' => $profile?->vulnerable_group,
            ]);
        }
    }

    /**
     * Acción para editar el perfil desde un Modal (Opción B)
     */
    public function editProfileAction(): Action
    {
        return Action::make('editProfile')
            ->label('Editar información institucional')
            ->icon('heroicon-m-pencil-square')
            ->color('gray')
            ->size('sm')
            ->outlined()
            // Cargamos los datos actuales al abrir el modal
            ->fillForm(function () {
                $profile = UserProfile::where('user_id', auth()->id())->first();
                return $profile ? $profile->toArray() : [];
            })
            // Reutilizamos los campos del formulario principal
            ->form([
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->required(),
                TextInput::make('age')
                    ->label('Edad')
                    ->numeric()
                    ->required()
                    ->minValue(16),
                Select::make('gender')
                    ->label('Género')
                    ->options([
                        'Masculino' => 'Masculino',
                        'Femenino' => 'Femenino',
                        'No binario' => 'No binario',
                        'Prefiero no decir' => 'Prefiero no decir',
                    ])->required(),
                Select::make('sex')
                    ->label('Sexo')
                    ->options([
                        'Hombre' => 'Hombre',
                        'Mujer' => 'Mujer',
                        'Prefiero no decir' => 'Prefiero no decir',
                    ])->required(),
                Select::make('vulnerable_group')
                    ->label('¿Grupo vulnerable?')
                    ->options([
                        'No' => 'No',
                        'Persona con discapacidad' => 'Persona con discapacidad',
                        'Adulto mayor' => 'Adulto mayor',
                        'Comunidad indígena' => 'Comunidad indígena',
                        'LGBTTTIQ+' => 'LGBTTTIQ+',
                        'Migrante' => 'Migrante',
                        'Prefiero no decir' => 'Prefiero no decir',
                    ])->required(),
            ])
            ->action(function (array $data) {
                UserProfile::updateOrCreate(
                    ['user_id' => auth()->id()],
                    $data
                );

                Notification::make()
                    ->title('Perfil actualizado')
                    ->success()
                    ->send();
            })
            ->modalHeading('Actualizar Datos del Perfil')
            ->modalWidth('2xl');
    }

    public function profileForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->required()
                    ->maxLength(15),

                TextInput::make('age')
                    ->label('Edad')
                    ->numeric()
                    ->required()
                    ->minValue(16)
                    ->maxValue(100),

                Select::make('gender')
                    ->label('Género')
                    ->options([
                        'Masculino'         => 'Masculino',
                        'Femenino'          => 'Femenino',
                        'No binario'        => 'No binario',
                        'Prefiero no decir' => 'Prefiero no decir',
                    ])
                    ->required(),

                Select::make('sex')
                    ->label('Sexo')
                    ->options([
                        'Hombre'            => 'Hombre',
                        'Mujer'             => 'Mujer',
                        'Prefiero no decir' => 'Prefiero no decir',
                    ])
                    ->required(),

                Select::make('vulnerable_group')
                    ->label('¿Se identifica con algún grupo vulnerable?')
                    ->options([
                        'No'                       => 'No',
                        'Persona con discapacidad' => 'Persona con discapacidad',
                        'Adulto mayor'             => 'Adulto mayor',
                        'Comunidad indígena'       => 'Comunidad indígena',
                        'LGBTTTIQ+'                => 'LGBTTTIQ+',
                        'Migrante'                 => 'Migrante',
                        'Prefiero no decir'        => 'Prefiero no decir',
                    ])
                    ->required(),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function save(): void
    {
        $validated = $this->profileForm->getState();

        UserProfile::updateOrCreate(
            ['user_id' => auth()->id()],
            $validated
        );

        $this->profileComplete = true;

        Notification::make()
            ->title('¡Perfil completado!')
            ->body('Tu información ha sido guardada correctamente.')
            ->success()
            ->send();
    }

    private function isProfileComplete(?UserProfile $profile): bool
    {
        return $profile
            && $profile->phone
            && $profile->age
            && $profile->gender
            && $profile->sex
            && $profile->vulnerable_group;
    }
}

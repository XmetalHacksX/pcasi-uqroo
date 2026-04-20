<?php

namespace App\Filament\Helper;

use Filament\Schemas\Schema; // <--- 1. Actualizado a Schema
use Filament\Schemas\Components\Section; // <--- 2. Actualizado al namespace correcto para esta versión
use Filament\Auth\Pages\Login as BaseLogin;
use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;

class CustomLogin extends BaseLogin
{
    use HasCustomLayout;

    // 3. Actualizamos la firma para usar Schema
    public function form(Schema $schema): Schema
    {
        return $schema
            // 4. Schema usa ->components() en la raíz en lugar de ->schema()
            ->components([
                Section::make('Acceso con credenciales')
                    ->description('O usa el botón de cuenta Institucional')
                    ->collapsed()
                    ->schema([ // Adentro del Section sí sigue siendo schema()
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
            ])
            ->statePath('data');
    }
}

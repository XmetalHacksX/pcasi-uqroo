<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ProfileCompletionWidget;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Tables\Table;          // Agregado para la estética global de tablas
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(\App\Filament\Helper\CustomLogin::class)
            // 🚀 UX PREMIUM: Modo SPA (Single Page Application)
            // Esto hace que Filament cargue todas las páginas al instante sin recargar el navegador.
            // Es el secreto #1 para que un panel se sienta como una aplicación de primer nivel.
            ->spa()

            // ✍️ TIPOGRAFÍA Y BRANDING
            // Inter es la reina indiscutible del UI/UX moderno (es la usada por Apple, Stripe, Vercel).
            // Le dará un aspecto limpio, profesional y altamente legible a los tickets.
            ->font('Inter')
            
            // Identidad Visual UAEQROO
            ->brandLogo(asset('images/Escudo UAEQROO Oficial-01.png'))
            // 8rem era excesivamente grande para una topbar moderna. 3rem es el estándar dorado.
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/favicon.ico')) // Agrega aquí tu icono si no lo tienes

            // 📐 LAYOUT Y ESPACIADO
            // En vez de quitar la topbar, usamos un Sidebar colapsable. Es la mejor distribución para SaaS.
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('7xl') // <-- CORREGIDO: Se pasa directamente como String

            // 🎨 COLORES INSTITUCIONALES REFINADOS
            ->colors([
                'primary' => Color::hex('#e7bc13'), // Oro UAEQROO
                'success' => Color::hex('#054c31'), // Verde UAEQROO
                // Color::Zinc es un gris puro y neutro superior a Slate (que tira a azul).
                // Hará que el contraste con el blanco de las tarjetas (cards) resalte la pulcritud del panel.
                'gray' => Color::Zinc, 
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                ProfileCompletionWidget::class,
            ])
            ->plugins([
                // 1. Gestión de Roles y Permisos
                FilamentShieldPlugin::make(),

                // 2. Mejora Visual del Login (Auth UI Enhancer)
                AuthUIEnhancerPlugin::make()
                    ->showEmptyPanelOnMobile(false)
                    ->formPanelPosition('right')
                    ->formPanelWidth('40%')
                    ->emptyPanelView('filament.auth.login-image'),

                // 3. Autenticación Institucional Microsoft
                FilamentSocialitePlugin::make()
                    ->providers([
                        Provider::make('microsoft')
                            ->label('Ingresar con cuenta Institucional')
                            ->icon('heroicon-m-globe-alt')
                            ->color('primary')
                            ->outlined(false)
                            ->with(['prompt' => 'select_account']),
                    ])
                    ->registration(true)
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * INYECCIÓN GLOBAL PARA MEJORAR LA UI/UX DE LAS TABLAS DE FILAMENT
     */
    public function boot(): void
    {
        // Aplicamos estilos limpios y profesionales a todas tus Grids / Tables
        Table::configureUsing(function (Table $table): void {
            $table
                ->striped() // Añade un tono zebra tenue
                ->defaultPaginationPageOption(10) // Mantiene la tabla controlada y no gigantesca
                ->emptyStateHeading('Aún no hay registros en esta vista')
                ->emptyStateIcon('heroicon-o-inbox'); // Icono premium en el empty state
        });
    }
}
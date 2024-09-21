<?php

namespace App\Providers\Filament;

use App\Livewire\UserProfile;
use App\Services\Auth\Login;
use App\Services\Layout\LoginImage;
use App\Services\Layout\PanelSize;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;

class TasksPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('tasks')
            ->path('tasks')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->viteTheme('resources/css/filament/tasks/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ])
            ->plugins([

                FilamentBackgroundsPlugin::make()
                ->showAttribution(false)
                ->imageProvider(
                    LoginImage::make()
                ),

                SpotlightPlugin::make(),

                FilamentApexChartsPlugin::make(),

                BreezyCore::make()
                ->myProfile(
                    shouldRegisterUserMenu: false,
                    shouldRegisterNavigation: false,
                    hasAvatars: false,
                    slug: 'my-profile'
                )
                ->myProfileComponents([
                    'personal_info' => UserProfile::class,
                ])
                ->withoutMyProfileComponents([
                    'update_password'
                ]),


            ])
            ->userMenuItems([

                MenuItem::make()
                    ->label('Meu Perfil')
                    ->url(url('/tasks/my-profile'))
                    ->icon('heroicon-s-user'),

            ])
            ->brandLogo(asset('images/layout/logo-panel.png'))
            ->darkModeBrandLogo(asset('images/layout/logo-panel-dark.png'))
            ->brandLogoHeight('2.8rem')
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(PanelSize::default())
            ->defaultThemeMode(ThemeMode::Light)
            ->navigationGroups([
                'Gerenciamento',
                'Configurações',
            ])
            ->databaseNotifications()
            ->unsavedChangesAlerts(false)
            ->databaseNotificationsPolling('30s');
    }

}

<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
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
            ->brandName('BPR Banda Raya')
            ->brandLogo(asset('images/logo-horizontal-clean.png'))
            ->darkModeBrandLogo(asset('images/logo-horizontal-dark.png'))
            ->brandLogoHeight('2.75rem')
            ->favicon(asset('images/logo-tight.png'))
            ->colors([
                'primary' => Color::hex('#0E6838'), // BPR Banda Raya Forest Green
                'warning' => Color::hex('#F37A20'), // BPR Banda Raya Warm Orange
                'amber' => Color::hex('#FDB813'),   // BPR Banda Raya Golden Ribbon
                'gray' => Color::Slate,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.m3-theme')->render()
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => view('filament.components.role-indicator')->render()
            )
            ->databaseNotifications()
            ->lazyLoadedDatabaseNotifications()
            ->databaseNotificationsPolling('120s')
            ->font('Plus Jakarta Sans', provider: \Filament\FontProviders\LocalFontProvider::class)
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
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
                \App\Http\Middleware\SecurityHeaders::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

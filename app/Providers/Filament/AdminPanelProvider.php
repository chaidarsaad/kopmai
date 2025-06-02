<?php

namespace App\Providers\Filament;

use App\Filament\Auth\CostumLogin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\BestSellingProductTable;
use App\Http\Middleware\RedirectIfNotFilamentAdmin;
use App\Livewire\Auth\Login;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use TomatoPHP\FilamentPWA\FilamentPWAPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->sidebarCollapsibleOnDesktop()
            ->default()
            ->id('pengelola')
            ->path('pengelola')
            // ->login(Login::class)
            ->login(CostumLogin::class)
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                RevenueChart::class,
                BestSellingProductTable::class,
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
                    // Authenticate::class,
                RedirectIfNotFilamentAdmin::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Toko Online')
                    ->icon('heroicon-o-building-storefront')
                    ->url(fn() => route('home', absolute: false))
            ])
            ->unsavedChangesAlerts()
            ->databaseNotifications()
            ->databaseNotificationsPolling(5)
            ->profile(isSimple: false)
            ->breadcrumbs(false)
            ->spa()
            ->spaUrlExceptions(fn(): array => [
                url(route('download-template')),
                url(route('home')),
                url(route('download-data')),
                url(route('download-rekap')),
                url(route('download-order')),
                url(route('filament.pengelola.auth.logout'))
            ])
            ->plugins([
                FilamentPWAPlugin::make(),
                FilamentShieldPlugin::make(),
            ])
            ->navigationItems([
                NavigationItem::make('Ubah Profil')
                    ->sort(20)
                    ->isActiveWhen(fn() => request()->routeIs('filament.pengelola.auth.profile'))
                    ->url(fn() => route('filament.pengelola.auth.profile', absolute: true))
                    ->icon('heroicon-o-user'),
            ])
        ;
    }
}

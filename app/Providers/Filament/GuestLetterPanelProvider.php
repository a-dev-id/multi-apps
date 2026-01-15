<?php

namespace App\Providers\Filament;

use App\Filament\GuestLetter\Resources\PostStayLetters\PostStayLetterResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Actions\Action;
use Filament\Facades\Filament;

class GuestLetterPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->favicon('https://nandinibali.com/images/favicon-njhg.png')
            ->default()
            ->id('guestletter-admin')
            ->path('guestletter')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/GuestLetter/Resources'), for: 'App\Filament\GuestLetter\Resources')
            ->resources([
                PostStayLetterResource::class,
            ])
            ->discoverPages(in: app_path('Filament/GuestLetter/Pages'), for: 'App\Filament\GuestLetter\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/GuestLetter/Widgets'), for: 'App\Filament\GuestLetter\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ->userMenuItems([
                'app-chooser' => Action::make('appChooser')
                    ->label('App Chooser')
                    ->icon('heroicon-o-squares-2x2')
                    ->url('/'),

                'logout' => Action::make('logout')
                    ->label('Sign out')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('danger')
                    ->action(fn() => Filament::auth()->logout()),
            ]);
    }
}

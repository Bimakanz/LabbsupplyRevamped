<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
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
            ->login()
            ->brandName('Lab Supply Admin')
            ->favicon(asset('theme/img/favicon-192.png'))
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Master Data')->icon('heroicon-o-circle-stack')->collapsible(),
                NavigationGroup::make('Penjualan')->icon('heroicon-o-shopping-bag')->collapsible(),
                NavigationGroup::make('Konten')->icon('heroicon-o-document-text')->collapsible(),
                NavigationGroup::make('Interaksi')->icon('heroicon-o-chat-bubble-left-right')->collapsible(),
                NavigationGroup::make('Pengguna')->icon('heroicon-o-users')->collapsible(),
            ])
            ->colors([
                'primary' => Color::hex('#4981d4'),
                'gray' => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => '<link rel="stylesheet" href="'.asset('css/filament-admin.css').'"><style>input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button{-webkit-appearance:none;margin:0}input[type=number]{-moz-appearance:textfield;appearance:textfield}.fi-fo-file-upload .filepond--file{background:transparent!important}.fi-fo-file-upload .filepond--file-info,.fi-fo-file-upload .filepond--file-status{display:none!important}.fi-sidebar-item-icon{display:none!important}</style>',
            )
            ->renderHook(
                PanelsRenderHook::SCRIPTS_AFTER,
                fn (): string => '<script>window.labsBgRemoveUrl = '.json_encode(route('admin.image.remove-background'))
                    .'; window.labsCsrf = '.json_encode(csrf_token()).';</script>'
                    .'<script src="'.asset('js/filament-bg-editor.js').'"></script>',
            )
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
}

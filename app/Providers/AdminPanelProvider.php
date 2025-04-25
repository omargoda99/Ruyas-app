<?php

namespace App\Providers;

use Filament\Panel;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;

class AdminPanelProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Filament::registerPanel(
            Panel::make()
                ->id('admin')
                ->path('admin')
                ->login()
                ->maxContentWidth(MaxWidth::SevenExtraLarge)
                ->colors([
                    'primary' => Color::Indigo,
                ])
                ->discoverResources(
                    in: app_path('Filament/Resources'),
                    for: 'App\\Filament\\Resources'
                )
                ->discoverPages(
                    in: app_path('Filament/Pages'),
                    for: 'App\\Filament\\Pages'
                )
        );
    }
}


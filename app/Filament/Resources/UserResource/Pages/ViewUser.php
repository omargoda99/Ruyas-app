<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Support\HtmlString;
use Resources\Widgets\UserActivityWidget;


class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Profile Information')
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make(2)
                                ->schema([
                                    Components\ImageEntry::make('image_url')
                                        ->label('')
                                        ->height('150px')
                                        ->circular()
                                        ->columnSpan(1),

                                    Components\Grid::make(1)
                                        ->schema([
                                            Components\TextEntry::make('name')
                                                ->size('lg')
                                                ->weight('bold'),

                                            Components\TextEntry::make('email')
                                                ->icon('heroicon-o-envelope'),

                                            Components\TextEntry::make('age')
                                                ->icon('heroicon-o-cake'),

                                            Components\TextEntry::make('gender')
                                                ->badge()
                                                ->color(fn (string $state): string => match ($state) {
                                                    'male' => 'primary',
                                                    'female' => 'pink',
                                                    default => 'gray',
                                                }),
                                        ])
                                        ->columnSpan(1),
                                ]),
                        ]),
                    ]),

                Components\Section::make('Account Details')
                    ->columns(3)
                    ->schema([
                        Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'warning',
                                'banned' => 'danger',
                                default => 'gray',
                            }),

                        Components\TextEntry::make('created_at')
                            ->label('Registered On')
                            ->dateTime(),

                        Components\TextEntry::make('last_login_at')
                            ->label('Last Login')
                            ->dateTime()
                            ->placeholder('Never logged in'),

                        Components\TextEntry::make('email_verified_at')
                            ->label('Email Verified')
                            ->dateTime()
                            ->placeholder('Not verified'),

                        Components\TextEntry::make('ip_address')
                            ->label('IP Address')
                            ->placeholder('Unknown'),
                    ]),

                Components\Section::make('Demographic Information')
                    ->columns(3)
                    ->schema([
                        Components\TextEntry::make('marital_status')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : 'Not specified'),

                        Components\TextEntry::make('employment_status')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : 'Not specified'),

                        Components\TextEntry::make('country')
                            ->label('Location')
                            ->formatStateUsing(function ($record) {
                                $location = [];
                                if ($record->city) $location[] = $record->city;
                                if ($record->region) $location[] = $record->region;
                                if ($record->country) $location[] = $record->country;

                                return $location ? implode(', ', $location) : 'Not specified';
                            }),
                    ]),

                Components\Section::make('Activity Stats')
                    ->columns(2)
                    ->schema([
                        Components\TextEntry::make('created_at')
                            ->label('Account Age')
                            ->formatStateUsing(fn ($state) => $state->diffForHumans()),

                        Components\TextEntry::make('last_login_at')
                            ->label('Last Activity')
                            ->formatStateUsing(function ($state) {
                                if (!$state) return 'Never active';
                                return $state->diffForHumans();
                            }),

                        Components\TextEntry::make('login_count')
                            ->label('Login Count')
                            ->default('Not tracked')
                            ->numeric(),

                        Components\TextEntry::make('last_login_ip')
                            ->label('Last Login IP')
                            ->default('Not available'),
                    ]),

                Components\Section::make('Additional Information')
                    ->schema([
                        Components\KeyValueEntry::make('metadata')
                            ->columnSpanFull()
                            ->default(new HtmlString('<div class="text-gray-500 text-sm">No additional information available</div>')),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil'),

            Actions\Action::make('impersonate')
                ->icon('heroicon-o-identification')
                ->url(fn () => route('impersonate', $this->record->id))
                ->hidden(fn () => !auth()->user()->can('impersonate users')),

            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),

            Actions\Action::make('back')
                ->label('Back to list')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            Resources\Widgets\UserActivityWidget::class,
        ];
    }
}

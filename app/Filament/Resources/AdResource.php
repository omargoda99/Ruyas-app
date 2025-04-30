<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdResource\Pages;
use App\Models\Ad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class AdResource extends Resource
{
    protected static ?string $model = Ad::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $modelLabel = 'Advertisement';

    protected static ?string $navigationLabel = 'Advertisements';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ad Information')
                    ->schema([
                        TextInput::make('ad_title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('ad_description')
                            ->label('Description')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('link')
                            ->label('Target URL')
                            ->url()
                            ->nullable(),

                        FileUpload::make('ad_image_path')
                            ->label('Image')
                            ->image()
                            ->directory('ads')
                            ->preserveFilenames()
                            ->nullable(),

                        DateTimePicker::make('start_date')
                            ->label('Start Date')
                            ->required(),

                        DateTimePicker::make('end_date')
                            ->label('End Date')
                            ->required(),

                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'expired' => 'Expired',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('ad_image_path')
                    ->label('Image')
                    ->disk('public')
                    ->width(80)
                    ->height(60),

                TextColumn::make('ad_title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'expired' => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'expired' => 'Expired',
                    ]),

                Tables\Filters\Filter::make('active_ads')
                    ->label('Currently Active')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->before(function (Ad $record) {
                    if ($record->ad_image_path) {
                        Storage::disk('public')->delete($record->ad_image_path);
                    }
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Delete all images when bulk deleting
                            foreach ($records as $record) {
                                if ($record->ad_image_path) {
                                    Storage::disk('public')->delete($record->ad_image_path);
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relation managers if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
    }
}

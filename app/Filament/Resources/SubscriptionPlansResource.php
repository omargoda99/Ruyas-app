<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionPlansResource\Pages;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionPlansResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Plan Name'),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->maxLength(65535)
                    ->nullable(),

                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->step(0.01)
                    ->required()
                    ->label('Price (USD)'),

                Forms\Components\KeyValue::make('features')
                    ->label('Features')
                    ->addButtonLabel('Add Feature')
                    ->keyLabel('Feature')
                    ->valueLabel('Description')
                    ->required(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable()
                    ->label('Price'),

                    Tables\Columns\TextColumn::make('features')
                    ->label('Features')
                    ->formatStateUsing(function ($state) {
                        $features = json_decode($state, true);
                        if (!is_array($features)) return '-';
                        return collect($features)
                            ->map(fn ($desc, $feature) => "• {$feature}: {$desc}")
                            ->implode('<br>');
                    })
                    ->html()
                    ->limit(150)
                    ->wrap()
                    ->sortable(),
                

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created At')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Is Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlans::route('/create'),
            'edit' => Pages\EditSubscriptionPlans::route('/{record}/edit'),
        ];
    }
}

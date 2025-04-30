<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppGuideResource\Pages;
use App\Filament\Resources\AppGuideResource\RelationManagers;
use App\Models\AppGuide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AppGuideResource extends Resource
{
    protected static ?string $model = AppGuide::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $navigationLabel = 'App Guide Steps';
protected static ?string $navigationGroup = 'Configuration';


    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('view_title')
                ->label('View Title')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->required(),

            Forms\Components\TextInput::make('order')
                ->label('Order')
                ->numeric()
                ->default(0),

            Forms\Components\FileUpload::make('image_path')
                ->label('Image')
                ->directory('app-guides')
                ->image()
                ->nullable(),
        ]);
}


public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('view_title')
                ->label('View')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('description')
                ->limit(50)
                ->wrap()
                ->label('Description'),

            Tables\Columns\TextColumn::make('order')
                ->sortable(),

            Tables\Columns\ImageColumn::make('image_path')
                ->label('Image'),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Created')
                ->dateTime('M d, Y'),
        ])
        ->filters([
            //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppGuides::route('/'),
            'create' => Pages\CreateAppGuide::route('/create'),
            'edit' => Pages\EditAppGuide::route('/{record}/edit'),
        ];
    }
}

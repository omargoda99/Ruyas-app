<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DreamResource\Pages;
use App\Filament\Resources\DreamResource\RelationManagers;
use App\Models\Dream;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DreamResource extends Resource
{
    protected static ?string $model = Dream::class;

    protected static ?string $navigationIcon = 'heroicon-o-moon';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->rows(5)
                ->nullable(),

            Forms\Components\Toggle::make('is_shared')
                ->label('Shared?'),

            Forms\Components\Toggle::make('is_explained')
                ->label('Explained?'),

            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name') // Assuming `User` has `name`
                ->searchable()
                ->required(),
        ]);
}


public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('user.name')->label('User'),
            Tables\Columns\IconColumn::make('is_shared')->boolean(),
            Tables\Columns\IconColumn::make('is_explained')->boolean(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->filters([
            Tables\Filters\TernaryFilter::make('is_explained')->label('Explained'),
            Tables\Filters\TernaryFilter::make('is_shared')->label('Shared'),
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
            'index' => Pages\ListDreams::route('/'),
            'create' => Pages\CreateDream::route('/create'),
            'edit' => Pages\EditDream::route('/{record}/edit'),
        ];
    }
}

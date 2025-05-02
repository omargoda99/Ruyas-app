<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Models\Feedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('interpreter_id')
                    ->label('Interpreter')
                    ->relationship('interpreter', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('interpretation_id')
                    ->label('Interpretation')
                    ->relationship('interpretation', 'title') // assuming interpretation has a title or description
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('dream_id')
                    ->label('Dream')
                    ->relationship('dream', 'title') // assuming dream has a title
                    ->searchable()
                    ->required(),

                Forms\Components\Textarea::make('feedback_text')
                    ->label('Feedback')
                    ->required()
                    ->rows(5),

                Forms\Components\TextInput::make('rating')
                    ->label('Rating (1-5)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('User')->searchable(),
                Tables\Columns\TextColumn::make('interpreter.name')->label('Interpreter')->searchable(),
                Tables\Columns\TextColumn::make('dream.title')->label('Dream')->limit(25),
                Tables\Columns\TextColumn::make('interpretation.title')->label('Interpretation')->limit(25),
                Tables\Columns\TextColumn::make('feedback_text')->label('Feedback')->limit(40)->wrap(),
                Tables\Columns\TextColumn::make('rating')->label('Rating')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Submitted At')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListFeedback::route('/'),
            'create' => Pages\CreateFeedback::route('/create'),
            'edit' => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }
}

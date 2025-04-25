<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterpreterResource\Pages;
use App\Models\Interpreter;
use App\Models\Dream;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InterpreterResource extends Resource
{
    protected static ?string $model = Interpreter::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    // Form schema is empty as no create/edit functionality is needed
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Interpreter Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status') // Use BadgeColumn here
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => ucfirst($state)) // Capitalize the status value (active, inactive, banned)
                    ->colors([
                        'active' => 'success',   // Green for active
                        'inactive' => 'secondary', // Gray for inactive
                        'banned' => 'danger',    // Red for banned
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('dreams_count')
                    ->label('Interpreted Dreams')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn (Interpreter $interpreter) => $interpreter->dreams->count()), // Count related dreams
            ])
            ->filters([
                // Filters for active, inactive, or banned interpreters
                Tables\Filters\Filter::make('active')
                    ->query(fn (Builder $query) => $query->where('status', 'active'))
                    ->label('Active'),
                Tables\Filters\Filter::make('banned')
                    ->query(fn (Builder $query) => $query->where('status', 'banned'))
                    ->label('Banned'),
            ])
            ->actions([
                // Ban action
                Tables\Actions\Action::make('ban')
                    ->label('Ban')
                    ->action(fn (Interpreter $interpreter) => $interpreter->update(['status' => 'banned']))
                    ->color('danger'),
                // Delete action
                Tables\Actions\DeleteAction::make()
                    ->label('Delete Interpreter'),
            ])
            ->bulkActions([
                // Bulk delete action
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Example: If you had dreams related, you can add relation manager here
            // 'dreams' => HasManyRelationManager::make(Dream::class)
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInterpreters::route('/'),
        ];
    }
}

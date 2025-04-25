<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterpretationResource\Pages;
use App\Filament\Resources\InterpretationResource\RelationManagers;
use App\Models\Interpretation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InterpretationResource extends Resource
{
    protected static ?string $model = Interpretation::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListInterpretations::route('/'),
            'create' => Pages\CreateInterpretation::route('/create'),
            'edit' => Pages\EditInterpretation::route('/{record}/edit'),
        ];
    }
}

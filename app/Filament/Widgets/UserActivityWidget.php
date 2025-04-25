<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\User;

class UserActivityWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected int|string|array $rowSpan = 1;

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                User::query()
                    ->select('id', 'name', 'email', 'last_activity_at')
                    ->latest('last_activity_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('name')->label('Name'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('last_activity_at')
                    ->label('Last Activity')
                    ->dateTime(),
            ]);
    }
}

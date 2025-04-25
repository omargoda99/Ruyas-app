<?php

namespace App\Filament\Resources\InterpreterResource\Pages;

use App\Filament\Resources\InterpreterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterpreter extends EditRecord
{
    protected static string $resource = InterpreterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

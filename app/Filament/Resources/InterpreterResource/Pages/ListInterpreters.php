<?php

namespace App\Filament\Resources\InterpreterResource\Pages;

use App\Filament\Resources\InterpreterResource;
use Filament\Resources\Pages\ListRecords;

class ListInterpreters extends ListRecords
{
    protected static string $resource = InterpreterResource::class;

    protected function getHeaderActions(): array
    {
        // Return an empty array to remove the "New Interpreter" button
        return [];
    }
}

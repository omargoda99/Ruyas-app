<?php

namespace App\Filament\Resources\InterpretationResource\Pages;

use App\Filament\Resources\InterpretationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterpretations extends ListRecords
{
    protected static string $resource = InterpretationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

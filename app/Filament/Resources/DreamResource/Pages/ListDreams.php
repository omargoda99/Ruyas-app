<?php

namespace App\Filament\Resources\DreamResource\Pages;

use App\Filament\Resources\DreamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDreams extends ListRecords
{
    protected static string $resource = DreamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

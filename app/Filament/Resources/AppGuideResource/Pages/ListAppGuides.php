<?php

namespace App\Filament\Resources\AppGuideResource\Pages;

use App\Filament\Resources\AppGuideResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppGuides extends ListRecords
{
    protected static string $resource = AppGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\AppGuideResource\Pages;

use App\Filament\Resources\AppGuideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppGuide extends EditRecord
{
    protected static string $resource = AppGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

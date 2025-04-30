<?php

namespace App\Filament\Resources\DreamResource\Pages;

use App\Filament\Resources\DreamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDream extends EditRecord
{
    protected static string $resource = DreamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

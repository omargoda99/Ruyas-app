<?php

namespace App\Filament\Resources\SubscriptionPlansResource\Pages;

use App\Filament\Resources\SubscriptionPlansResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscriptionPlans extends EditRecord
{
    protected static string $resource = SubscriptionPlansResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

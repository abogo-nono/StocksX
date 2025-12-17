<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['tenant_id'] = auth()->user()->tenant_id ?? 1;

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Purchase order created successfully';
    }
}

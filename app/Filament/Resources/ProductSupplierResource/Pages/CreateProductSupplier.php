<?php

namespace App\Filament\Resources\ProductSupplierResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProductSupplierResource;

class CreateProductSupplier extends CreateRecord
{
    protected static string $resource = ProductSupplierResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title("Supplier created")
            ->body("The supplier has been created successfully.")
            ->icon('heroicon-o-queue-list')
            ->color('success');
    }
}

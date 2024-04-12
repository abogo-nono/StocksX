<?php

namespace App\Filament\Resources\ProductSupplierResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProductSupplierResource;

class EditProductSupplier extends EditRecord
{
    protected static string $resource = ProductSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }


    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title("Supplier update")
            ->body("The supplier has been updated successfully.")
            ->icon('heroicon-o-queue-list')
            ->color('success');
    }
}

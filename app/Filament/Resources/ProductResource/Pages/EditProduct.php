<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use App\Models\ProductSupplier;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProductResource;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $category = ProductSupplier::query()->where('id', $data['product_suppliers_id'])->get('category_id')->first()->category_id;
        $data['product_categories_id'] = $category;

        // dd($data);
        return $data;
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title("Product updated")
            ->body("The product has been updated successfully.")
            ->icon('heroicon-o-rectangle-group')
            ->color('success');
    }
}

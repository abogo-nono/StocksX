<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use App\Models\ProductSupplier;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProductResource;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $category = ProductSupplier::query()->where('id', $data['product_suppliers_id'])->get('category_id')->first()->category_id;
        $data['product_categories_id'] = $category;

        // dd($data);
        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title("Product created")
            ->body("The product has been created successfully.")
            ->icon('heroicon-o-rectangle-group')
            ->color('success');
    }
}

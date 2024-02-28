<?php

namespace App\Filament\Resources\ProductSupplierResource\Pages;

use App\Filament\Resources\ProductSupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
}

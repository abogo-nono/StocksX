<?php

namespace App\Filament\Resources\ProductSupplierResource\Pages;

use App\Filament\Resources\ProductSupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductSuppliers extends ListRecords
{
    protected static string $resource = ProductSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

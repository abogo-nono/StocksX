<?php

namespace App\Filament\Resources\OrderDetailResource\Pages;

use App\Filament\Resources\OrderDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderDetails extends ListRecords
{
    protected static string $resource = OrderDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions;
use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         OrderResource\Widgets\OrdersChart::class,
    //     ];
    // }
}

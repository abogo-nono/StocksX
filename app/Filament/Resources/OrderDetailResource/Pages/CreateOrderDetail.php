<?php

namespace App\Filament\Resources\OrderDetailResource\Pages;

use App\Filament\Resources\OrderDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderDetail extends CreateRecord
{
    protected static string $resource = OrderDetailResource::class;
}

<?php

namespace App\Filament\Resources\OrderDetailResource\Pages;

use App\Filament\Resources\OrderDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderDetail extends EditRecord
{
    protected static string $resource = OrderDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

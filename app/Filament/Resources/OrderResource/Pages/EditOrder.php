<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Mail\LowStockAlert;
use App\Models\Product;
use App\Models\User;
use App\Support\Orders\OrderStockManager;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * @var array<int, int>
     */
    protected array $originalQuantities = [];

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->originalQuantities = OrderStockManager::originalQuantities($this->record);

        if ($this->record->delivered) {
            Notification::make()
                ->warning()
                ->title('Order cannot be edited')
                ->body('The order has already been delivered and cannot be edited.')
                ->persistent()
                ->send();

            $this->halt();
        }

        $errorMessage = OrderStockManager::validate($this->data['orderProducts'] ?? [], $this->originalQuantities);

        if ($errorMessage) {
            Notification::make()
                ->warning()
                ->title('Unable to update this order')
                ->body($errorMessage)
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    protected function afterSave(): void
    {
        OrderStockManager::sync($this->data['orderProducts'] ?? [], $this->originalQuantities);

        $lowStockProducts = Product::where('quantity', '<=', 10)->get(['name', 'quantity']);

        if ($lowStockProducts->isNotEmpty()) {
            $adminUser = User::find(1, ['name', 'email']);

            $emailData = [
                // 'subject' => 'Low Stocks Alert',
                'products' => $lowStockProducts,
                'user' => $adminUser,
            ];

            Mail::send(new LowStockAlert($emailData));
        }
    }
}

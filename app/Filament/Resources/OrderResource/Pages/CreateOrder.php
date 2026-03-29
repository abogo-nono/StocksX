<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Mail\LowStockAlert;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use App\Support\Orders\OrderStockManager;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected static string $view = 'filament.resources.order-resource.pages.create-order';

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Order created')
            ->body('The Order has been created successfully.')
            ->icon('heroicon-o-document-text')
            ->color('success');
    }

    protected function beforeCreate(): void
    {
        $errorMessage = OrderStockManager::validate($this->data['orderProducts'] ?? []);

        if ($errorMessage) {
            Notification::make()
                ->warning()
                ->title('Unable to complete this sale')
                ->body($errorMessage)
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        OrderStockManager::sync($this->data['orderProducts'] ?? []);

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

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Complete sale');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Save and start next sale');
    }

    public function getCartItems(): array
    {
        $items = collect(data_get($this->data, 'orderProducts', []))
            ->filter(fn (array $item): bool => filled($item['product_id'] ?? null))
            ->values();

        $products = Product::query()
            ->whereIn('id', $items->pluck('product_id'))
            ->get()
            ->keyBy('id');

        return $items
            ->map(function (array $item) use ($products): array {
                $product = $products->get($item['product_id']);
                $quantity = (int) ($item['quantity'] ?? 0);
                $price = (float) ($item['price'] ?? $product?->price ?? 0);

                return [
                    'name' => $product?->name ?? 'Unavailable product',
                    'quantity' => $quantity,
                    'price' => $price,
                    'line_total' => $quantity * $price,
                ];
            })
            ->all();
    }

    public function getSelectedClient(): ?Client
    {
        $clientId = data_get($this->data, 'client_id');

        if (! $clientId) {
            return null;
        }

        return Client::find($clientId);
    }

    public function getCartTotal(): float
    {
        return collect($this->getCartItems())->sum('line_total');
    }

    public function getCartQuantity(): int
    {
        return (int) collect($this->getCartItems())->sum('quantity');
    }
}

<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Product;
use App\Mail\LowStockAlert;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\OrderResource;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

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
        if ($this->data['delivered']) {
            Notification::make()
                ->warning()
                ->title("Order cannot be edited")
                ->body('The order has already been delivered and cannot be edited.')
                ->persistent()
                ->send();

            $this->halt();
        }

        foreach ($this->data['orderProducts'] as $order) {
            $product = Product::find($order['product_id']);

            if (!$product) {
                Notification::make()
                    ->error()
                    ->title("Product not found")
                    ->body('The product with ID ' . $order['product_id'] . ' does not exist.')
                    ->persistent()
                    ->send();

                $this->halt();
            }

            $originalOrder = $this->record->orderProducts->firstWhere('product_id', $order['product_id']);
            $originalQuantity = $originalOrder ? $originalOrder->quantity : 0;

            $availableQuantity = $product->quantity + $originalQuantity;

            if ($availableQuantity < $order['quantity']) {
                Notification::make()
                    ->warning()
                    ->title("Insufficient stock")
                    ->body('The quantity needed for the product ' . $product->name . ' is not available. Available quantity is: ' . $availableQuantity)
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        foreach ($this->data['orderProducts'] as $order) {
            $product = Product::find($order['product_id']);

            if ($product) {
                $originalOrder = $this->record->orderProducts->firstWhere('product_id', $order['product_id']);
                $originalQuantity = $originalOrder ? $originalOrder->quantity : 0;

                if ($order['quantity'] > $originalQuantity) {
                    // Decrease stock if the new quantity is greater than the original
                    $product->decrement('quantity', $order['quantity'] - $originalQuantity);
                } elseif ($order['quantity'] < $originalQuantity) {
                    // Increase stock if the new quantity is less than the original
                    $product->increment('quantity', $originalQuantity - $order['quantity']);
                }
            }
        }
    }

    protected function afterSave(): void
    {
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

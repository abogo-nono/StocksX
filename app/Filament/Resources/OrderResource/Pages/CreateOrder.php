<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Models\Product;
use Filament\Notifications\Notification;
use App\Filament\Resources\OrderResource;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title("Order created")
            ->body("The Order has been created successfully.")
            ->icon('heroicon-o-document-text')
            ->color('success');
    }

    protected function beforeCreate(): void
    {
        // dd($this->data['orderProducts']);
        foreach ($this->data['orderProducts'] as $order) {
            $product = Product::find($order['product_id']);

            if ($product->quantity - 10 < $order['quantity']) {
                Notification::make()
                ->warning()
                ->title("Product out of stocks")
                ->body('The quantity needed for the product ' . $product->name . ' is not available. Available quantity is: ' . $product->quantity - 10)
                ->persistent()
                ->send();

                $this->halt();
            }
        }

        $products = Product::find($this->data['orderProducts']);

        foreach ($products as $product) {
            foreach ($this->data['orderProducts'] as $ordered_product) {
                $product->quantity -= $ordered_product['quantity'];
                $product->save();
                // dd($product->quantity, $ordered_product['quantity']);
            }
            // $product->quantity = $products->quantity -
        }

        // dd($this->data['orderProducts'], $products);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}

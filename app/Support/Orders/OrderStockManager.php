<?php

namespace App\Support\Orders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;

class OrderStockManager
{
    public static function validate(array $items, ?array $originalQuantities = null): ?string
    {
        $submittedQuantities = self::submittedQuantities($items);
        $originalQuantities ??= [];

        foreach ($submittedQuantities as $productId => $quantity) {
            $product = Product::find($productId);

            if (! $product) {
                return "The selected product #{$productId} no longer exists.";
            }

            $availableQuantity = $product->quantity + ($originalQuantities[$productId] ?? 0);

            if ($availableQuantity < $quantity) {
                return "Insufficient stock for {$product->name}. Available quantity is {$availableQuantity}.";
            }
        }

        return null;
    }

    public static function sync(array $items, ?array $originalQuantities = null): void
    {
        $submittedQuantities = self::submittedQuantities($items);
        $originalQuantities ??= [];
        $productIds = collect(array_keys($submittedQuantities))
            ->merge(array_keys($originalQuantities))
            ->unique()
            ->values();

        Product::whereIn('id', $productIds)
            ->get()
            ->each(function (Product $product) use ($submittedQuantities, $originalQuantities): void {
                $newQuantity = $submittedQuantities[$product->id] ?? 0;
                $oldQuantity = $originalQuantities[$product->id] ?? 0;
                $difference = $oldQuantity - $newQuantity;

                if ($difference > 0) {
                    $product->increment('quantity', $difference);
                }

                if ($difference < 0) {
                    $product->decrement('quantity', abs($difference));
                }
            });
    }

    /**
     * @return array<int, int>
     */
    protected static function submittedQuantities(array $items): array
    {
        return collect($items)
            ->filter(fn (array $item): bool => filled($item['product_id'] ?? null) && filled($item['quantity'] ?? null))
            ->mapWithKeys(fn (array $item): array => [
                (int) $item['product_id'] => (int) $item['quantity'],
            ])
            ->all();
    }

    /**
     * @return array<int, int>
     */
    public static function originalQuantities(?Order $order): array
    {
        if (! $order) {
            return [];
        }

        return $order->orderProducts()
            ->get(['product_id', 'quantity'])
            ->mapWithKeys(fn ($item): array => [(int) $item->product_id => (int) $item->quantity])
            ->all();
    }
}

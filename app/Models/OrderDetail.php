<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_code',
        'quantity',
        'unitcost',
        'total',
        'unit_price',
        'total_price',
        'tax_rate',
        'tax_amount',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unitcost' => 'decimal:2',
        'total' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate totals and sync fields when data changes
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($orderDetail) {
            // Sync unitcost with unit_price if unit_price is set
            if ($orderDetail->unit_price && !$orderDetail->unitcost) {
                $orderDetail->unitcost = $orderDetail->unit_price;
            } elseif ($orderDetail->unitcost && !$orderDetail->unit_price) {
                $orderDetail->unit_price = $orderDetail->unitcost;
            }

            // Calculate subtotal
            $unitPrice = $orderDetail->unit_price ?: $orderDetail->unitcost;
            $subtotal = $orderDetail->quantity * $unitPrice;

            // Calculate tax amount if tax rate is provided
            if ($orderDetail->tax_rate) {
                $orderDetail->tax_amount = ($subtotal * $orderDetail->tax_rate) / 100;
            } else {
                $orderDetail->tax_amount = 0;
            }

            // Calculate total price (subtotal + tax)
            $totalPrice = $subtotal + $orderDetail->tax_amount;
            $orderDetail->total_price = $totalPrice;
            $orderDetail->total = $totalPrice; // Sync with DBML field
        });

        static::saved(function ($orderDetail) {
            // Update order totals when order detail is saved
            $orderDetail->updateOrderTotals();
        });

        static::deleted(function ($orderDetail) {
            // Update order totals when order detail is deleted
            $orderDetail->updateOrderTotals();
        });
    }

    /**
     * Update order totals based on order details
     */
    public function updateOrderTotals()
    {
        if ($this->order) {
            $orderDetails = $this->order->orderDetails;

            $subtotal = $orderDetails->sum('total_price') - $orderDetails->sum('tax_amount');
            $taxAmount = $orderDetails->sum('tax_amount');
            $total = $orderDetails->sum('total_price');

            $this->order->update([
                'sub_total' => $subtotal,
                'vat' => $taxAmount,
                'total' => $total
            ]);
        }
    }

    /**
     * Get line total
     */
    public function getLineSubtotalAttribute(): float
    {
        return $this->quantity * $this->unitcost;
    }

    /**
     * Scope for order details by product
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}

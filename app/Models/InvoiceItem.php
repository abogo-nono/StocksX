<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'product_name',
        'product_code',
        'description',
        'quantity',
        'unit_price',
        'line_total',
        'tax_amount',
        'discount_amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate totals when saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($invoiceItem) {
            // Calculate total price before discount
            $subtotal = $invoiceItem->quantity * $invoiceItem->unit_price;

            // Apply discount
            $afterDiscount = $subtotal - $invoiceItem->discount_amount;

            // Calculate tax amount
            $invoiceItem->tax_amount = ($afterDiscount * $invoiceItem->tax_rate) / 100;

            // Final total price
            $invoiceItem->total_price = $afterDiscount + $invoiceItem->tax_amount;
        });
    }

    /**
     * Get the subtotal before tax and discount
     */
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Get the total after discount but before tax
     */
    public function getAfterDiscountAttribute(): float
    {
        return $this->subtotal - $this->discount_amount;
    }
}

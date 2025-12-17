<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unitcost',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unitcost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Boot function to auto-calculate total
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detail) {
            $detail->total = $detail->quantity * $detail->unitcost;
        });

        static::updating(function ($detail) {
            $detail->total = $detail->quantity * $detail->unitcost;
        });

        static::saved(function ($detail) {
            // Recalculate purchase total when detail is saved
            $detail->purchase->calculateTotals();
        });

        static::deleted(function ($detail) {
            // Recalculate purchase total when detail is deleted
            $detail->purchase->calculateTotals();
        });
    }

    /**
     * Get the purchase that owns the purchase detail.
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the product that owns the purchase detail.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the line total formatted
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2);
    }

    /**
     * Get the unit cost formatted
     */
    public function getFormattedUnitCostAttribute(): string
    {
        return number_format($this->unitcost, 2);
    }
}

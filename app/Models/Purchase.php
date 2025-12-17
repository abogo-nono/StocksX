<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'supplier_id',
        'date',
        'purchase_no',
        'status',
        'total_amount',
        'created_by',
        'updated_by',
        'tenant_id',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Boot function to auto-generate purchase number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            if (!$purchase->purchase_no) {
                $purchase->purchase_no = 'PUR-' . date('Y') . '-' . str_pad(
                    static::whereYear('created_at', date('Y'))->count() + 1,
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    /**
     * Get the supplier that owns the purchase.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(ProductSupplier::class, 'supplier_id');
    }

    /**
     * Get the user who created the purchase.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the purchase.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the tenant that owns the purchase.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the purchase details for the purchase.
     */
    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    /**
     * Calculate total amount from purchase details
     */
    public function calculateTotals()
    {
        $this->total_amount = $this->purchaseDetails->sum('total');
        $this->save();
    }

    /**
     * Update inventory when purchase is received
     */
    public function updateInventory()
    {
        if ($this->status === 'complete') {
            foreach ($this->purchaseDetails as $detail) {
                $product = $detail->product;
                $product->quantity += $detail->quantity;
                $product->buying_price = $detail->unitcost; // Update buying price
                $product->save();
            }
        }
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending purchases
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved purchases
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for completed purchases
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'complete');
    }
}

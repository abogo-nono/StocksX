<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'order_id',
        'invoice_no',
        'status',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'notes',
        'terms_conditions',
        'invoice_template',
        'created_by',
        'sent_at',
        'paid_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Generate invoice number automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = 'INV-' . date('Y') . '-' . str_pad(
                    static::whereYear('created_at', date('Y'))->count() + 1,
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    /**
     * Calculate totals from invoice items
     */
    public function calculateTotals()
    {
        $this->subtotal = $this->invoiceItems->sum('total_price');
        $this->tax_amount = $this->invoiceItems->sum('tax_amount');
        $this->total_amount = $this->subtotal + $this->tax_amount;
        $this->save();
    }

    /**
     * Get balance due
     */
    public function getBalanceDueAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Check if invoice is fully paid
     */
    public function getIsFullyPaidAttribute(): bool
    {
        return $this->balance_due <= 0;
    }

    /**
     * Check if invoice is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < now() && $this->status !== 'paid';
    }

    /**
     * Get total payments received
     */
    public function getTotalPaymentsAttribute(): float
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }
}

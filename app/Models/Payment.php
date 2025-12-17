<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'amount',
        'payment_method',
        'reference_no',
        'payment_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'payment_details' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Generate payment reference number automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->reference_no) {
                $payment->reference_no = 'PAY-' . date('Y') . '-' . str_pad(
                    static::whereYear('created_at', date('Y'))->count() + 1,
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($payment) {
            // Update invoice paid amount when payment is created
            $payment->updateInvoicePaidAmount();
        });
    }

    /**
     * Update the invoice paid amount
     */
    public function updateInvoicePaidAmount()
    {
        if ($this->invoice) {
            $totalPaid = $this->invoice->payments()->sum('amount');
            $this->invoice->update([
                'paid_amount' => $totalPaid,
                'remaining_amount' => $this->invoice->total_amount - $totalPaid
            ]);

            // Mark invoice as paid if fully paid
            if ($this->invoice->remaining_amount <= 0) {
                $this->invoice->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);
            }
        }
    }

    /**
     * Scope for payments by method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope for completed payments
     * In this system, all payments are considered completed when created
     */
    public function scopeCompleted($query)
    {
        return $query; // All payments are completed by default
    }

    /**
     * Scope for payments in date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Get formatted payment method
     */
    public function getFormattedMethodAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->payment_method));
    }
}

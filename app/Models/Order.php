<?php

namespace App\Models;

use App\Models\OrderProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_name',
        'client_phone',
        'client_address',
        'total',
        'delivered',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class)
    //         ->withPivot('quantity', 'price')
    //         ->withTimestamps();
    // }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

}

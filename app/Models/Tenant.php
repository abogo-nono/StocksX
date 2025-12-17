<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model implements HasName
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'logo',
        'main_color',
        'address',
    ];

    protected $casts = [
        'main_color' => 'string',
    ];

    public function getFilamentName(): string
    {
        return $this->name;
    }

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // public function settings(): HasMany
    // {
    //     return $this->hasMany(TenantSetting::class);
    // }
}

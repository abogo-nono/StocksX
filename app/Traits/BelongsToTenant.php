<?php

namespace App\Traits;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenant = Filament::getTenant();

            if ($tenant) {
                $builder->where('tenant_id', $tenant->id);
            }
        });

        static::creating(function (Model $model) {
            $tenant = Filament::getTenant();

            if ($tenant && !$model->tenant_id) {
                $model->tenant_id = $tenant->id;
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}

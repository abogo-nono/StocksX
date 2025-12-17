<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Filament::getTenant();

        if ($tenant) {
            // Apply tenant scoping to models that need it
            $this->applyTenantScoping($tenant);
        }

        return $next($request);
    }

    protected function applyTenantScoping($tenant)
    {
        // Add global scopes for tenant-aware models
        $tenantAwareModels = [
            \App\Models\Product::class,
            \App\Models\Customer::class,
            \App\Models\Order::class,
            \App\Models\Invoice::class,
            \App\Models\Payment::class,
            \App\Models\ProductCategory::class,
            \App\Models\ProductSupplier::class,
            \App\Models\Unit::class,
        ];

        foreach ($tenantAwareModels as $model) {
            if (class_exists($model)) {
                $model::addGlobalScope('tenant', function ($query) use ($tenant) {
                    $query->where('tenant_id', $tenant->id);
                });
            }
        }
    }
}

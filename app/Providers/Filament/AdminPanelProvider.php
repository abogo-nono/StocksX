<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Resources\OrderResource\Widgets\OrdersChart;
use App\Filament\Resources\UserResource\Widgets\UserOverview;
use App\Filament\Widgets\FinancialOverview;
use App\Filament\Widgets\SalesChart;
use App\Filament\Widgets\RecentOrders;
use App\Filament\Widgets\TopProducts;
use App\Filament\Widgets\LowStockAlert;
use App\Filament\Widgets\InvoicesOverview;
use App\Filament\Widgets\PaymentsChart;
use App\Filament\Widgets\OutstandingInvoices;
use App\Models\Tenant;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('stocks-manager')
            ->login()
            ->profile()
            ->registration()
            ->emailVerification()
            ->passwordReset()
            ->tenant(Tenant::class)
            ->tenantRegistration(\App\Filament\Pages\Tenancy\RegisterTenant::class)
            ->tenantProfile(\App\Filament\Pages\Tenancy\EditTenantProfile::class)
            ->colors([
                'primary' => Color::Cyan,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Overview Widgets (Row 1)
                FinancialOverview::class,
                InvoicesOverview::class,

                // Charts (Row 2)
                SalesChart::class,
                PaymentsChart::class,

                // Data Tables (Row 3)
                RecentOrders::class,
                OutstandingInvoices::class,

                // Additional Widgets (Row 4)
                TopProducts::class,
                LowStockAlert::class,
                UserOverview::class,
                OrdersChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\TenantMiddleware::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->navigationGroups([
                'Sales Management',
                'Stocks Management',
                'Customer Management',
                'Financial Management',
                'Users Management',
                'System Management'
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->brandLogo(asset('images/logo.png'))
            ->brandName('Stocks X')
            ->favicon(asset(asset('images/logo.png')))
            ->spa();
    }
}

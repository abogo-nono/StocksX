<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class FinancialOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calculate financial metrics
        $totalRevenue = Payment::completed()->sum('amount');
        $monthlyRevenue = Payment::completed()
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $pendingInvoices = Invoice::where('status', '!=', 'paid')->sum('total_amount');
        $overdueInvoices = Invoice::where('due_date', '<', now())
            ->where('status', '!=', 'paid')
            ->count();

        $lowStockProducts = Product::whereColumn('quantity', '<=', 'quantity_alert')->count();
        $totalProducts = Product::count();

        $outstandingAmount = Invoice::where('status', '!=', 'paid')->sum('remaining_amount');
        $completedPayments = Payment::completed()
            ->whereMonth('payment_date', now()->month)
            ->count();

        return [
            Stat::make('Total Revenue', 'XFA ' . Number::format($totalRevenue, 2))
                ->description('All-time revenue')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Monthly Revenue', 'XFA ' . Number::format($monthlyRevenue, 2))
                ->description('Current month revenue')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),

            Stat::make('Pending Invoices', 'XFA ' . Number::format($pendingInvoices, 2))
                ->description($overdueInvoices . ' overdue')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueInvoices > 0 ? 'danger' : 'warning'),

            Stat::make('Low Stock Alert', $lowStockProducts)
                ->description('Out of ' . $totalProducts . ' products')
                ->descriptionIcon('heroicon-m-archive-box-x-mark')
                ->color($lowStockProducts > 0 ? 'danger' : 'success'),

            Stat::make('Outstanding Amount', 'XFA ' . Number::format($outstandingAmount, 2))
                ->description('Unpaid invoice amounts')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Monthly Payments', $completedPayments)
                ->description('Completed this month')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}

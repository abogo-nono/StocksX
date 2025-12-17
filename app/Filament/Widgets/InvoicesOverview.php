<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoicesOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        $totalInvoices = Invoice::count();
        $totalAmount = Invoice::sum('total_amount');
        $paidAmount = Invoice::sum('paid_amount');
        $outstandingAmount = $totalAmount - $paidAmount;
        $overdueInvoices = Invoice::where('due_date', '<', now())
            ->where('status', '!=', 'paid')
            ->count();

        return [
            Stat::make('Total Invoices', $totalInvoices)
                ->description('All time invoices')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Total Amount', '$' . number_format($totalAmount, 2))
                ->description('Total invoice value')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Outstanding Amount', '$' . number_format($outstandingAmount, 2))
                ->description('Unpaid invoices')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($outstandingAmount > 0 ? 'warning' : 'success'),

            Stat::make('Overdue Invoices', $overdueInvoices)
                ->description('Past due date')
                ->descriptionIcon('heroicon-m-clock')
                ->color($overdueInvoices > 0 ? 'danger' : 'success'),
        ];
    }
}

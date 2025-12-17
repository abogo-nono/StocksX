<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PaymentsChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Payments';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $payments = Payment::selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
            ->whereYear('payment_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $data = [];
        $labels = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = $months[$i];
            $payment = $payments->firstWhere('month', $i);
            $data[] = $payment ? $payment->total : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Payments Received',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

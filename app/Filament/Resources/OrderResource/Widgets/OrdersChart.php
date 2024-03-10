<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use Carbon\Carbon;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        //current year
        $year = Carbon::now()->year;
        //variable to store each order count as array.
        $new_orders_count = [];
        //Looping through the month array to get count for each month in the provided year
        for ($i = 1; $i <= 12; $i++) {
            $new_orders_count[] = Order::whereYear('updated_at', $year)
                ->whereMonth('updated_at', $i)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders Passed',
                    'data' => $new_orders_count,
                    'fill' => true,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];

        $tabs['all'] = Tab::make('All')
            ->badge(Order::count());

        $tabs['today'] = Tab::make('Today')
            ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('created_at', now()->toDateString()))
            ->badge(Order::whereDate('created_at', now()->toDateString())->count());

        $tabs['yesterday'] = Tab::make('Yesterday')
            ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('created_at', now()->subDay()->toDateString()))
            ->badge(Order::whereDate('created_at', now()->subDay()->toDateString())->count());

        $tabs['this_week'] = Tab::make('This Week')
            ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
            ->badge(Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count());

        $tabs['this_month'] = Tab::make('This Month')
            ->modifyQueryUsing(fn(Builder $query) => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year))
            ->badge(Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count());

        $tabs['last_month'] = Tab::make('Last Month')
            ->modifyQueryUsing(fn(Builder $query) => $query->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year))
            ->badge(Order::whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year)->count());

        $tabs['this_year'] = Tab::make('This Year')
            ->modifyQueryUsing(fn(Builder $query) => $query->whereYear('created_at', now()->year))
            ->badge(Order::whereYear('created_at', now()->year)->count());

        $tabs['last_year'] = Tab::make('Last Year')
            ->modifyQueryUsing(fn(Builder $query) => $query->whereYear('created_at', now()->subYear()->year))
            ->badge(Order::whereYear('created_at', now()->subYear()->year)->count());

        return $tabs;
    }
}

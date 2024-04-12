<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use App\Models\Product;
use App\Models\ProductSupplier;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];

        if (auth()->user()->hasRole("super_admin")) {
            $suppliers = ProductSupplier::all();

            $tabs['all'] = Tab::make('All suppliers')
                ->badge(Product::count());

            foreach ($suppliers as $supplier) {
                $tabs[$supplier->name] = Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('product_suppliers_id', $supplier->id))
                    ->badge(Product::where('product_suppliers_id', $supplier->id)->count());
            }

        }

        return $tabs;
    }
}

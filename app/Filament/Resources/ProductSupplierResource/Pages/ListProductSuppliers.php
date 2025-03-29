<?php

namespace App\Filament\Resources\ProductSupplierResource\Pages;

use App\Filament\Resources\ProductSupplierResource;
use App\Models\ProductCategory;
use App\Models\ProductSupplier;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProductSuppliers extends ListRecords
{
    protected static string $resource = ProductSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $categories = ProductCategory::all();

        $tabs = [];

        $tabs['all'] = Tab::make('All suppliers')
            ->badge(ProductSupplier::count());

        foreach ($categories as $category) {
            $tabs[$category->title] = Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('category_id', $category->id))
                ->badge(ProductSupplier::where('category_id', $category->id)->count());
        }

        return $tabs;

        // return [
        //     'all' => Tab::make('All suppliers'),
        //     'test' => Tab::make('test')
        //         ->modifyQueryUsing(fn (Builder $query) => $query->where('category', 2)),

        // ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\InvoiceItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopProducts extends BaseWidget
{
    protected static ?string $heading = 'Top Selling Products';

    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->withCount(['orderDetails as orders_count', 'invoiceItems as invoices_count'])
                    ->withSum('orderDetails as total_sold', 'quantity')
                    ->withSum('invoiceItems as total_invoiced', 'quantity')
                    ->having('total_sold', '>', 0)
                    ->orHaving('total_invoiced', '>', 0)
                    ->orderByDesc('total_sold')
                    ->orderByDesc('total_invoiced')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->square()
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('product_code')
                    ->label('Code')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('total_sold')
                    ->label('Sold')
                    ->numeric()
                    ->placeholder('0')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('total_invoiced')
                    ->label('Invoiced')
                    ->numeric()
                    ->placeholder('0')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Stock')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state <= 5 ? 'danger' : ($state <= 10 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('selling_price')
                    ->money('XFA')
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', $record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlert extends BaseWidget
{
    protected static ?string $heading = 'Low Stock Alert';

    protected static ?int $sort = 8;

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->whereColumn('quantity', '<=', 'quantity_alert')
                    ->with(['supplier', 'category', 'unit', 'tenant'])
                    ->orderBy('quantity')
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
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Current Stock')
                    ->numeric()
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('quantity_alert')
                    ->label('Min Level')
                    ->numeric()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('unit.short_code')
                    ->label('Unit')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->limit(15)
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('restock')
                    ->label('Restock')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('No Low Stock Items')
            ->emptyStateDescription('All products are above minimum stock level.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OutstandingInvoices extends BaseWidget
{
    protected static ?string $heading = 'Outstanding Invoices';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->where('status', '!=', 'paid')
                    ->where('remaining_amount', '>', 0)
                    ->orderBy('due_date', 'asc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('invoice_no')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(fn (Invoice $record): string => $record->due_date < now() ? 'danger' : 'warning'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('remaining_amount')
                    ->label('Outstanding')
                    ->money()
                    ->sortable()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Invoice $record): string => route('filament.admin.resources.invoices.view', $record)),
            ]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Payments';

    protected static ?string $navigationGroup = 'Financial Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Details')
                    ->description('Basic payment information')
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->label('Tenant')
                            ->relationship(name: 'tenant', titleAttribute: 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->default(1),
                        Forms\Components\Select::make('invoice_id')
                            ->label('Invoice')
                            ->relationship(
                                name: 'invoice',
                                titleAttribute: 'invoice_number',
                                modifyQueryUsing: fn (Builder $query) => $query->where('status', '!=', 'paid')
                            )
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->nullable()
                            ->createOptionForm([
                                Forms\Components\Select::make('customer_id')
                                    ->label('Customer')
                                    ->relationship(name: 'customer', titleAttribute: 'name')
                                    ->required(),
                                Forms\Components\TextInput::make('total_amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('XFA'),
                            ]),
                        Forms\Components\Select::make('order_id')
                            ->label('Order')
                            ->relationship(name: 'order', titleAttribute: 'order_number')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->nullable(),
                        Forms\Components\TextInput::make('payment_number')
                            ->label('Payment Number')
                            ->maxLength(50)
                            ->unique('payments', ignoreRecord: true)
                            ->placeholder('Leave empty for auto-generation'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Amount & Method')
                    ->description('Payment amount and method details')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('XFA')
                            ->step(0.01),
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'card' => 'Credit/Debit Card',
                                'bank_transfer' => 'Bank Transfer',
                                'check' => 'Check',
                                'mobile_money' => 'Mobile Money',
                                'other' => 'Other',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('reference_no')
                            ->label('Reference Number')
                            ->maxLength(255)
                            ->placeholder('Transaction ID, check number, etc.'),
                        Forms\Components\DatePicker::make('payment_date')
                            ->required()
                            ->default(now()),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Additional Information')
                    ->description('Notes and extra payment details')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->rows(3),
                        Forms\Components\KeyValue::make('payment_details')
                            ->label('Payment Details')
                            ->keyLabel('Detail')
                            ->valueLabel('Value')
                            ->reorderable(false),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')
                    ->label('Payment #')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('XFA')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'card' => 'info',
                        'bank_transfer' => 'warning',
                        'check' => 'gray',
                        'mobile_money' => 'primary',
                        'other' => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference_no')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tenant.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'card' => 'Credit/Debit Card',
                        'bank_transfer' => 'Bank Transfer',
                        'check' => 'Check',
                        'mobile_money' => 'Mobile Money',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('Tenant')
                    ->relationship('tenant', 'name')
                    ->preload(),
                Tables\Filters\Filter::make('payment_date')
                    ->form([
                        Forms\Components\DatePicker::make('payment_from')
                            ->label('Payment Date From'),
                        Forms\Components\DatePicker::make('payment_until')
                            ->label('Payment Date Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['payment_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['payment_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_invoice')
                    ->label('View Invoice')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn ($record) => route('filament.admin.resources.invoices.view', $record->invoice_id))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('payment_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        // Count payments made today
        $todayCount = static::getModel()::whereDate('payment_date', today())->count();
        return $todayCount > 0 ? (string) $todayCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Number of pending payments';
    }
}

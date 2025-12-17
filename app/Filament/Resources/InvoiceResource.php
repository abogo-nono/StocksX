<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Invoices';

    protected static ?string $navigationGroup = 'Financial Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Details')
                    ->description('Basic invoice information')
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->label('Tenant')
                            ->relationship(name: 'tenant', titleAttribute: 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->default(1),
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->relationship(name: 'customer', titleAttribute: 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(20),
                            ]),
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->required()
                            ->maxLength(50)
                            ->unique('invoices', ignoreRecord: true)
                            ->placeholder('Leave empty for auto-generation'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'paid' => 'Paid',
                                'overdue' => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->native(false)
                            ->default('draft'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Dates & Amounts')
                    ->description('Invoice dates and financial information')
                    ->schema([
                        Forms\Components\DatePicker::make('invoice_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('due_date')
                            ->required()
                            ->default(now()->addDays(30)),
                        Forms\Components\TextInput::make('subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('XFA')
                            ->step(0.01)
                            ->default(0),
                        Forms\Components\TextInput::make('tax_amount')
                            ->label('Tax Amount')
                            ->numeric()
                            ->prefix('XFA')
                            ->step(0.01)
                            ->default(0),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->required()
                            ->numeric()
                            ->prefix('XFA')
                            ->step(0.01)
                            ->default(0),
                        Forms\Components\TextInput::make('paid_amount')
                            ->label('Paid Amount')
                            ->numeric()
                            ->prefix('XFA')
                            ->step(0.01)
                            ->default(0),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Additional Information')
                    ->description('Notes and extra details')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->rows(3),
                        Forms\Components\Textarea::make('terms')
                            ->label('Terms & Conditions')
                            ->maxLength(1000)
                            ->rows(3),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'info',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('invoice_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record): string =>
                        $record->due_date < now() && $record->status !== 'paid' ? 'danger' : 'primary'
                    ),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('XFA')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->money('XFA')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance Due')
                    ->money('XFA')
                    ->getStateUsing(fn ($record) => $record->total_amount - $record->paid_amount)
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success'),
                Tables\Columns\TextColumn::make('tenant.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('Tenant')
                    ->relationship('tenant', 'name')
                    ->preload(),
                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('due_date', '<', now())
                              ->where('status', '!=', 'paid')
                    )
                    ->label('Overdue Invoices'),
                Tables\Filters\Filter::make('unpaid')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereColumn('paid_amount', '<', 'total_amount')
                    )
                    ->label('Unpaid Balance'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'paid',
                            'paid_amount' => $record->total_amount,
                        ]);
                    })
                    ->visible(fn ($record) => $record->status !== 'paid'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('invoice_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InvoiceItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $overdueCount = static::getModel()::where('due_date', '<', now())
            ->where('status', '!=', 'paid')
            ->count();
        return $overdueCount > 0 ? (string) $overdueCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Number of overdue invoices';
    }
}

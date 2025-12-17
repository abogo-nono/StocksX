<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Purchase;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\PurchaseResource\Pages;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Stocks Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Purchase Information')
                    ->description('Basic purchase order details')
                    ->schema([
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        DatePicker::make('date')
                            ->label('Purchase Date')
                            ->required()
                            ->default(now()),

                        TextInput::make('purchase_no')
                            ->label('Purchase Number')
                            ->placeholder('Auto-generated if left empty')
                            ->maxLength(255),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'complete' => 'Complete',
                                'cancel' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Purchase Items')
                    ->description('Products to be purchased')
                    ->schema([
                        Repeater::make('purchaseDetails')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            if ($product) {
                                                $set('unitcost', $product->buying_price ?? 0);
                                                self::updateTotal($get, $set);
                                            }
                                        }
                                    })
                                    ->native(false),

                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live()
                                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateTotal($get, $set)),

                                TextInput::make('unitcost')
                                    ->label('Unit Cost')
                                    ->numeric()
                                    ->prefix('XFA')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateTotal($get, $set)),

                                TextInput::make('total')
                                    ->label('Total')
                                    ->numeric()
                                    ->prefix('XFA')
                                    ->readOnly()
                                    ->required(),
                            ])
                            ->columns(4)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updatePurchaseTotal($get, $set);
                            })
                            ->deleteAction(
                                fn(Action $action) => $action->after(fn(Get $get, Set $set) => self::updatePurchaseTotal($get, $set)),
                            )
                            ->reorderable(false)
                            ->addActionLabel('Add Product')
                            ->defaultItems(1),
                    ]),

                Section::make('Total Amount')
                    ->description('Purchase total calculation')
                    ->schema([
                        TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->prefix('XFA')
                            ->numeric()
                            ->readOnly()
                            ->required(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('purchase_no')
                    ->label('Purchase No.')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Purchase Date')
                    ->date()
                    ->sortable(),

                SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'complete' => 'Complete',
                        'cancel' => 'Cancelled',
                    ])
                    ->selectablePlaceholder(false)
                    ->afterStateUpdated(function (Purchase $record, string $state) {
                        $record->updated_by = auth()->id();
                        $record->save();

                        // Update inventory if status changed to complete
                        if ($state === 'complete') {
                            $record->updateInventory();
                        }
                    }),

                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('XFA')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'complete' => 'Complete',
                        'cancel' => 'Cancelled',
                    ])
                    ->multiple(),

                SelectFilter::make('supplier')
                    ->relationship('supplier', 'name')
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                ViewAction::make(),
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'view' => Pages\ViewPurchase::route('/{record}'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->purchase_no;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'purchase_no',
            'supplier.name',
            'total_amount',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Supplier' => $record->supplier->name,
            'Date' => $record->date->format('M d, Y'),
            'Status' => ucfirst($record->status),
            'Total' => 'XFA ' . number_format($record->total_amount, 2),
        ];
    }

    /**
     * Update line total for purchase detail
     */
    protected static function updateTotal(Get $get, Set $set): void
    {
        $quantity = (float) $get('quantity') ?: 0;
        $unitcost = (float) $get('unitcost') ?: 0;
        $total = $quantity * $unitcost;

        $set('total', number_format($total, 2, '.', ''));
    }

    /**
     * Update purchase total amount
     */
    protected static function updatePurchaseTotal(Get $get, Set $set): void
    {
        $purchaseDetails = $get('purchaseDetails') ?: [];
        $total = 0;

        foreach ($purchaseDetails as $detail) {
            $total += (float) ($detail['total'] ?? 0);
        }

        $set('total_amount', number_format($total, 2, '.', ''));
    }
}

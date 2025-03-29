<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Order;
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
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use App\Filament\Resources\OrderResource\Pages;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AymanAlhattami\FilamentDateScopesFilter\DateScopeFilter;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Stocks Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Client')
                    ->description('Client details')
                    ->collapsible()
                    ->schema([
                        TextInput::make('client_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('client_phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('client_address')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),

                Section::make('Products')
                    ->description('Ordered products')
                    ->collapsible()
                    ->schema([
                        Repeater::make('orderProducts')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship(name: 'product', titleAttribute: 'name')
                                    ->required()
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('price', $product->price);
                                        }
                                    })
                                    // Disable options that are already selected in other rows
                                    ->disableOptionWhen(function ($value, $state, Get $get) {
                                        return collect($get('../*.product_id'))
                                            ->reject(fn($id) => $id == $state)
                                            ->filter()
                                            ->contains($value);
                                    }),
                                TextInput::make('quantity')
                                    ->default(1)
                                    ->integer()
                                    ->required()
                                    ->minValue(1),
                                TextInput::make('price')
                                    ->numeric()
                                    ->prefix('XFA')
                                    ->readOnly()
                                    ->required(),
                            ])
                            ->columns(3)
                            // Repeatable field is live so that it will trigger the state update on each change
                            ->live()
                            // After adding a new row, we need to update the totals
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            // After deleting a row, we need to update the totals
                            ->deleteAction(
                                fn(Action $action) => $action->after(fn(Get $get, Set $set) => self::updateTotals($get, $set)),
                            )
                            // Disable reordering
                            ->reorderable(false),
                    ]),
                Section::make('Total')
                    ->description('Total to pay')
                    ->collapsible()
                    ->schema([
                        TextInput::make('total')
                            ->prefix('XFA')
                            ->required()
                            ->numeric()
                            ->readOnly()
                            // This enables us to display the subtotal on the edit page load
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            }),
                    ]),
                Section::make('Delivered')
                    ->description('The client had paid and got his products')
                    ->hiddenOn(['create', 'edit'])
                    ->schema([
                        Forms\Components\Toggle::make('delivered')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Cashier')
                    ->hidden(!auth()->user()->hasRole('super_admin')),
                TextColumn::make('client_name')
                    ->searchable(),
                TextColumn::make('client_phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('client_address')
                    ->searchable()
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total')
                    ->money('XFA')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total')
                    ->money('XFA')
                    ->summarize(Sum::make()),
                ToggleColumn::make('delivered'),
            ])
            ->filters([
                TrashedFilter::make(),
                DateScopeFilter::make('created_at'),
            ])
            ->actions([
                ViewAction::make(),
                // EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            // 'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->client_name; // Show client name as title
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'client_name',
            'client_phone',
            'client_address',
            'total',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Phone' => $record->client_phone,
            'Address' => $record->client_address,
            'Total' => $record->total,
            'Delivered' => $record->delivered ? 'Yes' : 'No',
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return OrderResource::getUrl('view', ['record' => $record]);
    }

    public static function getNavigationBadge(): ?string
    {
        $undeliveredOrdersCount = static::getModel()::where('delivered', false)
            ->withoutTrashed()
            ->count();
        return $undeliveredOrdersCount > 0 ? (string) $undeliveredOrdersCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('delivered', false)->count() > 10 ? 'danger' : 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The number of undelivered orders';
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    // / This function updates totals based on the selected products and quantities
    public static function updateTotals(Get $get, Set $set): void
    {
        // Retrieve all selected products and remove empty rows
        $selectedProducts = collect($get('orderProducts'))->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity']));

        // Retrieve prices for all selected products
        $prices = Product::find($selectedProducts->pluck('product_id'))->pluck('price', 'id');

        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
            return $subtotal + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);

        // Update the state with the new values
        // $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('total', number_format($subtotal, 2, '.', ''));
        // $set('total', number_format($subtotal + ($subtotal * ($get('taxes') / 100)), 2, '.', ''));
    }
}

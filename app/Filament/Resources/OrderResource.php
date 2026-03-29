<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use AymanAlhattami\FilamentDateScopesFilter\DateScopeFilter;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Sales Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                    'xl' => 1,
                ])
                    ->schema([
                        Section::make('POS cart')
                            ->description('Build the order quickly. Product prices are pulled from inventory automatically.')
                            ->columnSpanFull()
                            ->schema([
                                Repeater::make('orderProducts')
                                    ->label('')
                                    ->relationship()
                                    ->defaultItems(1)
                                    ->minItems(1)
                                    ->schema([
                                        Select::make('product_id')
                                            ->label('Product')
                                            ->relationship(name: 'product', titleAttribute: 'name')
                                            ->getOptionLabelFromRecordUsing(fn (Product $record): string => "{$record->name} · {$record->quantity} in stock")
                                            ->searchable(['name'])
                                            ->preload()
                                            ->native(false)
                                            ->required()
                                            ->live()
                                            ->columnSpan([
                                                'default' => 1,
                                                'xl' => 4,
                                            ])
                                            ->afterStateUpdated(function ($state, Set $set, Get $get): void {
                                                $product = Product::find($state);
                                                $set('price', $product?->price ?? null);
                                                self::updateTotals($get, $set);
                                            })
                                            ->disableOptionWhen(function ($value, $state, Get $get): bool {
                                                return collect($get('../*.product_id'))
                                                    ->reject(fn ($id) => $id == $state)
                                                    ->filter()
                                                    ->contains($value);
                                            }),
                                        TextInput::make('quantity')
                                            ->integer()
                                            ->default(1)
                                            ->minValue(1)
                                            ->required()
                                            ->live(onBlur: true)
                                            ->suffix('pcs')
                                            ->extraInputAttributes(['class' => 'text-base font-semibold text-center'])
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 2,
                                                'xl' => 2,
                                            ])
                                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotals($get, $set)),
                                        Placeholder::make('unit_price')
                                            ->label('Unit price')
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 2,
                                                'xl' => 1,
                                            ])
                                            ->content(fn (Get $get): string => self::formatMoney((float) ($get('price') ?? 0))),
                                        Placeholder::make('line_total')
                                            ->label('Line total')
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 2,
                                                'xl' => 1,
                                            ])
                                            ->content(function (Get $get): string {
                                                $price = (float) ($get('price') ?? 0);
                                                $quantity = (int) ($get('quantity') ?? 0);

                                                return self::formatMoney($price * $quantity);
                                            }),
                                        Hidden::make('price')
                                            ->default(0)
                                            ->required(),
                                    ])
                                    ->columns([
                                        'default' => 1,
                                        'md' => 4,
                                        'xl' => 8,
                                    ])
                                    ->addActionLabel('Add product')
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotals($get, $set))
                                    ->deleteAction(
                                        fn (Action $action) => $action->after(fn (Get $get, Set $set) => self::updateTotals($get, $set)),
                                    )
                                    ->reorderable(false),
                            ]),
                        Grid::make(1)
                            ->columnSpanFull()
                            ->schema([
                                Section::make('Client')
                                    ->description('Pick an existing customer or leave this empty for a walk-in sale.')
                                    ->schema([
                                        Select::make('client_id')
                                            ->label('Client')
                                            ->relationship(name: 'client', titleAttribute: 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'))
                                            ->searchable(['name', 'phone', 'address'])
                                            ->getOptionLabelFromRecordUsing(function (Client $record): string {
                                                $phone = $record->phone ? " · {$record->phone}" : '';

                                                return "{$record->name}{$phone}";
                                            })
                                            ->preload()
                                            ->native(false)
                                            ->placeholder('Walk-in customer')
                                            ->createOptionForm(ClientResource::clientFormSchema())
                                            ->createOptionUsing(fn (array $data): int => Client::create($data)->getKey())
                                            ->helperText('The order can be saved without a client.')
                                            ->columnSpanFull(),
                                        Hidden::make('total')
                                            ->default(0)
                                            ->visibleOn('create')
                                            ->dehydrated(),
                                    ]),
                                Section::make('Order summary')
                                    ->hiddenOn('create')
                                    ->schema([
                                        Placeholder::make('items_count')
                                            ->label('Items')
                                            ->content(function (Get $get): string {
                                                return (string) collect($get('orderProducts'))
                                                    ->filter(fn ($item) => filled($item['product_id'] ?? null))
                                                    ->count();
                                            }),
                                        Placeholder::make('cashier')
                                            ->content(fn (): string => auth()->user()->name),
                                        TextInput::make('total')
                                            ->prefix('XFA')
                                            ->numeric()
                                            ->readOnly()
                                            ->required()
                                            ->afterStateHydrated(fn (Get $get, Set $set) => self::updateTotals($get, $set)),
                                        Forms\Components\Toggle::make('delivered')
                                            ->helperText('Mark the order as handed over to the client.')
                                            ->visibleOn('edit'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order')
                    ->prefix('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_display_name')
                    ->label('Client')
                    ->state(fn (Order $record): string => $record->client?->name ?? 'Walk-in customer')
                    ->description(fn (Order $record): ?string => $record->client?->phone)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('client', function (Builder $clientQuery) use ($search): void {
                            $clientQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere('address', 'like', "%{$search}%");
                        });
                    })
                    ->sortable()
                    ->placeholder('Walk-in customer'),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->state(fn (Order $record): int => $record->orderProducts()->count())
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('XFA')
                    ->sortable(),
                Tables\Columns\IconColumn::make('delivered')
                    ->boolean()
                    ->label('Delivered'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cashier')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden(! auth()->user()?->hasRole('super_admin')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                DateScopeFilter::make('created_at'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Order details')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('Order')
                            ->prefix('#')
                            ->weight('semibold'),
                        Infolists\Components\TextEntry::make('client.name')
                            ->label('Client')
                            ->default('Walk-in customer'),
                        Infolists\Components\TextEntry::make('client.phone')
                            ->label('Phone')
                            ->placeholder('No phone'),
                        Infolists\Components\TextEntry::make('client.address')
                            ->label('Address')
                            ->placeholder('No address')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Cashier'),
                        Infolists\Components\TextEntry::make('total')
                            ->money('XFA'),
                        Infolists\Components\IconEntry::make('delivered')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make('Items')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('orderProducts')
                            ->hiddenLabel()
                            ->schema([
                                Infolists\Components\TextEntry::make('product.name')
                                    ->weight('semibold'),
                                Infolists\Components\TextEntry::make('quantity'),
                                Infolists\Components\TextEntry::make('price')
                                    ->money('XFA')
                                    ->label('Unit price'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->client?->name ?? "Walk-in order #{$record->id}";
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'client.name',
            'client.phone',
            'client.address',
            'total',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Phone' => $record->client?->phone ?: 'No phone',
            'Address' => $record->client?->address ?: 'No address',
            'Total' => self::formatMoney((float) $record->total),
            'Delivered' => $record->delivered ? 'Yes' : 'No',
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('view', ['record' => $record]);
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
        return 'Undelivered orders';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'client',
                'user',
                'orderProducts.product',
            ])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $selectedProducts = collect($get('orderProducts'))
            ->filter(fn ($item) => filled($item['product_id'] ?? null) && filled($item['quantity'] ?? null));

        $prices = Product::query()
            ->whereIn('id', $selectedProducts->pluck('product_id'))
            ->pluck('price', 'id');

        $total = $selectedProducts->reduce(function (float $subtotal, array $product) use ($prices): float {
            return $subtotal + (((float) ($prices[$product['product_id']] ?? 0)) * ((int) $product['quantity']));
        }, 0);

        $set('total', number_format($total, 2, '.', ''));
    }

    public static function formatMoney(float $amount): string
    {
        return number_format($amount, 2).' XFA';
    }
}

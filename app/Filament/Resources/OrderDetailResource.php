<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderDetailResource\Pages;
use App\Filament\Resources\OrderDetailResource\RelationManagers;
use App\Models\OrderDetail;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderDetailResource extends Resource
{
    protected static ?string $model = OrderDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Order Items';

    protected static ?string $navigationGroup = 'Sales Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Item Details')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->label('Order')
                            ->relationship('order', 'order_no')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('product_name', $product->name);
                                        $set('product_code', $product->code);
                                        $set('unit_price', $product->selling_price);
                                    }
                                }
                            })
                            ->required(),

                        Forms\Components\TextInput::make('product_name')
                            ->label('Product Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('product_code')
                            ->label('Product Code')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $unitPrice = (float) $get('unit_price');
                                $quantity = (int) $state;
                                $taxAmount = (float) $get('tax_amount') ?: 0;
                                $totalPrice = ($unitPrice * $quantity) + $taxAmount;
                                $set('total_price', $totalPrice);
                            })
                            ->required(),

                        Forms\Components\TextInput::make('unit_price')
                            ->label('Unit Price')
                            ->numeric()
                            ->prefix('$')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $unitPrice = (float) $state;
                                $quantity = (int) $get('quantity') ?: 1;
                                $taxAmount = (float) $get('tax_amount') ?: 0;
                                $totalPrice = ($unitPrice * $quantity) + $taxAmount;
                                $set('total_price', $totalPrice);
                            })
                            ->required(),

                        Forms\Components\TextInput::make('tax_rate')
                            ->label('Tax Rate (%)')
                            ->numeric()
                            ->suffix('%')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $taxRate = (float) $state ?: 0;
                                $unitPrice = (float) $get('unit_price');
                                $quantity = (int) $get('quantity') ?: 1;
                                $taxAmount = ($unitPrice * $quantity) * ($taxRate / 100);
                                $set('tax_amount', $taxAmount);
                                $totalPrice = ($unitPrice * $quantity) + $taxAmount;
                                $set('total_price', $totalPrice);
                            }),

                        Forms\Components\TextInput::make('tax_amount')
                            ->label('Tax Amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),

                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Price')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_no')
                    ->label('Order No.')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product_code')
                    ->label('Code')
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Unit Price')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tax_amount')
                    ->label('Tax')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order_id')
                    ->label('Order')
                    ->relationship('order', 'order_no')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListOrderDetails::route('/'),
            'create' => Pages\CreateOrderDetail::route('/create'),
            'edit' => Pages\EditOrderDetail::route('/{record}/edit'),
        ];
    }
}

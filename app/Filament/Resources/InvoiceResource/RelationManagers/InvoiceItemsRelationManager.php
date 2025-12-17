<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'invoiceItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->relationship(name: 'product', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $product = Product::find($state);
                        if ($product) {
                            $set('product_name', $product->name);
                            $set('product_code', $product->product_code);
                            $set('unit_price', $product->selling_price);
                            $set('tax_rate', $product->tax);
                        }
                    }),
                Forms\Components\TextInput::make('product_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_code')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(1000)
                    ->rows(2),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $quantity = (float) $state;
                        $unitPrice = (float) $get('unit_price');
                        $discountAmount = (float) $get('discount_amount');
                        $taxRate = (float) $get('tax_rate');

                        $subtotal = $quantity * $unitPrice;
                        $afterDiscount = $subtotal - $discountAmount;
                        $taxAmount = ($afterDiscount * $taxRate) / 100;
                        $totalPrice = $afterDiscount + $taxAmount;

                        $set('total_price', $totalPrice);
                        $set('tax_amount', $taxAmount);
                    }),
                Forms\Components\TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->prefix('XFA')
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $quantity = (float) $get('quantity');
                        $unitPrice = (float) $state;
                        $discountAmount = (float) $get('discount_amount');
                        $taxRate = (float) $get('tax_rate');

                        $subtotal = $quantity * $unitPrice;
                        $afterDiscount = $subtotal - $discountAmount;
                        $taxAmount = ($afterDiscount * $taxRate) / 100;
                        $totalPrice = $afterDiscount + $taxAmount;

                        $set('total_price', $totalPrice);
                        $set('tax_amount', $taxAmount);
                    }),
                Forms\Components\TextInput::make('discount_amount')
                    ->numeric()
                    ->prefix('XFA')
                    ->step(0.01)
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $quantity = (float) $get('quantity');
                        $unitPrice = (float) $get('unit_price');
                        $discountAmount = (float) $state;
                        $taxRate = (float) $get('tax_rate');

                        $subtotal = $quantity * $unitPrice;
                        $afterDiscount = $subtotal - $discountAmount;
                        $taxAmount = ($afterDiscount * $taxRate) / 100;
                        $totalPrice = $afterDiscount + $taxAmount;

                        $set('total_price', $totalPrice);
                        $set('tax_amount', $taxAmount);
                    }),
                Forms\Components\TextInput::make('tax_rate')
                    ->numeric()
                    ->suffix('%')
                    ->step(0.01)
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $quantity = (float) $get('quantity');
                        $unitPrice = (float) $get('unit_price');
                        $discountAmount = (float) $get('discount_amount');
                        $taxRate = (float) $state;

                        $subtotal = $quantity * $unitPrice;
                        $afterDiscount = $subtotal - $discountAmount;
                        $taxAmount = ($afterDiscount * $taxRate) / 100;
                        $totalPrice = $afterDiscount + $taxAmount;

                        $set('total_price', $totalPrice);
                        $set('tax_amount', $taxAmount);
                    }),
                Forms\Components\TextInput::make('total_price')
                    ->numeric()
                    ->prefix('XFA')
                    ->step(0.01)
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('product_code')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('XFA'),
                Tables\Columns\TextColumn::make('discount_amount')
                    ->money('XFA')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tax_rate')
                    ->suffix('%')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('XFA')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

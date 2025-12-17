<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Unit;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Stocks Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Tenant & Supplier')
                    ->description('Product tenant, supplier and category')
                    ->schema([
                        Select::make('tenant_id')
                            ->label('Tenant')
                            ->relationship(name: 'tenant', titleAttribute: 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->default(1),
                        Select::make('product_suppliers_id')
                            ->label('Supplier Name')
                            ->relationship(name: 'supplier', titleAttribute: 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->required(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->hiddenOn('view'),
                Section::make('Product Details')
                    ->description('Product information and specifications')
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->columnSpanFull()
                            ->imageEditor()
                            ->required(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(debounce: 1000)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique('products', ignoreRecord: true),
                        TextInput::make('product_code')
                            ->label('Product Code')
                            ->maxLength(100)
                            ->unique('products', ignoreRecord: true)
                            ->placeholder('Leave empty for auto-generation'),
                        TextInput::make('barcode')
                            ->maxLength(255)
                            ->unique('products', ignoreRecord: true),
                        Select::make('unit_id')
                            ->label('Unit')
                            ->relationship(name: 'unit', titleAttribute: 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->required(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Section::make('Pricing & Stock')
                    ->description('Price information and stock details')
                    ->schema([
                        TextInput::make('buying_price')
                            ->required()
                            ->numeric()
                            ->prefix('XFA')
                            ->step(0.01),
                        TextInput::make('selling_price')
                            ->required()
                            ->numeric()
                            ->prefix('XFA')
                            ->step(0.01),
                        TextInput::make('price')
                            ->label('Display Price')
                            ->numeric()
                            ->prefix('XFA')
                            ->step(0.01)
                            ->helperText('Used for display if different from selling price'),
                        TextInput::make('tax')
                            ->numeric()
                            ->suffix('%')
                            ->step(0.01)
                            ->default(0),
                        TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('quantity_alert')
                            ->label('Minimum Stock Level')
                            ->numeric()
                            ->default(5)
                            ->helperText('Alert when stock falls below this level'),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_code')
                    ->label('Code')
                    ->searchable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('buying_price')
                    ->money('XFA')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->money('XFA')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Display Price')
                    ->money('XFA')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => $state <= 5 ? 'danger' : ($state <= 10 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('unit.short_code')
                    ->label('Unit')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('tax')
                    ->suffix('%')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tenant.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category.title')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('barcode')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('quantity_alert')
                    ->label('Min Stock')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('slug')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('Tenant')
                    ->relationship('tenant', 'name')
                    ->preload(),
                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'name')
                    ->preload(),
                Tables\Filters\Filter::make('low_stock')
                    ->query(fn (Builder $query): Builder => $query->whereColumn('quantity', '<=', 'quantity_alert'))
                    ->label('Low Stock'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            // 'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }


    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name; // Show name as title
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'product_code',
            'barcode',
            'buying_price',
            'selling_price',
            'quantity',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Code' => $record->product_code,
            'Category' => $record->category?->title,
            'Selling Price' => number_format($record->selling_price, 2) . ' XFA',
            'Quantity' => $record->quantity . ' ' . ($record->unit?->short_code ?? ''),
            'Tenant' => $record->tenant?->name,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $lowStockCount = static::getModel()::whereColumn('quantity', '<=', 'quantity_alert')
            ->withoutTrashed()
            ->count();
        return $lowStockCount > 0 ? (string) $lowStockCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The number of products with low stock';
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Product Image')
                    ->schema([
                        Infolists\Components\ImageEntry::make('image')
                            ->label('')
                    ]),
                Infolists\Components\Section::make('Product Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->color('info')
                            ->weight('semibold'),
                        Infolists\Components\TextEntry::make('product_code')
                            ->label('Product Code')
                            ->color('primary')
                            ->weight('semibold'),
                        Infolists\Components\TextEntry::make('barcode')
                            ->color('info'),
                        Infolists\Components\TextEntry::make('unit.name')
                            ->label('Unit')
                            ->color('info'),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Business Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('tenant.name')
                            ->label('Tenant')
                            ->color('info')
                            ->weight('semibold'),
                        Infolists\Components\TextEntry::make('supplier.name')
                            ->color('info')
                            ->weight('semibold'),
                        Infolists\Components\TextEntry::make('category.title')
                            ->color('info')
                            ->weight('semibold'),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make('Pricing & Stock')
                    ->schema([
                        Infolists\Components\TextEntry::make('buying_price')
                            ->money('XFA')
                            ->color('success')
                            ->weight('semibold'),
                        Infolists\Components\TextEntry::make('selling_price')
                            ->money('XFA')
                            ->color('warning')
                            ->weight('semibold'),
                        Infolists\Components\TextEntry::make('price')
                            ->label('Display Price')
                            ->money('XFA')
                            ->color('info'),
                        Infolists\Components\TextEntry::make('tax')
                            ->suffix('%')
                            ->color('info'),
                        Infolists\Components\TextEntry::make('quantity')
                            ->color('primary')
                            ->weight('semibold'),
                        Infolists\Components\TextEntry::make('quantity_alert')
                            ->label('Minimum Stock Level')
                            ->color('danger'),
                    ])
                    ->columns(3),
            ]);
    }

}

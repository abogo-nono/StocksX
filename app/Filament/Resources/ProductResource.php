<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Product;
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
                Section::make('Supplier')
                    ->description('Product supplier and category')
                    ->schema([
                        Select::make('product_suppliers_id')
                            ->label('Supplier Name')
                            ->relationship(name: 'supplier', titleAttribute: 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->hiddenOn('view'),
                Section::make('Product')
                    ->description('Product details')
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
                    ])
                    ->columns(2)
                    ->collapsible(),
                Section::make('Price')
                    ->description('Price and Quantity')
                    ->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('XFA'),
                        TextInput::make('quantity')
                            ->required()
                            ->numeric(),
                    ])
                    ->columns(2)
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('XFA')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable()
                    ->badge()
                // ->color(fn(string $state): string => match ($state) {
                //     'quantity' > '10' => 'success',
                //     'quantity' < '5' => 'warning',
                //     'quantity' < '10' => 'info'
                // })
                ,
                Tables\Columns\TextColumn::make('supplier.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category.title')
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
            'price',
            'quantity',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Category' => $record->category->title,
            'Price' => $record->price,
            'Quantity' => $record->quantity,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $lowStockCount = static::getModel()::where('quantity', '<=', '10')
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
                Infolists\Components\Section::make([
                    Infolists\Components\ImageEntry::make('image')
                        ->label('')
                ]),
                Infolists\Components\Section::make([
                    Infolists\Components\TextEntry::make('supplier.name')
                        ->color('info')
                        ->weight('semibold'),
                    Infolists\Components\TextEntry::make('category.title')
                        ->color('info')
                        ->weight('semibold'),

                    Infolists\Components\TextEntry::make('name')
                        ->color('info')
                        ->weight('semibold'),
                    Infolists\Components\TextEntry::make('price')
                        ->color('info')
                        ->weight('semibold'),
                    Infolists\Components\TextEntry::make('quantity')
                        ->color('info')
                        ->weight('semibold')
                ]),
            ]);
    }

}

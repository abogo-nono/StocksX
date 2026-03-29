<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Sales Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Client details')
                    ->description('Store reusable customer information for future POS orders.')
                    ->schema(static::clientFormSchema())
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->placeholder('No phone'),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('No address'),
                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Orders')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
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
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Client profile')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->weight('semibold'),
                        Infolists\Components\TextEntry::make('phone')
                            ->placeholder('No phone'),
                        Infolists\Components\TextEntry::make('address')
                            ->placeholder('No address')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('notes')
                            ->placeholder('No notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Activity')
                    ->schema([
                        Infolists\Components\TextEntry::make('orders_count')
                            ->state(fn (Client $record): int => $record->orders()->count())
                            ->label('Orders placed'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('orders')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'phone',
            'address',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Phone' => $record->phone ?: 'No phone',
            'Address' => $record->address ?: 'No address',
        ];
    }

    public static function clientFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('phone')
                ->tel()
                ->maxLength(255),
            Forms\Components\TextInput::make('address')
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('notes')
                ->rows(4)
                ->columnSpanFull(),
        ];
    }
}

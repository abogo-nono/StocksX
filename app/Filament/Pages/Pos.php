<?php

namespace App\Filament\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;

class Pos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'POS';

    protected static ?string $navigationGroup = 'Sales Management';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.pos';

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $this->redirect(OrderResource::getUrl('create'), navigate: true);
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('page_Pos') && OrderResource::canCreate();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->sort(static::getNavigationSort())
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.orders.create'))
                ->url(OrderResource::getUrl('create')),
        ];
    }
}

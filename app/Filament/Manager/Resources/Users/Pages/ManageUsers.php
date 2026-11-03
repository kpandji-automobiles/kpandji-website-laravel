<?php

namespace App\Filament\Manager\Resources\Users\Pages;

use App\Filament\Manager\Resources\Users\UserResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('À valider')
                ->badge(fn (): ?string => UserResource::getNavigationBadge())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_status', 'pending')),
            'refused' => Tab::make('Refusés')->modifyQueryUsing(fn (Builder $query) => $query->where('account_status', 'refused')),
            'active' => Tab::make('Actifs')->modifyQueryUsing(fn (Builder $query) => $query->where('account_status', 'active')),
            'all' => Tab::make('Tous'),
        ];
    }
}

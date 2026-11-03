<?php

namespace App\Filament\Manager\Resources\Invitations\Pages;

use App\Filament\Manager\Resources\Invitations\InvitationResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageInvitations extends ManageRecords
{
    protected static string $resource = InvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [InvitationResource::inviteAction()];
    }

    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make('Toutes')];

        foreach (InvitationResource::STATES as $state => $label) {
            $tabs[$state] = Tab::make($label)->modifyQueryUsing(fn (Builder $query) => InvitationResource::whereState($query, $state));
        }

        $tabs['pending']->badge(fn (): ?string => InvitationResource::getNavigationBadge());

        return $tabs;
    }
}

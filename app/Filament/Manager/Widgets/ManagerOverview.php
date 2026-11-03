<?php

namespace App\Filament\Manager\Widgets;

use App\Filament\Manager\Pages\ContactRequests;
use App\Filament\Manager\Resources\Clients\ClientResource;
use App\Filament\Manager\Resources\Invitations\InvitationResource;
use App\Models\ClientInvitation;
use App\Models\User;
use App\Support\Inbox;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Chiffres clés de l'espace manager : demandes ouvertes, invitations en attente, clients. */
class ManagerOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $statuses = Inbox::statusCounts(Inbox::SOURCES);

        return [
            Stat::make('Demandes ouvertes', (int) $statuses->except(['traite'])->sum())
                ->description(((int) $statuses->get('nouveau', 0)).' nouvelles')
                ->color('warning')
                ->url(ContactRequests::getUrl()),
            Stat::make('Invitations en attente', InvitationResource::whereState(ClientInvitation::query(), 'pending')->count())
                ->url(InvitationResource::getUrl()),
            Stat::make('Clients', User::query()->where('role', 'client')->count())
                ->url(ClientResource::getUrl()),
        ];
    }
}

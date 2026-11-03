<?php

namespace App\Filament\Manager\Resources\Conversations\Pages;

use App\Filament\Manager\Resources\Conversations\ConversationResource;
use App\Support\DirectMessages;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListConversations extends ListRecords
{
    protected static string $resource = ConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [ConversationResource::composeAction()];
    }

    public function getTabs(): array
    {
        $counts = once(fn (): array => DirectMessages::boxCounts(auth()->user()));

        return [
            'received' => Tab::make('Reçus')
                ->badge($counts['received_unread'] ?: null)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('recipient_id', auth()->id())),
            'sent' => Tab::make('Envoyés')
                ->badge($counts['sent_unread'] ?: null)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('sender_id', auth()->id())),
        ];
    }
}

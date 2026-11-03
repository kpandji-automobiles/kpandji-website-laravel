<?php

namespace App\Filament\Manager\Pages;

use BackedEnum;
use Filament\Support\Icons\Heroicon;

/** Messages du formulaire de contact. */
class ContactRequests extends InboxPage
{
    protected static string $tab = 'messages';

    protected static ?string $slug = 'boite/contacts';

    protected static ?string $title = 'Contacts';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftEllipsis;

    protected static ?int $navigationSort = 1;
}

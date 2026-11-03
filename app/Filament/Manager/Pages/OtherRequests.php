<?php

namespace App\Filament\Manager\Pages;

use BackedEnum;
use Filament\Support\Icons\Heroicon;

/** Demandes des clients, inscriptions SIRA, Privilège et candidatures. */
class OtherRequests extends InboxPage
{
    protected static string $tab = 'autres';

    protected static ?string $slug = 'boite/autres';

    protected static ?string $title = 'Autres demandes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?int $navigationSort = 4;
}

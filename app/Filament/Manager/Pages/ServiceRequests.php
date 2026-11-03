<?php

namespace App\Filament\Manager\Pages;

use BackedEnum;
use Filament\Support\Icons\Heroicon;

/** Demandes de service après-vente. */
class ServiceRequests extends InboxPage
{
    protected static string $tab = 'sav';

    protected static ?string $slug = 'boite/sav';

    protected static ?string $title = 'S.A.V.';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?int $navigationSort = 3;
}

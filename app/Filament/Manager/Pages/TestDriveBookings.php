<?php

namespace App\Filament\Manager\Pages;

use BackedEnum;
use Filament\Support\Icons\Heroicon;

/** Réservations d’essai et demandes de devis. */
class TestDriveBookings extends InboxPage
{
    protected static string $tab = 'reservations';

    protected static ?string $slug = 'boite/reservations';

    protected static ?string $title = 'Réservations d’essai';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 2;
}

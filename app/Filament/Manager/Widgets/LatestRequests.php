<?php

namespace App\Filament\Manager\Widgets;

use App\Filament\Manager\Pages\InboxPage;
use App\Support\Inbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/** Les six dernières demandes, toutes origines confondues (données : Inbox::paginate). */
class LatestRequests extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Dernières demandes';

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => Inbox::paginate(Inbox::SOURCES, [], 6, 1)->getCollection()
                ->mapWithKeys(fn (array $item): array => [$item['source'].'-'.$item['id'] => ['__key' => $item['source'].'-'.$item['id']] + $item])
                ->all())
            ->paginated(false)
            ->columns([
                TextColumn::make('date')->label('Reçue')->since(),
                TextColumn::make('source')->label('Origine')->badge()->formatStateUsing(fn (string $state): string => InboxPage::SOURCES[$state] ?? $state),
                TextColumn::make('name')->label('Contact')->placeholder('—'),
                TextColumn::make('subject')->label('Objet')->limit(50),
                TextColumn::make('status')->label('Statut')->badge()->formatStateUsing(fn (string $state): string => InboxPage::STATUSES[$state] ?? $state),
            ]);
    }
}

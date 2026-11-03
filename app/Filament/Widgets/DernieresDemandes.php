<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TestDriveRequests\TestDriveRequestResource;
use App\Filament\Support\HandledRequests;
use App\Models\TestDriveRequest;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/** Les dernières demandes d'essai et de contact, à traiter en priorité. */
class DernieresDemandes extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Dernières demandes d’essai et de contact';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => TestDriveRequest::query()->with('car')->latest()->limit(8))
            ->paginated(false)
            ->columns([
                TextColumn::make('created_at')->label('Reçue')->since(),
                TextColumn::make('type')->label('Type')->badge()->formatStateUsing(fn (?string $state): string => TestDriveRequestResource::TYPES[$state] ?? (string) $state),
                TextColumn::make('name')->label('Demandeur')->description(fn (TestDriveRequest $record): ?string => $record->phone),
                TextColumn::make('car.name')->label('Modèle')->placeholder('—'),
                HandledRequests::statusColumn(),
            ])
            ->headerActions([
                Action::make('all')->label('Toutes les demandes')->url(TestDriveRequestResource::getUrl())->link(),
            ])
            ->recordActions([
                HandledRequests::toggleAction(),
            ]);
    }
}

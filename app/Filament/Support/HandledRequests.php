<?php

namespace App\Filament\Support;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/** Éléments communs aux demandes reçues du site : statut « traité » porté par la colonne handled_at. */
final class HandledRequests
{
    public static function statusColumn(): TextColumn
    {
        return TextColumn::make('handled_at')
            ->label('Statut')
            ->badge()
            ->state(fn (Model $record): string => $record->handled_at ? 'Traitée' : 'À traiter')
            ->color(fn (string $state): string => $state === 'Traitée' ? 'success' : 'warning')
            ->sortable();
    }

    public static function filter(): TernaryFilter
    {
        return TernaryFilter::make('handled')
            ->label('Statut')
            ->placeholder('Toutes')
            ->trueLabel('Traitées')
            ->falseLabel('À traiter')
            ->queries(
                true: fn (Builder $query) => $query->whereNotNull('handled_at'),
                false: fn (Builder $query) => $query->whereNull('handled_at'),
            );
    }

    public static function toggleAction(): Action
    {
        return Action::make('toggleHandled')
            ->label(fn (Model $record): string => $record->handled_at ? 'Remettre à traiter' : 'Marquer traitée')
            ->icon(fn (Model $record) => $record->handled_at ? Heroicon::OutlinedArrowUturnLeft : Heroicon::OutlinedCheckCircle)
            ->color(fn (Model $record): string => $record->handled_at ? 'gray' : 'success')
            ->action(fn (Model $record) => $record->forceFill(['handled_at' => $record->handled_at ? null : now()])->save());
    }

    public static function bulkActions(): BulkActionGroup
    {
        return BulkActionGroup::make([
            BulkAction::make('markHandled')
                ->label('Marquer traitées')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->deselectRecordsAfterCompletion()
                ->action(fn (Collection $records) => $records->each(fn (Model $record) => $record->forceFill(['handled_at' => now()])->save())),
            DeleteBulkAction::make(),
        ]);
    }

    /** @param  class-string<Model>  $model */
    public static function navigationBadge(string $model): ?string
    {
        $count = $model::query()->whereNull('handled_at')->count();

        return $count > 0 ? (string) $count : null;
    }
}

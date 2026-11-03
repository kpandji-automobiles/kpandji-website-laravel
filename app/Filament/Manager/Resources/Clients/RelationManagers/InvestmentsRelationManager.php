<?php

namespace App\Filament\Manager\Resources\Clients\RelationManagers;

use App\Filament\Manager\Resources\Investments\InvestmentForm;
use App\Filament\Manager\Resources\Investments\InvestmentResource;
use App\Models\Investment;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Investissements d'un client ; les écritures se gèrent dans la fiche de l'investissement. */
class InvestmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'investments';

    protected static ?string $title = 'Investissements';

    protected static ?string $modelLabel = 'investissement';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components(InvestmentForm::investmentFields(withClient: false));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')->label('Investissement')->description(fn (Investment $record): ?string => InvestmentForm::PROGRAMS[$record->program] ?? null),
                TextColumn::make('amount')->label('Montant')->formatStateUsing(fn (?int $state): string => InvestmentForm::fcfa($state))->alignEnd(),
                TextColumn::make('entries_sum_amount')->label('Écritures')->sum('entries', 'amount')->formatStateUsing(fn ($state): string => InvestmentForm::fcfa((int) $state))->alignEnd(),
                TextColumn::make('start_date')->label('Début')->date('d/m/Y'),
                TextColumn::make('status')->label('Statut')->badge()->formatStateUsing(fn (?string $state): string => InvestmentForm::STATUSES[$state] ?? (string) $state),
            ])
            ->headerActions([CreateAction::make()->label('Ajouter un investissement')])
            ->recordActions([
                Action::make('entries')
                    ->label('Écritures')
                    ->icon(Heroicon::OutlinedListBullet)
                    ->url(fn (Investment $record): string => InvestmentResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ]);
    }
}

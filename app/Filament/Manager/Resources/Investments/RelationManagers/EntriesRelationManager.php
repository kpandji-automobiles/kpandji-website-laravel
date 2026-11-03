<?php

namespace App\Filament\Manager\Resources\Investments\RelationManagers;

use App\Filament\Manager\Resources\Investments\InvestmentForm;
use App\Models\RevenueEntry;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Écritures de gains d'un investissement (revenus, versements, frais). */
class EntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'entries';

    protected static ?string $title = 'Écritures de gains';

    protected static ?string $modelLabel = 'écriture';

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components(InvestmentForm::entryFields());
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('entry_date', 'desc')
            ->columns([
                TextColumn::make('entry_date')->label('Date')->date('d/m/Y')->sortable(),
                TextColumn::make('type')->label('Type')->badge()->formatStateUsing(fn (?string $state): string => InvestmentForm::ENTRY_TYPES[$state] ?? (string) $state),
                TextColumn::make('description')->label('Description')->placeholder('—'),
                TextColumn::make('amount')->label('Montant')->alignEnd()
                    ->formatStateUsing(fn (?int $state, RevenueEntry $record): string => ($record->type === 'frais' ? '− ' : '+ ').InvestmentForm::fcfa($state))
                    ->color(fn (RevenueEntry $record): string => $record->type === 'frais' ? 'danger' : 'success'),
            ])
            ->headerActions([CreateAction::make()->label('Ajouter une écriture')])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}

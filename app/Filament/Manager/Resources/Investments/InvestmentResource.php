<?php

namespace App\Filament\Manager\Resources\Investments;

use App\Filament\Manager\Resources\Investments\Pages\CreateInvestment;
use App\Filament\Manager\Resources\Investments\Pages\EditInvestment;
use App\Filament\Manager\Resources\Investments\Pages\ListInvestments;
use App\Filament\Manager\Resources\Investments\RelationManagers\EntriesRelationManager;
use App\Models\Investment;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/** Investissements des clients et leurs écritures de gains. */
class InvestmentResource extends Resource
{
    protected static ?string $model = Investment::class;

    protected static ?string $slug = 'investissements';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Investissements';

    protected static ?string $modelLabel = 'investissement';

    protected static ?string $pluralModelLabel = 'investissements';

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(2)->schema(InvestmentForm::investmentFields(withClient: true)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('start_date', 'desc')
            ->columns([
                TextColumn::make('label')->label('Investissement')->description(fn (Investment $record): ?string => InvestmentForm::PROGRAMS[$record->program] ?? null)->searchable(),
                TextColumn::make('user.name')->label('Client')->searchable(),
                TextColumn::make('amount')->label('Montant')->formatStateUsing(fn (?int $state): string => InvestmentForm::fcfa($state))->alignEnd()->sortable(),
                TextColumn::make('entries_sum_amount')->label('Écritures')->sum('entries', 'amount')->formatStateUsing(fn ($state): string => InvestmentForm::fcfa((int) $state))->alignEnd(),
                TextColumn::make('start_date')->label('Début')->date('d/m/Y')->sortable(),
                TextColumn::make('status')->label('Statut')->badge()
                    ->formatStateUsing(fn (?string $state): string => InvestmentForm::STATUSES[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => match ($state) {
                        'actif' => 'success', 'en_attente' => 'warning', default => 'gray'
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(InvestmentForm::STATUSES),
                SelectFilter::make('program')->label('Programme')->options(InvestmentForm::PROGRAMS),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getRelations(): array
    {
        return [EntriesRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvestments::route('/'),
            'create' => CreateInvestment::route('/create'),
            'edit' => EditInvestment::route('/{record}/edit'),
        ];
    }
}

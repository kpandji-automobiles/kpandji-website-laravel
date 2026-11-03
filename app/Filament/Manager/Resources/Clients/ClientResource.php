<?php

namespace App\Filament\Manager\Resources\Clients;

use App\Filament\Manager\Resources\Clients\Pages\ListClients;
use App\Filament\Manager\Resources\Clients\Pages\ViewClient;
use App\Filament\Manager\Resources\Clients\RelationManagers\InvestmentsRelationManager;
use App\Filament\Manager\Resources\Investments\InvestmentForm;
use App\Models\User;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Clients investisseurs : fiche, portefeuille et écritures. Les comptes se créent par invitation. */
class ClientResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'clients';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $modelLabel = 'client';

    protected static ?string $pluralModelLabel = 'clients';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'client');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(4)->schema([
                TextEntry::make('email')->label('E-mail')->copyable(),
                TextEntry::make('phone')->label('Téléphone')->placeholder('—'),
                TextEntry::make('created_at')->label('Client depuis')->date('d/m/Y'),
                TextEntry::make('account_status')->label('Compte')->badge()
                    ->formatStateUsing(fn (?string $state): string => ['pending' => 'À valider', 'refused' => 'Refusé'][$state] ?? 'Actif')
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning', 'refused' => 'danger', default => 'success'
                    }),
                TextEntry::make('total_invested')->label('Total investi')
                    ->state(fn (User $record): string => InvestmentForm::fcfa((int) $record->investments()->sum('amount'))),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('investments')->withSum('investments', 'amount'))
            ->columns([
                TextColumn::make('name')->label('Client')->description(fn (User $record): string => $record->email)->searchable(['name', 'email'])->sortable(),
                TextColumn::make('phone')->label('Téléphone')->placeholder('—'),
                TextColumn::make('investments_count')->label('Investissements')->alignCenter()->sortable(),
                TextColumn::make('investments_sum_amount')->label('Total investi')->formatStateUsing(fn ($state): string => InvestmentForm::fcfa((int) $state))->alignEnd()->sortable(),
            ])
            ->recordActions([ViewAction::make()]);
    }

    public static function getRelations(): array
    {
        return [InvestmentsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'view' => ViewClient::route('/{record}'),
        ];
    }
}

<?php

namespace App\Filament\Resources\SavRequests;

use App\Filament\Resources\SavRequests\Pages\ManageSavRequests;
use App\Filament\Support\HandledRequests;
use App\Models\SavRequest;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Demandes « Écrire au S.A.V. » de la page /sav. */
class SavRequestResource extends Resource
{
    protected static ?string $model = SavRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup = 'Demandes';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'S.A.V.';

    protected static ?string $modelLabel = 'demande S.A.V.';

    protected static ?string $pluralModelLabel = 'demandes S.A.V.';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return HandledRequests::navigationBadge(SavRequest::class);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('name')->label('Nom'),
                TextEntry::make('created_at')->label('Reçue le')->dateTime('d/m/Y à H:i'),
                TextEntry::make('contact')->label('Téléphone ou e-mail')->copyable(),
                TextEntry::make('model')->label('Modèle')->placeholder('—'),
                TextEntry::make('location')->label('Lieu du véhicule')->placeholder('—'),
                TextEntry::make('handled_at')->label('Traitée le')->dateTime('d/m/Y à H:i')->placeholder('À traiter'),
                TextEntry::make('issue')->label('Problème')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Reçue')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('name')->label('Demandeur')->description(fn (SavRequest $record): string => (string) $record->contact)->searchable(['name', 'contact']),
                TextColumn::make('model')->label('Modèle')->placeholder('—')->searchable(),
                TextColumn::make('issue')->label('Problème')->limit(60)->wrap(),
                TextColumn::make('location')->label('Lieu')->placeholder('—'),
                HandledRequests::statusColumn(),
            ])
            ->filters([HandledRequests::filter()])
            ->recordActions([ViewAction::make(), HandledRequests::toggleAction(), DeleteAction::make()])
            ->toolbarActions([HandledRequests::bulkActions()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageSavRequests::route('/')];
    }
}

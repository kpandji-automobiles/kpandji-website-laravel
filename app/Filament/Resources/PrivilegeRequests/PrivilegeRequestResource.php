<?php

namespace App\Filament\Resources\PrivilegeRequests;

use App\Filament\Resources\PrivilegeRequests\Pages\ManagePrivilegeRequests;
use App\Filament\Support\HandledRequests;
use App\Models\PrivilegeRequest;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Demandes Kpandji Privilège (page /privilege). */
class PrivilegeRequestResource extends Resource
{
    public const SOURCES = ['advisor' => 'Parler à un conseiller', 'contact' => 'Nous contacter'];

    protected static ?string $model = PrivilegeRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|UnitEnum|null $navigationGroup = 'Demandes';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Privilège';

    protected static ?string $modelLabel = 'demande Privilège';

    protected static ?string $pluralModelLabel = 'demandes Privilège';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return HandledRequests::navigationBadge(PrivilegeRequest::class);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('source')->label('Formulaire')->badge()->formatStateUsing(fn (?string $state): string => self::SOURCES[$state] ?? (string) $state),
                TextEntry::make('created_at')->label('Reçue le')->dateTime('d/m/Y à H:i'),
                TextEntry::make('name')->label('Nom'),
                TextEntry::make('phone')->label('Téléphone')->copyable()->placeholder('—'),
                TextEntry::make('email')->label('E-mail')->copyable()->placeholder('—'),
                TextEntry::make('city')->label('Ville')->placeholder('—'),
                TextEntry::make('country')->label('Pays')->placeholder('—'),
                TextEntry::make('handled_at')->label('Traitée le')->dateTime('d/m/Y à H:i')->placeholder('À traiter'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Reçue')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('source')->label('Formulaire')->badge()->formatStateUsing(fn (?string $state): string => self::SOURCES[$state] ?? (string) $state),
                TextColumn::make('name')->label('Nom')->description(fn (PrivilegeRequest $record): string => collect([$record->phone, $record->email])->filter()->implode(' · '))->searchable(['name', 'email', 'phone']),
                TextColumn::make('city')->label('Ville')->placeholder('—'),
                HandledRequests::statusColumn(),
            ])
            ->filters([
                HandledRequests::filter(),
                SelectFilter::make('source')->label('Formulaire')->options(self::SOURCES),
            ])
            ->recordActions([ViewAction::make(), HandledRequests::toggleAction(), DeleteAction::make()])
            ->toolbarActions([HandledRequests::bulkActions()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManagePrivilegeRequests::route('/')];
    }
}

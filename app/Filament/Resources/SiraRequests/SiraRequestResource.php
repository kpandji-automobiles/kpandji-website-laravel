<?php

namespace App\Filament\Resources\SiraRequests;

use App\Filament\Resources\SiraRequests\Pages\ManageSiraRequests;
use App\Filament\Support\HandledRequests;
use App\Models\SiraRequest;
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

/** Inscriptions au programme Kpandji SIRA (page /sira). */
class SiraRequestResource extends Resource
{
    protected static ?string $model = SiraRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Demandes';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Inscriptions SIRA';

    protected static ?string $modelLabel = 'inscription SIRA';

    protected static ?string $pluralModelLabel = 'inscriptions SIRA';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return HandledRequests::navigationBadge(SiraRequest::class);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('name')->label('Nom'),
                TextEntry::make('created_at')->label('Reçue le')->dateTime('d/m/Y à H:i'),
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
                TextColumn::make('name')->label('Nom')->description(fn (SiraRequest $record): string => collect([$record->phone, $record->email])->filter()->implode(' · '))->searchable(['name', 'email', 'phone']),
                TextColumn::make('city')->label('Ville')->placeholder('—')->searchable(),
                TextColumn::make('country')->label('Pays')->placeholder('—'),
                HandledRequests::statusColumn(),
            ])
            ->filters([
                HandledRequests::filter(),
                SelectFilter::make('country')->label('Pays')->options(fn (): array => SiraRequest::query()->whereNotNull('country')->distinct()->orderBy('country')->pluck('country', 'country')->all()),
            ])
            ->recordActions([ViewAction::make(), HandledRequests::toggleAction(), DeleteAction::make()])
            ->toolbarActions([HandledRequests::bulkActions()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageSiraRequests::route('/')];
    }
}

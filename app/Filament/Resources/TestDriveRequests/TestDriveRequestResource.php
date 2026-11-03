<?php

namespace App\Filament\Resources\TestDriveRequests;

use App\Filament\Resources\TestDriveRequests\Pages\ManageTestDriveRequests;
use App\Filament\Support\HandledRequests;
use App\Models\TestDriveRequest;
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

/** Demandes d'essai, d'information et messages de contact (table test_drive_requests). */
class TestDriveRequestResource extends Resource
{
    public const TYPES = ['essai' => 'Essai', 'information' => 'Information', 'contact' => 'Contact'];

    protected static ?string $model = TestDriveRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Demandes';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Essais et contacts';

    protected static ?string $modelLabel = 'demande';

    protected static ?string $pluralModelLabel = 'essais et contacts';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return HandledRequests::navigationBadge(TestDriveRequest::class);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('type')->label('Type')->badge()->formatStateUsing(fn (?string $state): string => self::TYPES[$state] ?? (string) $state),
                TextEntry::make('created_at')->label('Reçue le')->dateTime('d/m/Y à H:i'),
                TextEntry::make('name')->label('Nom'),
                TextEntry::make('car.name')->label('Modèle')->placeholder('—'),
                TextEntry::make('email')->label('E-mail')->copyable()->placeholder('—'),
                TextEntry::make('phone')->label('Téléphone')->copyable()->placeholder('—'),
                TextEntry::make('preferred_date')->label('Date souhaitée')->date('d/m/Y')->placeholder('—'),
                TextEntry::make('handled_at')->label('Traitée le')->dateTime('d/m/Y à H:i')->placeholder('À traiter'),
                TextEntry::make('subject')->label('Sujet')->placeholder('—')->columnSpanFull(),
                TextEntry::make('message')->label('Message')->placeholder('—')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Reçue')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('type')->label('Type')->badge()->formatStateUsing(fn (?string $state): string => self::TYPES[$state] ?? (string) $state),
                TextColumn::make('name')
                    ->label('Demandeur')
                    ->description(fn (TestDriveRequest $record): string => collect([$record->phone, $record->email])->filter()->implode(' · '))
                    ->searchable(['name', 'email', 'phone']),
                TextColumn::make('car.name')->label('Modèle')->placeholder('—'),
                TextColumn::make('preferred_date')->label('Date souhaitée')->date('d/m/Y')->placeholder('—')->sortable(),
                HandledRequests::statusColumn(),
            ])
            ->filters([
                HandledRequests::filter(),
                SelectFilter::make('type')->label('Type')->options(self::TYPES),
                SelectFilter::make('car')->label('Modèle')->relationship('car', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                HandledRequests::toggleAction(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                HandledRequests::bulkActions(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTestDriveRequests::route('/'),
        ];
    }
}

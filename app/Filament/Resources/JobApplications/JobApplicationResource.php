<?php

namespace App\Filament\Resources\JobApplications;

use App\Filament\Resources\JobApplications\Pages\ManageJobApplications;
use App\Filament\Support\HandledRequests;
use App\Models\JobApplication;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

/** Candidatures envoyées depuis /emplois ; le CV reste sur le disque privé « local ». */
class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|UnitEnum|null $navigationGroup = 'Demandes';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Candidatures';

    protected static ?string $modelLabel = 'candidature';

    protected static ?string $pluralModelLabel = 'candidatures';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return HandledRequests::navigationBadge(JobApplication::class);
    }

    public static function downloadCvAction(): Action
    {
        return Action::make('downloadCv')
            ->label('Télécharger le CV')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->visible(fn (JobApplication $record): bool => filled($record->cv_path))
            ->action(function (JobApplication $record): ?StreamedResponse {
                if (! Storage::disk('local')->exists($record->cv_path)) {
                    Notification::make()->danger()->title('Le fichier du CV est introuvable.')->send();

                    return null;
                }

                return Storage::disk('local')->download($record->cv_path, $record->cv_name ?: basename($record->cv_path));
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('full_name')->label('Nom'),
                TextEntry::make('created_at')->label('Reçue le')->dateTime('d/m/Y à H:i'),
                TextEntry::make('position')->label('Poste visé')->placeholder('Candidature spontanée'),
                TextEntry::make('handled_at')->label('Traitée le')->dateTime('d/m/Y à H:i')->placeholder('À traiter'),
                TextEntry::make('email')->label('E-mail')->copyable(),
                TextEntry::make('phone')->label('Téléphone')->copyable()->placeholder('—'),
                TextEntry::make('cv_name')->label('CV')->placeholder('Aucun')
                    ->suffix(fn (JobApplication $record): string => $record->cv_size ? ' ('.number_format($record->cv_size / 1024, 0, ',', ' ').' Ko)' : ''),
                TextEntry::make('languages')->label('Langues')->placeholder('—'),
                TextEntry::make('cv_summary')->label('Parcours')->placeholder('—')->columnSpanFull(),
                TextEntry::make('education')->label('Formation')->placeholder('—')->columnSpanFull(),
                TextEntry::make('skills')->label('Compétences')->placeholder('—')->columnSpanFull(),
                TextEntry::make('extras')->label('Informations complémentaires')->placeholder('—')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Reçue')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('full_name')->label('Candidat')->description(fn (JobApplication $record): string => collect([$record->phone, $record->email])->filter()->implode(' · '))->searchable(['full_name', 'email', 'phone']),
                TextColumn::make('position')->label('Poste')->placeholder('Spontanée')->searchable()->limit(40),
                TextColumn::make('cv_name')->label('CV')->placeholder('—')->limit(30),
                HandledRequests::statusColumn(),
            ])
            ->filters([HandledRequests::filter()])
            ->recordActions([ViewAction::make(), self::downloadCvAction(), HandledRequests::toggleAction(), DeleteAction::make()])
            ->toolbarActions([HandledRequests::bulkActions()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageJobApplications::route('/')];
    }
}

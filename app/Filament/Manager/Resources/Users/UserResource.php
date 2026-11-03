<?php

namespace App\Filament\Manager\Resources\Users;

use App\Filament\Manager\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use App\Support\AccountValidation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Comptes client et manager créés sur invitation : validation ou refus. */
class UserResource extends Resource
{
    public const STATUSES = ['pending' => 'À valider', 'active' => 'Actif', 'refused' => 'Refusé'];

    public const ROLES = ['client' => 'Client', 'manager' => 'Manager'];

    protected static ?string $model = User::class;

    protected static ?string $slug = 'comptes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Comptes';

    protected static ?string $modelLabel = 'compte';

    protected static ?string $pluralModelLabel = 'comptes';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('role', array_keys(self::ROLES))
            ->where(fn (Builder $query) => $query->whereNull('is_admin')->orWhere('is_admin', false));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('account_status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nom')->description(fn (User $record): string => $record->email)->searchable(['name', 'email']),
                TextColumn::make('role')->label('Rôle')->badge()->formatStateUsing(fn (?string $state): string => self::ROLES[$state] ?? (string) $state),
                TextColumn::make('phone')->label('Téléphone')->placeholder('—'),
                TextColumn::make('account_status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::STATUSES[$state ?? 'active'] ?? (string) $state)
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning', 'refused' => 'danger', default => 'success'
                    }),
                TextColumn::make('created_at')->label('Créé le')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')->label('Rôle')->options(self::ROLES),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Valider')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Le compte devient actif et la personne est prévenue par e-mail.')
                    ->visible(fn (User $record): bool => AccountValidation::canDecide($record))
                    ->action(function (User $record): void {
                        AccountValidation::approve($record);
                        Notification::make()->success()->title('Compte validé.')->send();
                    }),
                Action::make('refuse')
                    ->label('Refuser')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => AccountValidation::canDecide($record) && $record->account_status !== 'refused')
                    ->action(function (User $record): void {
                        AccountValidation::refuse($record);
                        Notification::make()->success()->title('Compte refusé.')->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageUsers::route('/')];
    }
}

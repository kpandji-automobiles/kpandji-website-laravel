<?php

namespace App\Filament\Manager\Resources\Invitations;

use App\Filament\Manager\Resources\Invitations\Pages\ManageInvitations;
use App\Models\ClientInvitation;
use App\Support\Invitations;
use App\Support\NamedRateLimit;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Invitations client / manager : envoi, renvoi (limité) et révocation. Logique dans App\Support\Invitations. */
class InvitationResource extends Resource
{
    public const STATES = ['pending' => 'En attente', 'accepted' => 'Acceptée', 'expired' => 'Expirée', 'revoked' => 'Révoquée'];

    public const ROLES = ['client' => 'Client', 'manager' => 'Manager'];

    public const MAIL_STATUSES = ['sent' => 'Envoyé', 'logged' => 'Simulé', 'failed' => 'Échec'];

    protected static ?string $model = ClientInvitation::class;

    protected static ?string $slug = 'invitations';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelopeOpen;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Invitations';

    protected static ?string $modelLabel = 'invitation';

    protected static ?string $pluralModelLabel = 'invitations';

    public static function canCreate(): bool
    {
        return false;
    }

    /** Même règle que ClientInvitation::state(), exprimée en SQL pour les onglets. */
    public static function whereState(Builder $query, string $state): Builder
    {
        return match ($state) {
            'accepted' => $query->whereNotNull('accepted_at'),
            'revoked' => $query->whereNull('accepted_at')->whereNotNull('revoked_at'),
            'expired' => $query->whereNull('accepted_at')->whereNull('revoked_at')->where('expires_at', '<=', now()),
            default => $query->whereNull('accepted_at')->whereNull('revoked_at')->where('expires_at', '>', now()),
        };
    }

    public static function getNavigationBadge(): ?string
    {
        $count = self::whereState(ClientInvitation::query(), 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('inviter'))
            ->columns([
                TextColumn::make('name')->label('Invité')->description(fn (ClientInvitation $record): string => $record->email)->searchable(['name', 'email']),
                TextColumn::make('role')->label('Rôle')->badge()->formatStateUsing(fn (?string $state): string => self::ROLES[$state] ?? (string) $state),
                TextColumn::make('state')
                    ->label('Statut')
                    ->badge()
                    ->state(fn (ClientInvitation $record): string => $record->state())
                    ->formatStateUsing(fn (string $state): string => self::STATES[$state])
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success', 'pending' => 'warning', default => 'gray'
                    }),
                TextColumn::make('mail_status')
                    ->label('E-mail')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::MAIL_STATUSES[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => $state === 'failed' ? 'danger' : 'gray')
                    ->placeholder('—'),
                TextColumn::make('send_count')->label('Envois')->alignCenter(),
                TextColumn::make('inviter.name')->label('Invité par')->placeholder('—'),
                TextColumn::make('expires_at')->label('Expire le')->date('d/m/Y'),
                TextColumn::make('created_at')->label('Créée le')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')->label('Rôle')->options(self::ROLES),
            ])
            ->recordActions([
                Action::make('resend')
                    ->label('Renvoyer')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->requiresConfirmation()
                    ->modalDescription('Un nouveau lien, valable '.ClientInvitation::DAYS.' jours, remplace le précédent.')
                    ->visible(fn (ClientInvitation $record): bool => Invitations::isOpen($record))
                    ->action(function (ClientInvitation $record): void {
                        if (! NamedRateLimit::attempt('invitation-resend', request())) {
                            Notification::make()->danger()->title('Trop de renvois.')->body('Réessayez dans une minute.')->send();

                            return;
                        }

                        self::notifySent(Invitations::resend($record));
                    }),
                Action::make('revoke')
                    ->label('Révoquer')
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Le lien d\'invitation ne fonctionnera plus.')
                    ->visible(fn (ClientInvitation $record): bool => in_array($record->state(), ['pending', 'expired'], true))
                    ->action(function (ClientInvitation $record): void {
                        Invitations::revoke($record);
                        Notification::make()->success()->title('Invitation révoquée.')->send();
                    }),
            ]);
    }

    public static function inviteAction(): Action
    {
        return Action::make('invite')
            ->label('Inviter')
            ->icon(Heroicon::OutlinedUserPlus)
            ->modalHeading('Envoyer une invitation')
            ->modalSubmitActionLabel('Envoyer')
            ->schema([
                TextInput::make('name')->label('Nom')->required()->maxLength(120),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->maxLength(190)
                    ->rules([
                        fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                            if (is_string($value) && Invitations::emailTaken($value)) {
                                $fail(sp_t('inv_exists'));
                            }
                        },
                    ]),
                TextInput::make('phone')->label('Téléphone (WhatsApp)')->tel()->maxLength(40),
                Select::make('role')->label('Rôle')->options(self::ROLES)->in(array_keys(self::ROLES))->default('client')->required(),
            ])
            ->action(fn (array $data) => self::notifySent(Invitations::invite($data, auth()->user())));
    }

    /**
     * Le lien reste affiché : il peut être transmis à la main si l'e-mail n'est pas parti.
     *
     * @param  array{invitation: ClientInvitation, link: string}  $sent
     */
    private static function notifySent(array $sent): void
    {
        $invitation = $sent['invitation'];
        $whatsapp = Invitations::whatsappUrl($invitation, $sent['link']);

        $notification = Notification::make()
            ->title('Invitation envoyée à '.$invitation->name.'.')
            ->body(match ($invitation->mail_status) {
                'sent' => 'E-mail envoyé. Lien : ',
                'logged' => 'E-mail simulé (environnement de test), transmettez le lien : ',
                default => 'L\'e-mail n\'est pas parti, transmettez le lien : ',
            }.$sent['link'])
            ->persistent();

        $notification = $invitation->mail_status === 'failed' ? $notification->warning() : $notification->success();

        if ($whatsapp) {
            $notification->actions([
                Action::make('whatsapp')->label('Envoyer par WhatsApp')->url($whatsapp, shouldOpenInNewTab: true),
            ]);
        }

        $notification->send();
    }

    public static function getPages(): array
    {
        return ['index' => ManageInvitations::route('/')];
    }
}

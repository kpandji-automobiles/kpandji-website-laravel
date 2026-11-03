<?php

namespace App\Filament\Manager\Pages;

use App\Models\InboxReply;
use App\Support\Inbox;
use App\Support\InboxReplies;
use App\Support\NamedRateLimit;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use UnitEnum;

/**
 * Un onglet de la boîte de réception unifiée (App\Support\Inbox::TABS) : liste paginée en base,
 * statut, consultation et réponse par e-mail. Les données viennent d'Inbox::paginate / Inbox::find.
 */
abstract class InboxPage extends Page implements HasTable
{
    use InteractsWithTable;

    public const SOURCES = ['contact' => 'Contact', 'essai' => 'Essai', 'sav' => 'S.A.V.', 'sira' => 'SIRA', 'privilege' => 'Privilège', 'emplois' => 'Candidature', 'client' => 'Client'];

    public const STATUSES = ['nouveau' => 'Nouveau', 'en_cours' => 'En cours', 'confirme' => 'Confirmé', 'traite' => 'Traité', 'annule' => 'Annulé'];

    /** Champs techniques masqués dans le détail brut d'une demande. */
    private const HIDDEN_FIELDS = ['id', 'created_at', 'updated_at', 'handled_at', 'cv_path', 'ip', 'user_id', 'investment_id', 'u_name', 'u_email', 'u_phone', 'locale'];

    protected static string|UnitEnum|null $navigationGroup = 'Boîte de réception';

    /** Clé de l'onglet dans Inbox::TABS. */
    protected static string $tab;

    public static function getNavigationBadge(): ?string
    {
        $count = once(fn (): array => Inbox::newCounts())[static::$tab] ?? 0;

        return $count > 0 ? (string) $count : null;
    }

    /** @return list<string> */
    protected static function sources(): array
    {
        return Inbox::TABS[static::$tab];
    }

    /** @return array<string, string> */
    protected static function statusOptions(): array
    {
        return array_intersect_key(self::STATUSES, array_flip(Inbox::statuses(static::sources()[0])));
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([EmbeddedTable::make()]);
    }

    public function table(Table $table): Table
    {
        $sources = static::sources();

        return $table
            ->records(fn (?string $search, ?array $filters, int $page, int|string $recordsPerPage): LengthAwarePaginator => $this->requests($search, $filters ?? [], $page, (int) $recordsPerPage))
            ->searchable()
            ->paginated([10, 25, 50])
            ->emptyStateHeading('Aucune demande')
            ->columns([
                TextColumn::make('date')->label('Reçue le')->dateTime('d/m/Y H:i'),
                TextColumn::make('source')->label('Origine')->badge()->formatStateUsing(fn (string $state): string => self::SOURCES[$state])->visible(count($sources) > 1),
                TextColumn::make('name')->label('Contact')->description(fn (array $record): ?string => $record['email'] ?? $record['phone'])->placeholder('—'),
                TextColumn::make('subject')->label('Objet')->description(fn (array $record): string => Str::limit($record['body'], 80))->limit(50),
                TextColumn::make('wish_date')->label('Date souhaitée')->date('d/m/Y')->placeholder('—')->visible(static::$tab === 'reservations'),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'nouveau' => 'warning', 'en_cours', 'confirme' => 'info', 'traite' => 'success', default => 'gray'
                    }),
                TextColumn::make('replies')->label('Réponses')->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(static::statusOptions()),
                SelectFilter::make('source')->label('Origine')->options(array_intersect_key(self::SOURCES, array_flip($sources)))->visible(count($sources) > 1),
                SelectFilter::make('replied')->label('Réponse')->options(['1' => 'Avec réponse', '0' => 'Sans réponse']),
                Filter::make('date')->schema([
                    DatePicker::make('from')->label('Reçue depuis le'),
                    DatePicker::make('to')->label('Jusqu\'au'),
                ]),
            ])
            ->recordActions([
                $this->viewAction(),
                $this->statusAction(),
                $this->replyAction(),
            ]);
    }

    /**
     * Les filtres viennent du client : seules les valeurs connues sont transmises à Inbox::paginate.
     *
     * @param  array<string, array<string, mixed>>  $filters
     */
    protected function requests(?string $search, array $filters, int $page, int $perPage): LengthAwarePaginator
    {
        $sources = static::sources();
        $value = fn (string $filter, string $field = 'value'): ?string => is_string($filters[$filter][$field] ?? null) ? $filters[$filter][$field] : null;
        $date = fn (string $field): ?string => preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $value('date', $field)) ? substr($value('date', $field), 0, 10) : null;

        $paginator = Inbox::paginate($sources, array_filter([
            'q' => filled($search) ? mb_substr(trim($search), 0, 200) : null,
            'status' => in_array($value('status'), Inbox::statuses($sources[0]), true) ? $value('status') : null,
            'source' => in_array($value('source'), $sources, true) ? $value('source') : null,
            'replied' => in_array($value('replied'), ['0', '1'], true) ? $value('replied') : null,
            'from' => $date('from'),
            'to' => $date('to'),
        ], fn (?string $filter): bool => $filter !== null), $perPage, $page);

        return $paginator->setCollection($paginator->getCollection()->map(fn (array $item): array => ['__key' => $item['source'].'-'.$item['id']] + $item));
    }

    protected function viewAction(): Action
    {
        return Action::make('view')
            ->label('Ouvrir')
            ->icon(Heroicon::OutlinedEye)
            ->color('gray')
            ->modalHeading(fn (array $record): string => $record['name'] ?? self::SOURCES[$record['source']])
            ->modalDescription(fn (array $record): string => self::SOURCES[$record['source']].' · '.$record['subject'])
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Fermer')
            ->schema(function (array $record): array {
                $replies = InboxReply::with('author')->where(['source' => $record['source'], 'source_id' => $record['id']])->oldest()->get();
                $details = collect($record['raw'])->except(self::HIDDEN_FIELDS)->filter(fn ($value): bool => $value !== null && $value !== '')
                    ->map(fn ($value): string => Str::limit((string) $value, 400));

                return [
                    Section::make()->columns(2)->schema([
                        TextEntry::make('email')->label('E-mail')->state($record['email'])->placeholder('—')->copyable(),
                        TextEntry::make('phone')->label('Téléphone')->state($record['phone'])->placeholder('—'),
                        TextEntry::make('date')->label('Reçue le')->state($record['date'])->dateTime('d/m/Y H:i'),
                        TextEntry::make('model')->label('Modèle')->state($record['model'])->visible(filled($record['model'])),
                        TextEntry::make('wish_date')->label('Date souhaitée')->state($record['wish_date'])->date('d/m/Y')->visible(filled($record['wish_date'])),
                        TextEntry::make('location')->label('Lieu')->state($record['location'] ?? null)->visible(filled($record['location'] ?? null)),
                        TextEntry::make('body')->label('Message')->state($record['body'])->columnSpanFull()->visible(filled($record['body']))
                            ->extraAttributes(['style' => 'white-space: pre-line']),
                    ]),
                    Section::make('Réponses')->visible($replies->isNotEmpty())->schema(
                        $replies->map(fn (InboxReply $reply): TextEntry => TextEntry::make('reply_'.$reply->id)
                            ->label(($reply->author->name ?? '—').' · '.$reply->created_at->format('d/m/Y H:i').' → '.($reply->to_email ?: 'sans e-mail'))
                            ->state($reply->body)
                            ->extraAttributes(['style' => 'white-space: pre-line']))->all(),
                    ),
                    Section::make('Détails')->collapsed()->schema([
                        KeyValueEntry::make('raw')->hiddenLabel()->state($details->all())->keyLabel('Champ')->valueLabel('Valeur'),
                    ]),
                ];
            });
    }

    protected function statusAction(): Action
    {
        return Action::make('status')
            ->label('Statut')
            ->icon(Heroicon::OutlinedTag)
            ->color('gray')
            ->modalWidth('sm')
            ->fillForm(fn (array $record): array => ['status' => $record['status']])
            ->schema([
                Select::make('status')->label('Statut')->options(static::statusOptions())->in(array_keys(static::statusOptions()))->required(),
            ])
            ->action(function (array $record, array $data): void {
                Inbox::setStatus($record['source'], (int) $record['id'], $data['status']);
                Notification::make()->success()->title('Statut enregistré.')->send();
            });
    }

    protected function replyAction(): Action
    {
        return Action::make('reply')
            ->label('Répondre')
            ->icon(Heroicon::OutlinedPaperAirplane)
            ->modalHeading(fn (array $record): string => 'Répondre à '.($record['name'] ?? '—'))
            ->modalDescription(fn (array $record): string => $record['email']
                ? 'La réponse est envoyée par e-mail à '.$record['email'].'.'
                : 'Aucune adresse e-mail connue : la réponse sera seulement enregistrée.')
            ->modalSubmitActionLabel('Envoyer')
            ->schema([
                Textarea::make('body')->label('Message')->rows(7)->required()->maxLength(5000),
            ])
            ->action(function (array $record, array $data, Action $action): void {
                $item = Inbox::find($record['source'], (int) $record['id']);

                if ($item === null) {
                    Notification::make()->danger()->title('Demande introuvable.')->send();

                    return;
                }

                if (! NamedRateLimit::attempt('manager-reply', request())) {
                    Notification::make()->danger()->title('Trop de réponses.')->body('Réessayez dans une minute.')->send();
                    $action->halt();
                }

                InboxReplies::send($item, auth()->user(), $data['body']);
                Notification::make()->success()->title('Réponse envoyée.')->send();
            });
    }
}

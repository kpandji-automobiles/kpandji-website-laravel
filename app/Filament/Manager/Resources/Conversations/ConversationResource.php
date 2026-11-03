<?php

namespace App\Filament\Manager\Resources\Conversations;

use App\Filament\Manager\Resources\Conversations\Pages\ListConversations;
use App\Filament\Manager\Resources\Conversations\Pages\ViewConversation;
use App\Models\Conversation;
use App\Models\User;
use App\Support\DirectMessages;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

/** Messages directs entre l'équipe et les clients / managers : chaque personne ne voit que ses conversations. */
class ConversationResource extends Resource
{
    public const ROLES = ['client' => 'Client', 'manager' => 'Manager'];

    protected static ?string $model = Conversation::class;

    protected static ?string $slug = 'conversations';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Boîte de réception';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Messages internes';

    protected static ?string $modelLabel = 'conversation';

    protected static ?string $pluralModelLabel = 'conversations';

    protected static ?string $recordTitleAttribute = 'subject';

    public static function getEloquentQuery(): Builder
    {
        $userId = auth()->id();

        return parent::getEloquentQuery()->where(fn (Builder $query) => $query->where('sender_id', $userId)->orWhere('recipient_id', $userId));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Conversation::unreadTotal(auth()->user());

        return $count > 0 ? (string) $count : null;
    }

    /** Message initial reçu et non lu + réponses non lues de l'autre partie (compteur chargé par la table). */
    public static function unreadCount(Conversation $conversation): int
    {
        $initial = $conversation->recipient_id === auth()->id() && ! $conversation->read_at ? 1 : 0;

        return $initial + (int) $conversation->unread_replies_count;
    }

    public static function correspondent(Conversation $conversation): ?User
    {
        return $conversation->sender_id === auth()->id() ? $conversation->recipient : $conversation->sender;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with(['sender:id,name,email,role', 'recipient:id,name,email,role'])
                ->withCount([
                    'replies',
                    'replies as unread_replies_count' => fn (Builder $replies) => $replies->where('user_id', '!=', auth()->id())->whereNull('read_at'),
                ]))
            ->columns([
                TextColumn::make('subject')
                    ->label('Objet')
                    ->description(fn (Conversation $record): string => Str::limit($record->body, 80))
                    ->weight(fn (Conversation $record): ?FontWeight => self::unreadCount($record) > 0 ? FontWeight::Bold : null)
                    ->searchable(['subject', 'body']),
                TextColumn::make('correspondent')
                    ->label('Correspondant')
                    ->state(fn (Conversation $record): ?string => self::correspondent($record)?->name)
                    ->description(fn (Conversation $record): ?string => self::ROLES[self::correspondent($record)?->role] ?? null)
                    ->placeholder('—'),
                TextColumn::make('replies_count')->label('Réponses')->alignCenter(),
                TextColumn::make('unread')
                    ->label('Non lus')
                    ->state(fn (Conversation $record): ?int => self::unreadCount($record) ?: null)
                    ->badge()
                    ->alignCenter()
                    ->placeholder('—'),
                TextColumn::make('created_at')->label('Envoyé le')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->recordActions([ViewAction::make()->label('Ouvrir')]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->columnSpanFull()->schema([
                TextEntry::make('sender.name')->label('De')->placeholder('—'),
                TextEntry::make('recipient.name')->label('À')->placeholder('—'),
                TextEntry::make('created_at')->label('Envoyé le')->dateTime('d/m/Y H:i'),
                TextEntry::make('body')->label('Message')->columnSpanFull()->extraAttributes(['style' => 'white-space: pre-line']),
            ]),
            RepeatableEntry::make('replies')
                ->label('Réponses')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextEntry::make('author.name')->label('De')->placeholder('—'),
                    TextEntry::make('created_at')->label('Le')->dateTime('d/m/Y H:i'),
                    TextEntry::make('body')->hiddenLabel()->columnSpanFull()->extraAttributes(['style' => 'white-space: pre-line']),
                ])
                ->placeholder('Aucune réponse pour le moment.'),
        ]);
    }

    public static function composeAction(): Action
    {
        return Action::make('compose')
            ->label('Écrire')
            ->icon(Heroicon::OutlinedPencilSquare)
            ->modalHeading('Nouveau message')
            ->modalDescription('Chaque destinataire reçoit une conversation séparée et un e-mail ; personne ne voit les autres destinataires.')
            ->modalSubmitActionLabel('Envoyer')
            ->rateLimit(10)
            ->schema([
                Select::make('recipients')
                    ->label('Destinataires')
                    ->multiple()
                    ->searchable()
                    ->options(fn (): array => DirectMessages::recipients(auth()->user())
                        ->mapWithKeys(fn (User $user): array => [$user->id => $user->name.' · '.(self::ROLES[$user->role] ?? $user->role)])
                        ->all())
                    ->required()
                    ->maxItems(500),
                TextInput::make('subject')->label('Objet')->required()->maxLength(160),
                Textarea::make('body')->label('Message')->rows(7)->required()->maxLength(5000),
            ])
            ->action(function (array $data): void {
                $count = DirectMessages::compose(auth()->user(), $data['recipients'], $data['subject'], $data['body']);
                Notification::make()->success()->title('Message envoyé à '.$count.' destinataire'.($count > 1 ? 's' : '').'.')->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConversations::route('/'),
            'view' => ViewConversation::route('/{record}'),
        ];
    }
}

<?php

namespace App\Filament\Manager\Resources\Conversations\Pages;

use App\Filament\Manager\Resources\Conversations\ConversationResource;
use App\Models\Conversation;
use App\Support\DirectMessages;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

/** Conversation : l'ouvrir la marque comme lue pour la personne connectée. */
class ViewConversation extends ViewRecord
{
    protected static string $resource = ConversationResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->getConversation()->markReadFor(auth()->user());
    }

    public function getTitle(): string
    {
        return $this->getConversation()->subject;
    }

    private function getConversation(): Conversation
    {
        /** @var Conversation */
        return $this->getRecord();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label('Répondre')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->modalHeading(fn (): string => 'Répondre à '.(ConversationResource::correspondent($this->getConversation())?->name ?? '—'))
                ->modalDescription('La réponse est aussi envoyée par e-mail.')
                ->modalSubmitActionLabel('Envoyer')
                ->rateLimit(20)
                ->schema([
                    Textarea::make('body')->label('Message')->rows(7)->required()->maxLength(5000),
                ])
                ->action(function (array $data): void {
                    DirectMessages::reply($this->getConversation(), auth()->user(), $data['body']);
                    $this->getConversation()->unsetRelation('replies');
                    Notification::make()->success()->title('Réponse envoyée.')->send();
                }),
        ];
    }
}

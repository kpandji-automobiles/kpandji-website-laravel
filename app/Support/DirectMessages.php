<?php

namespace App\Support;

use App\Models\Conversation;
use App\Models\InboxReply;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/** Messages directs de l'espace manager vers les clients et managers (conversations), doublés d'un e-mail. */
final class DirectMessages
{
    /**
     * Destinataires possibles : comptes validés client / manager, hors expéditeur.
     *
     * @return Collection<int, User>
     */
    public static function recipients(User $sender): Collection
    {
        return User::whereIn('role', ['client', 'manager'])->where('account_status', 'active')->where('id', '!=', $sender->id)->orderBy('name')->get(['id', 'name', 'email', 'role']);
    }

    /**
     * Nombre de conversations envoyées / reçues et de non-lus (message initial reçu + réponses de l'autre partie).
     *
     * @return array{sent: int, sent_unread: int, received: int, received_unread: int}
     */
    public static function boxCounts(User $me): array
    {
        $counts = [];

        foreach (['sent' => 'sender_id', 'received' => 'recipient_id'] as $box => $column) {
            $conversations = Conversation::query()->where($column, $me->id);

            $initialUnread = (clone $conversations)->where('recipient_id', $me->id)->whereNull('read_at')->count();
            $replyUnread = InboxReply::query()
                ->where('source', 'dm')
                ->whereIn('source_id', (clone $conversations)->select('id'))
                ->where('user_id', '!=', $me->id)
                ->whereNull('read_at')
                ->count();

            $counts[$box] = (clone $conversations)->count();
            $counts[$box.'_unread'] = $initialUnread + $replyUnread;
        }

        return $counts;
    }

    /**
     * Une conversation séparée par destinataire : personne ne voit les autres destinataires.
     * Les identifiants hors de recipients() sont ignorés.
     *
     * @param  array<int, int|string>  $recipientIds
     * @return int nombre de conversations créées
     */
    public static function compose(User $sender, array $recipientIds, string $subject, string $body): int
    {
        $batch = (string) Str::uuid();
        $users = self::recipients($sender)->whereIn('id', array_map('intval', $recipientIds));

        foreach ($users as $user) {
            $conversation = Conversation::create(['sender_id' => $sender->id, 'recipient_id' => $user->id, 'subject' => $subject, 'body' => $body, 'batch' => $batch]);
            $link = $user->role === 'client' ? route('client.thread', 'dm-'.$conversation->id) : route('manager.conversation', $conversation);
            $conversation->forceFill(['mail_status' => SpaceMail::send($user->email, $subject, $body."\n\n— ".$sender->name.", KPANDJI\n".$link)])->save();
        }

        return $users->count();
    }

    /** Réponse dans la conversation, envoyée par e-mail à l'autre partie. */
    public static function reply(Conversation $conversation, User $author, string $body): InboxReply
    {
        $other = $conversation->sender_id === $author->id ? $conversation->recipient : $conversation->sender;
        $status = $other ? SpaceMail::send($other->email, sp_t('reply_subject').' — '.$conversation->subject, $body) : null;

        return InboxReply::create(['source' => 'dm', 'source_id' => $conversation->id, 'user_id' => $author->id, 'to_email' => $other?->email, 'body' => $body, 'mail_status' => $status]);
    }
}

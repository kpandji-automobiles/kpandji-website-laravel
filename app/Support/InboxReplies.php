<?php

namespace App\Support;

use App\Models\InboxReply;
use App\Models\User;

/** Réponse d'un manager à une demande de la boîte de réception (App\Support\Inbox), envoyée par e-mail. */
final class InboxReplies
{
    /**
     * Sans adresse e-mail connue, la réponse est seulement enregistrée. Une demande « nouveau » passe « en cours »
     * (sauf les essais, dont le cycle de statuts est différent).
     *
     * @param  array{source: string, id: int, email: ?string, subject: ?string, status: string}  $item  élément renvoyé par Inbox::find()
     */
    public static function send(array $item, User $author, string $body): InboxReply
    {
        $subject = sp_t('reply_subject').($item['subject'] && $item['subject'] !== '—' ? ' — '.$item['subject'] : '');
        $status = $item['email'] ? SpaceMail::send($item['email'], $subject, $body) : null;

        $reply = InboxReply::create([
            'source' => $item['source'],
            'source_id' => $item['id'],
            'user_id' => $author->id,
            'to_email' => $item['email'],
            'body' => $body,
            'mail_status' => $status,
        ]);

        if ($item['status'] === 'nouveau' && $item['source'] !== 'essai') {
            Inbox::setStatus($item['source'], $item['id'], 'en_cours');
        }

        return $reply;
    }
}

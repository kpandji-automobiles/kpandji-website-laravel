<?php
namespace App\Support;
use App\Models\ClientNotice;
use App\Models\InboxReply;
use App\Models\User;
use Illuminate\Support\Collection;
/** Fils et messages accessibles au client selon leur propriétaire. */
class ClientThreads
{
    public const FORM_SOURCES = ['client', 'contact', 'essai', 'sav', 'sira', 'privilege'];

   /** Autorise uniquement le propriétaire identifié. */
public static function owns(User $u, array $i): bool
{
    $ownerId = (int) ($i['raw']['user_id'] ?? 0);
    $userId = (int) $u->id;

    return $ownerId > 0
        && $userId > 0
        && $ownerId === $userId;
}

    /** Demandes (tous formulaires) appartenant au client. */
    public static function items(User $u, array $sources): Collection
    {
        return Inbox::all($sources)->filter(fn($i) => self::owns($u, $i))->values();
    }

    public static function all(User $u): Collection
    {
        $items = self::items($u, self::FORM_SOURCES);
        $replies = InboxReply::whereIn('source', self::FORM_SOURCES)->get()->groupBy(fn($r) => $r->source . '-' . $r->source_id);
        $threads = $items->map(function ($i) use ($replies, $u) {
            $rs = $replies[$i['source'] . '-' . $i['id']] ?? collect();
            return [
                'key' => $i['source'] . '-' . $i['id'],
                'source' => $i['source'],
                'subject' => $i['source'] === 'client' ? $i['subject'] : sp_t('src_' . $i['source']) . ($i['subject'] && $i['subject'] !== '—' ? ' — ' . $i['subject'] : ''),
                'date' => (string) ($rs->max('created_at') ?? $i['date']),
                'count' => $rs->count(),
                'status' => $i['status'],
                'unread' => $rs->where('user_id', '!=', $u->id)->whereNull('read_at')->count(),
                'preview' => (string) ($rs->sortByDesc('id')->first()?->body ?? $i['body'])
            ];
        })->filter(fn($t) => $t['source'] === 'client' || $t['count'] > 0); // formulaires : seulement s'il y a une réponse
        $notices = ClientNotice::where('user_id', $u->id)->get()->map(fn($n) => [
            'key' => 'notice-' . $n->id,
            'source' => 'notice',
            'subject' => $n->subject,
            'date' => (string) $n->created_at,
            'count' => 1,
            'status' => null,
            'unread' => $n->read_at ? 0 : 1,
            'preview' => $n->body
        ]);
        $dms = \App\Models\Conversation::where('recipient_id', $u->id)->get()->map(function ($c) use ($u) {
            $last = InboxReply::where(['source' => 'dm', 'source_id' => $c->id])->latest('id')->first();
            return [
                'key' => 'dm-' . $c->id,
                'source' => 'dm',
                'subject' => $c->subject,
                'date' => (string) ($last?->created_at ?? $c->created_at),
                'count' => 1,
                'status' => null,
                'unread' => $c->unreadFor($u),
                'preview' => (string) ($last?->body ?? $c->body)
            ];
        });
        return $threads->concat($notices)->concat($dms)->sortByDesc('date')->values();
    }

    public static function unread(User $u): int
    {
        return (int) self::all($u)->sum('unread');
    }

    /** Fil complet (null si le client n'en est pas propriétaire). */
    public static function find(User $u, string $key): ?array
    {
        [$src, $id] = array_pad(explode('-', $key, 2), 2, null);
        $id = (int) $id;
        if ($src === 'notice') {
            $n = ClientNotice::where('user_id', $u->id)->find($id);
            return $n ? ['key' => $key, 'source' => 'notice', 'subject' => $n->subject, 'status' => null, 'opener' => null, 'notice' => $n, 'messages' => collect()] : null;
        }
        if ($src === 'dm') {
            $c = \App\Models\Conversation::with('sender')->where('recipient_id', $u->id)->find($id);
            return $c ? [
                'key' => $key,
                'source' => 'dm',
                'subject' => $c->subject,
                'status' => null,
                'notice' => null,
                'conversation' => $c,
                'opener' => ['source' => 'dm', 'id' => $c->id, 'date' => $c->created_at, 'subject' => $c->subject, 'body' => $c->body, 'staff' => true],
                'messages' => InboxReply::with('author')->where(['source' => 'dm', 'source_id' => $id])->oldest('id')->get()
            ] : null;
        }
        if (!in_array($src, self::FORM_SOURCES, true) || !($i = Inbox::find($src, $id)) || !self::owns($u, $i))
            return null;
        return [
            'key' => $key,
            'source' => $src,
            'subject' => $src === 'client' ? $i['subject'] : sp_t('src_' . $src) . ($i['subject'] && $i['subject'] !== '—' ? ' — ' . $i['subject'] : ''),
            'status' => $i['status'],
            'opener' => $i,
            'notice' => null,
            'messages' => InboxReply::with('author')->where(['source' => $src, 'source_id' => $id])->oldest('id')->get()
        ];
    }

    public static function markRead(User $u, ?string $key = null): void
    {
        $now = now();
        if ($key === null) {
            foreach (self::all($u)->where('unread', '>', 0) as $t)
                self::markRead($u, $t['key']);
            return;
        }
        $t = self::find($u, $key);
        if (!$t)
            return;
        if ($t['notice']) {
            $t['notice']->read_at ??= $now;
            $t['notice']->save();
            return;
        }
        if ($t['source'] === 'dm') {
            $t['conversation']->markReadFor($u);
            return;
        }
        [$src, $id] = explode('-', $key, 2);
        InboxReply::where(['source' => $src, 'source_id' => (int) $id])->where(fn($q) => $q->whereNull('user_id')->orWhere('user_id', '!=', $u->id))->whereNull('read_at')->update(['read_at' => $now]);
    }
}

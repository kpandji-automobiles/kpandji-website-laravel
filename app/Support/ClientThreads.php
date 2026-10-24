<?php
namespace App\Support;
use App\Models\ClientNotice;
use App\Models\InboxReply;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
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
    /** Charge uniquement les demandes du client. */
    public static function items(User $u, array $sources): Collection
    {
        return Inbox::all($sources, (int) $u->id)
            ->filter(fn($item) => self::owns($u, $item))
            ->values();
    }

    public static function all(User $u): Collection
    {
        $items = self::items($u, self::FORM_SOURCES);
        $replies = collect();

        // Charge les réponses des demandes sélectionnées.
        if ($items->isNotEmpty()) {
            $replies = InboxReply::query()
                ->where(function ($query) use ($items) {
                    foreach ($items->groupBy('source') as $source => $requests) {
                        $ids = $requests->pluck('id')->all();

                        $query->orWhere(function ($group) use ($source, $ids) {
                            $group->where('source', $source)
                                ->whereIn('source_id', $ids);
                        });
                    }
                })
                ->get()
                ->groupBy(
                    fn($reply) => $reply->source . '-' . $reply->source_id
                );
        }
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
        // Chargement groupé des conversations et de leurs informations.
        $dms = \App\Models\Conversation::query()
            ->where('recipient_id', $u->id)
            ->with('latestReply')
            ->withCount([
                'replies as unread_replies_count' => fn($query) => $query
                    ->where('user_id', '!=', $u->id)
                    ->whereNull('read_at'),
            ])
            ->get()
            ->map(function ($conversation) use ($u) {
                $last = $conversation->latestReply;

                $initialUnread = (
                    $conversation->recipient_id === $u->id
                    && !$conversation->read_at
                ) ? 1 : 0;

                return [
                    'key' => 'dm-' . $conversation->id,
                    'source' => 'dm',
                    'subject' => $conversation->subject,
                    'date' => (string) (
                        $last?->created_at ?? $conversation->created_at
                    ),
                    'count' => 1,
                    'status' => null,
                    'unread' => $initialUnread
                        + (int) $conversation->unread_replies_count,
                    'preview' => (string) (
                        $last?->body ?? $conversation->body
                    ),
                ];
            });
        return $threads->concat($notices)->concat($dms)->sortByDesc('date')->values();
    }

    /** Charge les résumés de la page demandée. */
    public static function paginate(
        User $u,
        int $perPage = 10,
        ?int $page = null
    ): LengthAwarePaginator {
        $userId = (int) $u->id;
        $perPage = max(1, min(50, $perPage));
        $page = max(1, $page ?? LengthAwarePaginator::resolveCurrentPage());

        $queries = [];

        // Demandes appartenant au client.
        foreach ([
            'client' => 'client_requests',
            'sira' => 'sira_requests',
            'privilege' => 'privilege_requests',
        ] as $source => $table) {
            $replies = fn() => DB::table('inbox_replies')
                ->where('source', $source)
                ->whereColumn('source_id', $table . '.id');

            $query = DB::table($table)
                ->where($table . '.user_id', $userId);

            if ($userId <= 0) {
                $query->whereRaw('1 = 0');
            }

            // Colonnes communes aux différentes sources.
            $query->selectRaw('? as source', [$source])
                ->addSelect($table . '.id')
                ->selectRaw(
                    $source === 'client'
                    ? "$table.subject as subject, NULL as country"
                    : "$table.city as subject, $table.country as country"
                )
                ->selectSub(
                    $replies()->selectRaw(
                        "COALESCE(MAX(created_at), $table.created_at)"
                    ),
                    'date'
                )
                ->selectSub(
                    $replies()->selectRaw('COUNT(*)'),
                    'count'
                )
                ->selectRaw(
                    "COALESCE(
                    (SELECT status FROM inbox_statuses
                     WHERE source = ? AND source_id = $table.id
                     LIMIT 1),
                    CASE WHEN $table.handled_at IS NULL
                         THEN 'nouveau' ELSE 'traite' END
                ) as status",
                    [$source]
                )
                ->selectSub(
                    $replies()
                        ->whereNull('read_at')
                        ->where(function ($authors) use ($userId) {
                            $authors->whereNull('user_id')
                                ->orWhere('user_id', '!=', $userId);
                        })
                        ->selectRaw('COUNT(*)'),
                    'unread'
                )
                ->selectSub(
                    $replies()->select('body')->orderByDesc('id')->limit(1),
                    'preview'
                )
                ->selectRaw(
                    $source === 'client'
                    ? "$table.message as fallback"
                    : "'' as fallback"
                );

            // Les autres formulaires apparaissent après une réponse.
            if ($source !== 'client') {
                $query->whereExists($replies()->selectRaw('1'));
            }

            $queries[] = $query;
        }

        // Notifications.
        $queries[] = DB::table('client_notices')
            ->where('user_id', $userId)
            ->whereRaw($userId > 0 ? '1 = 1' : '1 = 0')
            ->selectRaw(
                "'notice' as source, id, subject, NULL as country,
             created_at as date, 1 as count, NULL as status,
             CASE WHEN read_at IS NULL THEN 1 ELSE 0 END as unread,
             body as preview, body as fallback"
            );

        // Conversations reçues.
        $dmReplies = fn() => DB::table('inbox_replies')
            ->where('source', 'dm')
            ->whereColumn('source_id', 'conversations.id');

        $queries[] = DB::table('conversations')
            ->where('recipient_id', $userId)
            ->whereRaw($userId > 0 ? '1 = 1' : '1 = 0')
            ->selectRaw(
                "'dm' as source, id, subject, NULL as country"
            )
            ->selectRaw(
                "COALESCE(
                (SELECT created_at FROM inbox_replies
                 WHERE source = 'dm'
                   AND source_id = conversations.id
                 ORDER BY id DESC LIMIT 1),
                conversations.created_at
            ) as date"
            )
            ->selectRaw('1 as count, NULL as status')
            ->selectRaw(
                "(CASE WHEN conversations.read_at IS NULL THEN 1 ELSE 0 END)
             + (SELECT COUNT(*) FROM inbox_replies
                WHERE source = 'dm'
                  AND source_id = conversations.id
                  AND user_id != ?
                  AND read_at IS NULL) as unread",
                [$userId]
            )
            ->selectSub(
                $dmReplies()->select('body')->orderByDesc('id')->limit(1),
                'preview'
            )
            ->selectRaw('conversations.body as fallback');

        // Pagination globale avant le retour des lignes.
        $union = array_shift($queries);

        foreach ($queries as $query) {
            $union->unionAll($query);
        }

        $paginator = DB::query()
            ->fromSub($union, 'threads')
            ->orderByDesc('date')
            ->orderBy('source')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();

        $paginator->setCollection(
            $paginator->getCollection()->map(function ($row) {
                $subject = $row->subject;

                if (in_array($row->source, ['sira', 'privilege'], true)) {
                    $location = $row->subject . ', ' . $row->country;
                    $subject = sp_t('src_' . $row->source) . ' — ' . $location;
                }

                return [
                    'key' => $row->source . '-' . $row->id,
                    'source' => $row->source,
                    'subject' => $subject,
                    'date' => (string) $row->date,
                    'count' => (int) $row->count,
                    'status' => $row->status,
                    'unread' => (int) $row->unread,
                    'preview' => (string) ($row->preview ?? $row->fallback),
                ];
            })
        );

        return $paginator;
    }

    /** Compte les messages non lus en base. */
    public static function unread(User $u): int
    {
        $userId = (int) $u->id;

        if ($userId <= 0) {
            return 0;
        }

        $notices = ClientNotice::query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        $initialMessages = \App\Models\Conversation::query()
            ->where('recipient_id', $userId)
            ->whereNull('read_at')
            ->count();

        $replies = InboxReply::query()
            ->whereNull('read_at')
            ->where(function ($query) use ($userId) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', '!=', $userId);
            })
            ->where(function ($query) use ($userId) {
                // Réponses aux demandes du client.
                foreach ([
                    'client' => 'client_requests',
                    'sira' => 'sira_requests',
                    'privilege' => 'privilege_requests',
                ] as $source => $table) {
                    $query->orWhere(function ($group) use ($source, $table, $userId) {
                        $group->where('source', $source)
                            ->whereIn(
                                'source_id',
                                \Illuminate\Support\Facades\DB::table($table)
                                    ->select('id')
                                    ->where('user_id', $userId)
                            );
                    });
                }

                // Réponses aux conversations reçues.
                $query->orWhere(function ($group) use ($userId) {
                    $group->where('source', 'dm')
                        ->whereNotNull('user_id')
                        ->whereIn(
                            'source_id',
                            \App\Models\Conversation::query()
                                ->select('id')
                                ->where('recipient_id', $userId)
                        );
                });
            })
            ->count();

        return $notices + $initialMessages + $replies;
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

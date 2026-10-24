<?php
namespace App\Support;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/** Boîte de réception de l'espace manager : un onglet par formulaire (contact, essai, S.A.V.) + « autres demandes ». */
class Inbox
{
    public const SOURCES = ['contact', 'essai', 'sav', 'sira', 'privilege', 'emplois', 'client'];
    public const TABS = ['messages' => ['contact'], 'reservations' => ['essai'], 'sav' => ['sav'], 'autres' => ['client', 'sira', 'privilege', 'emplois']];
    public const STATUSES = ['nouveau', 'en_cours', 'traite'];
    public const RES_STATUSES = ['nouveau', 'confirme', 'traite', 'annule'];

    public static function statuses(string $source): array
    {
        return $source === 'essai' ? self::RES_STATUSES : self::STATUSES;
    }
    public static function tabOf(string $source): string
    {
        foreach (self::TABS as $t => $s)
            if (in_array($source, $s, true))
                return $t;
        return 'autres';
    }

    private static function table(string $s): string
    {
        return [
            'contact' => 'test_drive_requests',
            'essai' => 'test_drive_requests',
            'sav' => 'sav_requests',
            'sira' => 'sira_requests',
            'privilege' => 'privilege_requests',
            'emplois' => 'job_applications',
            'client' => 'client_requests'
        ][$s];
    }

    private static function query(string $s)
    {
        $q = DB::table(self::table($s));
        if ($s === 'contact')
            $q->where('test_drive_requests.type', 'contact');
        if ($s === 'essai')
            $q->where('test_drive_requests.type', '!=', 'contact')->leftJoin('cars', 'cars.id', '=', 'test_drive_requests.car_id')->select('test_drive_requests.*', 'cars.name as car_name');
        if ($s === 'client')
            $q->leftJoin('users', 'users.id', '=', 'client_requests.user_id')
                ->select('client_requests.*', 'users.name as u_name', 'users.email as u_email', 'users.phone as u_phone');
        return $q;
    }

    private static function emailOf(?string $v = null): ?string
    {
        return $v && filter_var($v, FILTER_VALIDATE_EMAIL) ? $v : null;
    }

    private static function normalize(string $s, object $r): array
    {
        $b = ['source' => $s, 'id' => (int) $r->id, 'date' => $r->created_at, 'handled' => (bool) ($r->handled_at ?? false), 'raw' => (array) $r, 'model' => null, 'wish_date' => null];
        return array_merge($b, match ($s) {
            'contact' => ['name' => $r->name, 'email' => $r->email, 'phone' => $r->phone, 'subject' => $r->subject ?: '—', 'body' => (string) $r->message],
            'essai' => [
                'name' => $r->name,
                'email' => $r->email,
                'phone' => $r->phone,
                'subject' => $r->car_name ?: ($r->subject ?: '—'),
                'body' => (string) $r->message,
                'model' => $r->car_name,
                'wish_date' => $r->preferred_date,
                'kind' => $r->type
            ],
            'sav' => ['name' => $r->name, 'email' => self::emailOf($r->contact), 'phone' => self::emailOf($r->contact) ? null : $r->contact, 'subject' => $r->model, 'body' => (string) $r->issue, 'model' => $r->model, 'location' => $r->location],
            'sira', 'privilege' => ['name' => $r->name, 'email' => $r->email, 'phone' => $r->phone, 'subject' => $r->city . ', ' . $r->country, 'body' => ''],
            'emplois' => ['name' => $r->full_name, 'email' => $r->email, 'phone' => $r->phone, 'subject' => $r->position, 'body' => (string) $r->cv_summary],
            'client' => ['name' => $r->u_name, 'email' => $r->u_email, 'phone' => $r->u_phone, 'subject' => $r->subject, 'body' => (string) $r->message],
        });
    }

    /** @param string|array|null $sources */
    /** Charge les demandes, avec un propriétaire facultatif. */
    public static function all(
        $sources = null,
        ?int $userId = null
    ): Collection {
        if ($userId !== null && $userId <= 0) {
            return collect();
        }

        $selected = collect(
            $sources ? (array) $sources : self::SOURCES
        );

        // Sources possédant un propriétaire identifié.
        if ($userId !== null) {
            $selected = $selected->intersect([
                'client',
                'sira',
                'privilege',
            ]);
        }

        $items = $selected->flatMap(function ($source) use ($userId) {
            $query = self::query($source);

            // Filtrage avant le chargement des lignes.
            if ($userId !== null) {
                $query->where(
                    self::table($source) . '.user_id',
                    $userId
                );
            }

            return $query->get()->map(
                fn($row) => self::normalize($source, $row)
            );
        });

        // Aucun résultat : aucune métadonnée à charger.
        if ($items->isEmpty()) {
            return collect();
        }

        $groups = $items->groupBy('source');

        // Sélection des métadonnées par source et identifiants.
        $scope = function ($query) use ($groups) {
            foreach ($groups as $source => $requests) {
                $ids = $requests->pluck('id')->all();

                $query->orWhere(function ($group) use ($source, $ids) {
                    $group->where('source', $source)
                        ->whereIn('source_id', $ids);
                });
            }
        };

        $statuses = DB::table('inbox_statuses')
            ->where($scope)
            ->get()
            ->keyBy(fn($row) => $row->source . ':' . $row->source_id);

        $replies = DB::table('inbox_replies')
            ->where($scope)
            ->selectRaw('source, source_id, count(*) n')
            ->groupBy('source', 'source_id')
            ->get()
            ->keyBy(fn($row) => $row->source . ':' . $row->source_id);

        return $items->map(function ($item) use ($statuses, $replies) {
            $key = $item['source'] . ':' . $item['id'];

            $item['status'] = $statuses[$key]->status
                ?? ($item['handled'] ? 'traite' : 'nouveau');

            $item['replies'] = (int) ($replies[$key]->n ?? 0);

            return $item;
        })->sortByDesc('date')->values();
    }

    /** Nombre d'éléments « nouveau » par onglet. */
    public static function newCounts(): array
    {
        $all = self::all();
        return collect(self::TABS)->map(fn($src) => $all->whereIn('source', $src)->where('status', 'nouveau')->count())->all();
    }

    public static function find(string $source, int $id): ?array
    {
        if (!in_array($source, self::SOURCES, true))
            return null;
        $r = self::query($source)->where(self::table($source) . '.id', $id)->first();
        if (!$r)
            return null;
        $i = self::normalize($source, $r);
        $i['status'] = DB::table('inbox_statuses')->where(['source' => $source, 'source_id' => $id])->value('status') ?? ($i['handled'] ? 'traite' : 'nouveau');
        return $i;
    }

    /** Statut + synchronisation de handled_at (écrans admin existants) : traité / annulé = clos. */
    public static function setStatus(string $source, int $id, string $status): void
    {
        DB::table('inbox_statuses')->updateOrInsert(['source' => $source, 'source_id' => $id], ['status' => $status, 'updated_at' => now(), 'created_at' => now()]);
        DB::table(self::table($source))->where('id', $id)->update(['handled_at' => in_array($status, ['traite', 'annule'], true) ? now() : null]);
    }
}

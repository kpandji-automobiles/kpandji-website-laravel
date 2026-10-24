<?php
namespace App\Support;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

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

    /** Charge uniquement les demandes de la page. */
    public static function paginate(
        array $sources,
        array $filters = [],
        int $perPage = 10,
        ?int $page = null
    ): LengthAwarePaginator {
        // Refuse les filtres non encore pris en charge.
        if (
            array_diff(
                array_keys($filters),
                ['status', 'replied', 'from', 'to', 'source', 'q']
            ) !== []
        ) {
            throw new \InvalidArgumentException('Filtre non pris en charge.');
        }

        $sources = array_values(array_unique($sources));

        if (
            $sources === []
            || array_diff($sources, self::SOURCES) !== []
        ) {
            throw new \InvalidArgumentException('Sources invalides.');
        }

        $perPage = max(1, min(50, $perPage));
        $page = max(1, $page ?? LengthAwarePaginator::resolveCurrentPage());

        $union = null;


        // Index léger des demandes accessibles dans ces sources.
        foreach ($sources as $source) {
            $table = self::table($source);

            $query = self::query($source)
                ->select([])
                ->selectRaw('? as source', [$source])
                ->addSelect($table . '.id', $table . '.created_at as date')
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
                    DB::table('inbox_replies')
                        ->where('source', $source)
                        ->whereColumn('source_id', $table . '.id')
                        ->selectRaw('COUNT(*)'),
                    'replies'
                );

            // Recherche dans les champs de la source.
            if (isset($filters['q']) && trim($filters['q']) !== '') {
                $columns = match ($source) {
                    'contact' => [
                        'test_drive_requests.name',
                        'test_drive_requests.email',
                        'test_drive_requests.subject',
                    ],
                    'essai' => [
                        'test_drive_requests.name',
                        'test_drive_requests.email',
                        'test_drive_requests.subject',
                        'cars.name',
                    ],
                    'sav' => [
                        'sav_requests.name',
                        'sav_requests.contact',
                        'sav_requests.model',
                    ],
                    'sira', 'privilege' => [
                        $table . '.name',
                        $table . '.email',
                        $table . '.city',
                        $table . '.country',
                    ],
                    'emplois' => [
                        'job_applications.full_name',
                        'job_applications.email',
                        'job_applications.position',
                    ],
                    'client' => [
                        'users.name',
                        'users.email',
                        'client_requests.subject',
                    ],
                };

                $term = mb_strtolower(trim($filters['q']));

                // Recherche littérale des caractères spéciaux de LIKE.
                $term = str_replace(
                    ['!', '%', '_'],
                    ['!!', '!%', '!_'],
                    $term
                );

                $query->where(function ($search) use ($columns, $term) {
                    foreach ($columns as $column) {
                        $search->orWhereRaw(
                            "LOWER(COALESCE($column, '')) LIKE ? ESCAPE '!'",
                            ['%' . $term . '%']
                        );
                    }
                });
            }

            if ($union === null) {
                $union = $query;
            } else {
                $union->unionAll($query);
            }
        }

        // Limite appliquée avant le chargement du contenu.
        $query = DB::query()->fromSub($union, 'requests');

        // Filtrage avant le calcul des pages.
        if (
            isset($filters['status'])
            && $filters['status'] !== ''
        ) {
            $query->where('status', $filters['status']);
        }


        // Demandes avec ou sans réponse.
        if (($filters['replied'] ?? null) === '1') {
            $query->where('replies', '>', 0);
        } elseif (($filters['replied'] ?? null) === '0') {
            $query->where('replies', 0);
        }

        // Journées de début et de fin incluses.
        if (isset($filters['from']) && $filters['from'] !== '') {
            $query->whereDate('date', '>=', $filters['from']);
        }

        if (isset($filters['to']) && $filters['to'] !== '') {
            $query->whereDate('date', '<=', $filters['to']);
        }

        // Limite les résultats à une source de l’onglet.
        if (isset($filters['source']) && $filters['source'] !== '') {
            if (!in_array($filters['source'], $sources, true)) {
                throw new \InvalidArgumentException(
                    'Source non autorisée dans cet onglet.'
                );
            }

            $query->where('source', $filters['source']);
        }

        $paginator = $query
            ->orderByDesc('date')
            ->orderBy('source')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();



        $index = $paginator->getCollection();
        $details = collect();

        // Contenu chargé seulement pour les identifiants de la page.
        foreach ($index->groupBy('source') as $source => $rows) {
            $table = self::table($source);

            $requests = self::query($source)
                ->whereIn($table . '.id', $rows->pluck('id')->all())
                ->get();

            foreach ($requests as $request) {
                $details->put(
                    $source . ':' . $request->id,
                    self::normalize($source, $request)
                );
            }
        }

        // Reconstruction dans l’ordre de la page.
        $items = $index->map(function ($row) use ($details) {
            $item = $details->get($row->source . ':' . $row->id);

            if ($item === null) {
                return null;
            }

            $item['status'] = $row->status;
            $item['replies'] = (int) $row->replies;

            return $item;
        })->filter()->values();

        $paginator->setCollection($items);

        return $paginator;
    }

    /** Nombre d'éléments « nouveau » par onglet. */
    /** Compte les nouvelles demandes en base. */
    public static function newCounts(): array
    {
        $counts = [];

        foreach (self::TABS as $tab => $sources) {
            $counts[$tab] = 0;

            foreach ($sources as $source) {
                $table = self::table($source);

                $query = DB::table($table)
                    ->leftJoin('inbox_statuses as status', function ($join) use ($table, $source) {
                        $join->on('status.source_id', '=', $table . '.id')
                            ->where('status.source', '=', $source);
                    });

                // Distingue les contacts des demandes d’essai.
                if ($source === 'contact') {
                    $query->where($table . '.type', 'contact');
                } elseif ($source === 'essai') {
                    $query->where($table . '.type', '!=', 'contact');
                }

                // Le statut explicite est prioritaire.
                $query->where(function ($statuses) use ($table) {
                    $statuses->where('status.status', 'nouveau')
                        ->orWhere(function ($fallback) use ($table) {
                            $fallback->whereNull('status.status')
                                ->whereNull($table . '.handled_at');
                        });
                });

                $counts[$tab] += (int) $query->count($table . '.id');
            }
        }

        return $counts;
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

    /** Compte les demandes par statut. */
    public static function statusCounts(array $sources): Collection
    {
        $counts = collect();

        foreach (array_unique($sources) as $source) {
            if (!in_array($source, self::SOURCES, true)) {
                throw new \InvalidArgumentException('Source invalide.');
            }

            $table = self::table($source);

            $query = self::query($source)
                ->select([])
                ->selectRaw(
                    "COALESCE(
                    (SELECT status FROM inbox_statuses
                     WHERE source = ? AND source_id = $table.id
                     LIMIT 1),
                    CASE WHEN $table.handled_at IS NULL
                         THEN 'nouveau' ELSE 'traite' END
                ) as status",
                    [$source]
                );

            $groups = DB::query()
                ->fromSub($query, 'request_statuses')
                ->select('status')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('status')
                ->get();

            foreach ($groups as $group) {
                $counts->put(
                    $group->status,
                    (int) $counts->get($group->status, 0)
                    + (int) $group->total
                );
            }
        }

        return $counts;
    }
}

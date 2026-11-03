<?php

namespace App\Support;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Filtres du catalogue /modeles : recherche, catégorie, énergie, boîte, budget maximum et tri.
 * La gamme compte une dizaine de modèles : le filtrage se fait sur la collection déjà chargée.
 * Énergie et boîte n'ont pas de colonne : elles sont déduites des textes du modèle (Car::searchableText).
 */
final class Catalogue
{
    public const ENERGIES = ['essence' => 'Essence', 'diesel' => 'Diesel', 'hybride' => 'Hybride', 'electrique' => 'Électrique'];

    public const GEARBOXES = ['automatique' => 'Automatique', 'manuelle' => 'Manuelle'];

    public const SORTS = ['' => 'Recommandés', 'prix-asc' => 'Prix croissant', 'prix-desc' => 'Prix décroissant'];

    private const ENERGY_PATTERNS = [
        'essence' => '/(essence|\bgdi\b)/iu',
        'diesel' => '/diesel/iu',
        'hybride' => '/(hybride|\bphev\b)/iu',
        'electrique' => '/(électrique|electrique|\bev\b)/iu',
    ];

    private const GEARBOX_PATTERNS = [
        'automatique' => '/(automatique|\bbva\b|\bcvt\b|\b\d+at\b)/iu',
        'manuelle' => '/(manuelle|\bbvm\b)/iu',
    ];

    /** @return list<string> */
    public static function energiesOf(Car $car): array
    {
        return self::matching(self::ENERGY_PATTERNS, $car->searchableText());
    }

    /** @return list<string> */
    public static function gearboxesOf(Car $car): array
    {
        return self::matching(self::GEARBOX_PATTERNS, $car->searchableText());
    }

    /**
     * Filtres lus dans la requête : les valeurs inconnues sont ignorées plutôt que rejetées.
     *
     * @param  Collection<int, Car>  $cars
     * @return array{q: string, type: string, energie: string, boite: string, budget: ?int, tri: string}
     */
    public static function filtersFrom(Request $request, Collection $cars): array
    {
        $string = fn (string $key): string => is_string($request->query($key)) ? trim($request->query($key)) : '';
        $budget = (int) preg_replace('/\D/', '', mb_substr($string('budget'), 0, 20));

        return [
            'q' => mb_substr($string('q'), 0, 100),
            'type' => $cars->pluck('category')->contains($string('type')) ? $string('type') : '',
            'energie' => array_key_exists($string('energie'), self::ENERGIES) ? $string('energie') : '',
            'boite' => array_key_exists($string('boite'), self::GEARBOXES) ? $string('boite') : '',
            'budget' => $budget > 0 ? $budget : null,
            'tri' => array_key_exists($string('tri'), self::SORTS) ? $string('tri') : '',
        ];
    }

    /**
     * @param  Collection<int, Car>  $cars
     * @param  array{q: string, type: string, energie: string, boite: string, budget: ?int, tri: string}  $filters
     * @param  string|null  $except  filtre ignoré, pour compter les résultats de chaque option (facettes)
     * @return Collection<int, Car>
     */
    public static function apply(Collection $cars, array $filters, ?string $except = null): Collection
    {
        $terms = array_filter(explode(' ', self::normalize($filters['q'])));

        $result = $cars
            ->filter(fn (Car $car) => $terms === [] || collect($terms)->every(fn (string $term) => str_contains(self::normalize($car->searchableText()), $term)))
            ->when($filters['type'] !== '' && $except !== 'type', fn (Collection $list) => $list->where('category', $filters['type']))
            ->when($filters['energie'] !== '' && $except !== 'energie', fn (Collection $list) => $list->filter(fn (Car $car) => in_array($filters['energie'], self::energiesOf($car), true)))
            ->when($filters['boite'] !== '' && $except !== 'boite', fn (Collection $list) => $list->filter(fn (Car $car) => in_array($filters['boite'], self::gearboxesOf($car), true)))
            ->when($filters['budget'] !== null, fn (Collection $list) => $list->filter(fn (Car $car) => $car->startingPrice() !== null && $car->startingPrice() <= $filters['budget']));

        if ($filters['tri'] !== '') {
            $descending = $filters['tri'] === 'prix-desc';
            // Les modèles sans prix restent en fin de liste dans les deux sens.
            $result = $result->sortBy([
                fn (Car $a, Car $b) => ($a->startingPrice() === null) <=> ($b->startingPrice() === null),
                fn (Car $a, Car $b) => $descending ? $b->startingPrice() <=> $a->startingPrice() : $a->startingPrice() <=> $b->startingPrice(),
            ]);
        }

        return $result->values();
    }

    /** @param  array{q: string, type: string, energie: string, boite: string, budget: ?int, tri: string}  $filters */
    public static function isFiltered(array $filters): bool
    {
        return collect($filters)->except('tri')->filter(fn ($value) => $value !== '' && $value !== null)->isNotEmpty();
    }

    /**
     * Lien vers le catalogue avec des filtres modifiés (null ou '' retire le filtre).
     *
     * @param  array{q: string, type: string, energie: string, boite: string, budget: ?int, tri: string}  $filters
     * @param  array<string, mixed>  $changes
     */
    public static function url(array $filters, array $changes = []): string
    {
        return route('cars.index', array_filter(array_merge($filters, $changes), fn ($value) => $value !== '' && $value !== null));
    }

    /**
     * @param  array<string, string>  $patterns
     * @return list<string>
     */
    private static function matching(array $patterns, string $text): array
    {
        return array_keys(array_filter($patterns, fn (string $pattern) => preg_match($pattern, $text) === 1));
    }

    private static function normalize(string $text): string
    {
        return Str::lower(Str::ascii($text));
    }
}

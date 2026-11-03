<?php

namespace App\Models;

use App\Support\PublicUpload;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    /** Champs fichier dont l'ancien upload est supprimé quand il est remplacé ou que la voiture est supprimée. */
    private const UPLOADS = ['image', 'hero_image', 'brochure'];

    protected $guarded = [];

    protected $casts = ['highlights' => 'array', 'specs' => 'array', 'versions' => 'array', 'is_active' => 'boolean'];

    public function scopeVisible($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function hasVersions(): bool
    {
        return count($this->versions ?? []) > 1;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::updated(function (Car $car) {
            $kept = array_map(fn ($field) => $car->{$field}, self::UPLOADS);
            foreach (self::UPLOADS as $field) {
                $old = $car->getOriginal($field);
                if ($car->wasChanged($field) && ! in_array($old, $kept, true)) {
                    PublicUpload::delete($old);
                }
            }
        });
        static::deleted(function (Car $car) {
            foreach (array_unique(array_filter(array_map(fn ($field) => $car->{$field}, self::UPLOADS))) as $path) {
                PublicUpload::delete($path);
            }
        });
    }

    public function discountedPrice(): ?int
    {
        if ($this->promo_price) {
            return (int) $this->promo_price;
        }
        $pct = (float) config('promo.reduction_percent');
        if ($this->price && $pct > 0) {
            return (int) round($this->price * (1 - $pct / 100));
        }

        return null;
    }

    public static function fcfa(?int $v): string
    {
        return $v ? number_format($v, 0, ',', ' ').' '.config('promo.currency') : 'Prix sur demande';
    }

    /**
     * Caractéristiques saisies en texte : « ## Groupe » puis « Libellé : valeur » par ligne.
     *
     * @return array<string, array<string, string>>|null
     */
    public static function parseSpecs(?string $text): ?array
    {
        $out = [];
        $group = 'Caractéristiques';
        foreach (preg_split('/\r?\n/', (string) $text) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (str_starts_with($line, '##')) {
                $group = trim(ltrim($line, '#')) ?: $group;

                continue;
            }
            [$k, $v] = array_pad(array_map('trim', explode(':', $line, 2)), 2, '');
            $out[$group][$k] = $v;
        }

        return $out ?: null;
    }

    /** Inverse de parseSpecs(), pour l'édition. */
    public static function specsToText(?array $specs): string
    {
        $lines = [];
        foreach ($specs ?? [] as $group => $rows) {
            $lines[] = '## '.$group;
            foreach ((array) $rows as $k => $v) {
                $lines[] = $k.' : '.$v;
            }
        }

        return implode("\n", $lines);
    }
}

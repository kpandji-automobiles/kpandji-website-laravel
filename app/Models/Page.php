<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class Page extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['published' => 'boolean', 'in_nav' => 'boolean', 'is_system' => 'boolean'];

    /** Slugs réservés (routes existantes). */
    public const RESERVED = ['admin', 'modeles', 'showroom', 'essai', 'demande-essai', 'page', 'storage', 'images', 'login', 'logout', 'up', 'api', 'build', 'video.mp4'];

    /**
     * Slugs interdits : la liste ci-dessus et, pour une page libre, le premier segment de chaque route
     * déclarée (« contact », « sav »…), qui passerait avant la page.
     *
     * @return list<string>
     */
    public static function reservedSlugs(bool $isSystem = false): array
    {
        if ($isSystem) {
            return self::RESERVED;
        }
        $taken = collect(Route::getRoutes())
            ->map(fn ($route) => explode('/', trim($route->uri(), '/'))[0])
            ->filter(fn ($segment) => $segment !== '' && ! str_contains($segment, '{'));

        return array_values(array_unique(array_merge(self::RESERVED, $taken->all())));
    }

    public function url(): string
    {
        return url('/'.$this->slug);
    }
}

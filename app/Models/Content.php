<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Content extends Model
{
    protected $guarded = [];
    public const CACHE = 'kp.contents';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE));
        static::deleted(fn () => Cache::forget(self::CACHE));
    }

    /** Valeurs enregistrées [clé => valeur brute] (mises en cache). */
    public static function allCached(): array
    {
        return Cache::rememberForever(self::CACHE, function () {
            try {
                return Schema::hasTable('contents') ? static::pluck('value', 'key')->all() : [];
            } catch (\Throwable) {
                return [];
            }
        });
    }

    public static function field(string $key): ?array
    {
        return config('cms.fields')[$key] ?? null;
    }

    /** Valeur effective : base de données sinon valeur d'origine du registre. */
    public static function get(string $key, $default = null)
    {
        $def = static::field($key);
        $all = static::allCached();
        if (array_key_exists($key, $all) && $all[$key] !== null) {
            $raw = $all[$key];
            if (($def[1] ?? null) === 'list') {
                $arr = json_decode($raw, true);
                return is_array($arr) ? $arr : ($def[3] ?? []);
            }
            return $raw;
        }
        return $def ? $def[3] : $default;
    }
}

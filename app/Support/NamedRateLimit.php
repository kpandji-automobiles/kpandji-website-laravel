<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use InvalidArgumentException;

/**
 * Applique un limiteur nommé (RateLimiter::for) hors middleware, par exemple dans une action Filament.
 * Les clés sont celles du middleware throttle:{nom} : le quota est partagé avec les routes qui l'utilisent.
 */
final class NamedRateLimit
{
    /** Compte une tentative ; false si la limite est déjà atteinte (rien n'est alors compté). */
    public static function attempt(string $name, Request $request): bool
    {
        $limiter = RateLimiter::limiter($name) ?? throw new InvalidArgumentException("Limiteur [{$name}] inconnu.");
        $limits = Collection::wrap($limiter($request));

        foreach ($limits as $limit) {
            if (RateLimiter::tooManyAttempts(md5($name.$limit->key), $limit->maxAttempts)) {
                return false;
            }
        }

        foreach ($limits as $limit) {
            RateLimiter::hit(md5($name.$limit->key), $limit->decaySeconds);
        }

        return true;
    }
}

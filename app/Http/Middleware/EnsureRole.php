<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** role:manager -> manager ou admin ; role:client -> client uniquement. Invité -> page /connexion (URL demandée conservée). */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $u = $request->user();
        if (! $u) {
            return redirect()->guest(route('connexion'));
        }
        if (! $u->isActive()) {
            return redirect()->route('space.waiting');
        }
        $ok = match ($role) {
            'manager' => $u->isManager(), 'client' => $u->isClient(), default => false
        };
        abort_unless($ok, 403);

        return $next($request);
    }
}

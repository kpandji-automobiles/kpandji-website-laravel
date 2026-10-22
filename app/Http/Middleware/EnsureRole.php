<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
/** role:manager -> manager ou admin ; role:client -> client uniquement. Invité -> accueil + fenêtre de connexion. */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $u = $request->user();
        if (! $u) return redirect()->guest(route('home'))->with('kp_login_open', true);
        if (! $u->isActive()) return redirect()->route('space.waiting');
        $ok = match ($role) { 'manager' => $u->isManager(), 'client' => $u->isClient(), default => false };
        abort_unless($ok, 403);
        return $next($request);
    }
}

<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) return redirect()->route('login');
        // Manager / client connecté : accès refusé sans le déconnecter
        abort_unless($request->user()->isAdmin(), 403, 'Accès réservé aux administrateurs.');
        return $next($request);
    }
}

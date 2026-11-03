<?php

use App\Http\Controllers\DeployController;
use App\Http\Controllers\DeployReleaseController;
use App\Http\Middleware\EnsureRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            // Hors du groupe web (ni session ni CSRF) : appelé par GitHub Actions, protégé par DEPLOY_HOOK_TOKEN.
            // Pas de throttle : son cache en base n'existe pas avant la première migration.
            Route::post('/_deploy/finalize', DeployController::class)
                ->name('deploy.finalize');
            // Exécutées par la release en ligne : décompression de la nouvelle release, purge des anciennes.
            Route::post('/_deploy/unpack', [DeployReleaseController::class, 'unpack'])
                ->name('deploy.unpack');
            Route::post('/_deploy/prune', [DeployReleaseController::class, 'prune'])
                ->name('deploy.prune');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['role' => EnsureRole::class]);
        // Invités : page de connexion du site (les panneaux Filament ont la leur).
        $middleware->redirectGuestsTo('/connexion');
        // Vérifie la cohérence des sessions authentifiées.
        $middleware->authenticateSessions();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

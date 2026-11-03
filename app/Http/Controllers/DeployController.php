<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Finalise un déploiement sur l'hébergement mutualisé (pas d'accès shell) :
 * appelé par GitHub Actions une fois la nouvelle release en ligne.
 */
class DeployController extends Controller
{
    /**
     * Commandes Artisan exécutées, dans l'ordre, après la bascule de release.
     *
     * @var array<string, array<string, mixed>>
     */
    private const COMMANDS = [
        'migrate' => ['--force' => true],
        'storage:link' => [],
        'optimize' => [],
    ];

    public function __invoke(Request $request): JsonResponse
    {
        $token = (string) config('services.deploy.token');

        abort_if($token === '' || ! hash_equals($token, (string) $request->bearerToken()), 404);

        $expectedRelease = (string) $request->input('release');
        $currentRelease = $this->currentRelease();

        // Le cache realpath de PHP peut servir l'ancienne release quelques instants après la bascule du lien.
        if ($expectedRelease === '' || ! hash_equals($currentRelease, $expectedRelease)) {
            return response()->json(['status' => 'stale', 'release' => $currentRelease], 409);
        }

        $output = [];

        foreach (self::COMMANDS as $command => $arguments) {
            $exitCode = Artisan::call($command, $arguments);
            $output[$command] = trim(Artisan::output());

            if ($exitCode !== 0) {
                return response()->json(['status' => 'failed', 'command' => $command, 'output' => $output], 500);
            }
        }

        return response()->json(['status' => 'ok', 'release' => $currentRelease, 'output' => $output]);
    }

    private function currentRelease(): string
    {
        $path = base_path('RELEASE');

        return is_file($path) ? trim((string) file_get_contents($path)) : '';
    }
}

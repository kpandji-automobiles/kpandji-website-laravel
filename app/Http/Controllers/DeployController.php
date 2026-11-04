<?php

namespace App\Http\Controllers;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

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

        $output['admin'] = $this->ensureAdminAccount();

        return response()->json(['status' => 'ok', 'release' => $currentRelease, 'output' => $output]);
    }

    /**
     * Crée le compte admin (SEED_ADMIN_EMAIL / SEED_ADMIN_PASSWORD de shared/.env) s'il n'existe pas.
     * Ne modifie jamais un compte existant. La réponse finit dans les journaux GitHub Actions (dépôt
     * public) : elle ne contient ni l'e-mail ni le mot de passe.
     */
    private function ensureAdminAccount(): string
    {
        $email = Str::lower(trim((string) config('services.deploy.admin_email')));
        $password = (string) config('services.deploy.admin_password');

        if ($email === '' || $password === '') {
            return 'not-configured';
        }

        if (mb_strlen($password) < 12) {
            return 'password-too-short';
        }

        if (User::where('email', $email)->exists()) {
            return 'exists';
        }

        (new User)->forceFill(AdminSeeder::ATTRIBUTES + ['email' => $email, 'password' => $password])->save();

        return 'created';
    }

    private function currentRelease(): string
    {
        $path = base_path('RELEASE');

        return is_file($path) ? trim((string) file_get_contents($path)) : '';
    }
}

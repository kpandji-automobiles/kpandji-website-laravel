<?php

namespace App\Http\Controllers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ZipArchive;

/**
 * Travail de fichiers côté serveur pour deploy.sh (pas d'accès shell) : un envoi SFTP fichier par
 * fichier coûte un aller-retour réseau par fichier (~7 000 dans vendor/), on envoie donc une archive
 * que la release en ligne décompresse, et on supprime les anciennes releases sur place.
 */
class DeployReleaseController extends Controller
{
    /** Nom des dossiers de releases/ : AAAAMMJJhhmmss-sha7. */
    private const RELEASE = '/^\d{14}-[0-9a-f]{7}$/';

    private const ARCHIVE = 'release.zip';

    public function __construct(private readonly Filesystem $files) {}

    /** Décompresse releases/<release>/release.zip dans son dossier, puis supprime l'archive. */
    public function unpack(Request $request): JsonResponse
    {
        $this->authorizeDeploy($request);

        $release = (string) $request->input('release');
        abort_unless(preg_match(self::RELEASE, $release) === 1, 422, 'Release invalide.');

        if (! class_exists(ZipArchive::class)) {
            return response()->json(['status' => 'unsupported'], 501);
        }

        $target = $this->releasesPath().'/'.$release;
        $archive = $target.'/'.self::ARCHIVE;

        if (! is_file($archive)) {
            return response()->json(['status' => 'missing', 'archive' => self::ARCHIVE], 422);
        }

        $zip = new ZipArchive;

        if ($zip->open($archive) !== true) {
            return response()->json(['status' => 'unreadable'], 422);
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = (string) $zip->getNameIndex($i);

            if (! $this->isSafeEntry($entry)) {
                $zip->close();

                return response()->json(['status' => 'unsafe', 'entry' => $entry], 422);
            }
        }

        @set_time_limit(300);
        $count = $zip->numFiles;
        $extracted = $zip->extractTo($target);
        $zip->close();

        if (! $extracted) {
            return response()->json(['status' => 'failed'], 500);
        }

        unlink($archive);

        return response()->json(['status' => 'ok', 'release' => $release, 'entries' => $count]);
    }

    /** Supprime les plus anciennes releases au-delà de `keep`, sans jamais toucher aux releases protégées. */
    public function prune(Request $request): JsonResponse
    {
        $this->authorizeDeploy($request);

        $keep = max(2, (int) $request->input('keep', 5));
        $releases = collect($this->files->directories($this->releasesPath()))
            ->map(fn (string $path) => basename($path))
            ->filter(fn (string $name) => preg_match(self::RELEASE, $name) === 1)
            ->sort()
            ->values();

        $protected = collect((array) $request->input('protect', []))
            ->push(basename(base_path()), $this->currentRelease())
            ->filter()
            ->all();

        $removed = $releases->take(max(0, $releases->count() - $keep))
            ->reject(fn (string $name) => in_array($name, $protected, true))
            ->each(fn (string $name) => $this->files->deleteDirectory($this->releasesPath().'/'.$name))
            ->values();

        return response()->json(['status' => 'ok', 'removed' => $removed]);
    }

    private function authorizeDeploy(Request $request): void
    {
        $token = (string) config('services.deploy.token');

        abort_if($token === '' || ! hash_equals($token, (string) $request->bearerToken()), 404);
    }

    /** Dossier releases/ : parent de la release qui exécute ce code. */
    private function releasesPath(): string
    {
        return rtrim((string) (config('services.deploy.releases_path') ?: dirname(base_path())), '/');
    }

    /** Release pointée par le lien current (frère de releases/), si on peut la lire. */
    private function currentRelease(): ?string
    {
        $link = @readlink(dirname($this->releasesPath()).'/current');

        return $link === false ? null : basename($link);
    }

    /** Refuse les chemins absolus et les remontées (zip slip). */
    private function isSafeEntry(string $name): bool
    {
        if ($name === '' || str_starts_with($name, '/') || str_contains($name, '\\') || str_contains($name, ':')) {
            return false;
        }

        return ! in_array('..', explode('/', $name), true);
    }
}

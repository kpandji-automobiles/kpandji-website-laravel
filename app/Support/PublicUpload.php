<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Fichiers publics stockés en base sous la forme « storage/<dossier>/<fichier> »
 * (servis par le lien public/storage), alors que Filament manipule le chemin du disque « public ».
 */
final class PublicUpload
{
    private const PREFIX = 'storage/';

    /** Chemin en base → chemin du disque public, ou null pour un fichier hors du disque (ex. images/…). */
    public static function toDisk(?string $stored): ?string
    {
        return $stored !== null && str_starts_with($stored, self::PREFIX) ? substr($stored, strlen(self::PREFIX)) : null;
    }

    /** Chemin du disque public → chemin en base. */
    public static function toStored(?string $diskPath): ?string
    {
        return filled($diskPath) ? self::PREFIX.ltrim($diskPath, '/') : null;
    }

    /** Supprime le fichier s'il vit sur le disque public ; ignore les fichiers livrés avec le site. */
    public static function delete(?string $stored): void
    {
        $diskPath = self::toDisk($stored);

        if ($diskPath !== null) {
            Storage::disk('public')->delete($diskPath);
        }
    }
}

<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Arr;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Champ média du back-office : le chemin public reste visible et modifiable (fichiers livrés
 * avec le site comme « models/lath/pic3.jpg »), et un envoi de fichier le remplace par « storage/<dossier>/… ».
 */
final class MediaPath
{
    /** Chemin relatif sûr ou URL http(s), comme l'ancien admin. */
    public const SAFE_PATH = '~^(https?://\S+|[A-Za-z0-9_\-./ ()]+)$~';

    private const TYPES = [
        'image' => [['image/jpeg', 'image/png', 'image/webp'], 20480],
        'video' => [['video/mp4', 'video/webm', 'video/quicktime'], 204800],
        'pdf' => [['application/pdf'], 51200],
    ];

    public static function make(string $field, string $label, string $directory, string $type = 'image'): Group
    {
        [$mimes, $maxKb] = self::TYPES[$type];
        $upload = $field.'_upload';

        return Group::make([
            TextInput::make($field)
                ->label($label)
                ->maxLength(255)
                ->regex(self::SAFE_PATH)
                ->notRegex('~\.\.~')
                ->placeholder('Chemin public ou URL')
                ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? ltrim(trim($state), '/') : null),
            FileUpload::make($upload)
                ->hiddenLabel()
                ->disk('public')
                ->directory($directory)
                ->acceptedFileTypes($mimes)
                ->maxSize($maxKb)
                ->storeFiles(false)
                ->dehydrated(false)
                ->live()
                ->helperText('Envoyer un fichier remplace le chemin ci-dessus.')
                ->afterStateUpdated(function (Set $set, $state) use ($field, $upload, $directory): void {
                    $file = is_array($state) ? Arr::first($state) : $state;

                    if ($file instanceof TemporaryUploadedFile) {
                        $set($field, 'storage/'.$file->store($directory, 'public'));
                        $set($upload, null);
                    }
                }),
        ]);
    }
}

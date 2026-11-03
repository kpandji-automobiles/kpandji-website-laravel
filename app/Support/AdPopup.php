<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Fenêtre publicitaire du site (image + lien), réglée dans le back-office (page « Fenêtre publicitaire »).
 * Réglages stockés dans settings sous les clés popup.*.
 */
final class AdPopup
{
    public const KEYS = ['active', 'image', 'title', 'text', 'link', 'cta', 'starts_at', 'ends_at', 'scope', 'frequency'];

    public const SCOPES = ['home' => 'Accueil uniquement', 'site' => 'Toutes les pages du site public'];

    public const FREQUENCIES = [
        'session' => 'Une fois par visite',
        'day' => 'Une fois par jour',
        'week' => 'Une fois par semaine',
        'always' => 'À chaque page',
    ];

    /** Pages où la fenêtre ne s'affiche jamais : comptes, espaces privés, connexion. */
    private const EXCLUDED_ROUTES = ['connexion', 'client.*', 'space.*', 'invitation.*'];

    /**
     * Réglages enregistrés, avec leurs valeurs par défaut.
     *
     * @return array<string, string>
     */
    public static function settings(): array
    {
        $saved = Setting::allCached();
        $defaults = ['active' => '0', 'cta' => 'Découvrir', 'scope' => 'home', 'frequency' => 'day'];

        return collect(self::KEYS)
            ->mapWithKeys(fn (string $key): array => [$key => (string) ($saved['popup.'.$key] ?? $defaults[$key] ?? '')])
            ->all();
    }

    /**
     * La fenêtre à afficher sur cette page, ou null.
     *
     * @return array{image: string, title: string, text: string, link: string, cta: string, frequency: string, version: string}|null
     */
    public static function current(Request $request): ?array
    {
        $popup = self::settings();
        $today = Carbon::today();

        $hidden = $popup['active'] !== '1'
            || $popup['image'] === ''
            || ($popup['starts_at'] !== '' && $today->lt(Carbon::parse($popup['starts_at'])))
            || ($popup['ends_at'] !== '' && $today->gt(Carbon::parse($popup['ends_at'])))
            || ($popup['scope'] === 'home' && ! $request->routeIs('home'))
            || $request->routeIs(...self::EXCLUDED_ROUTES);

        if ($hidden) {
            return null;
        }

        return [
            'image' => $popup['image'],
            'title' => $popup['title'],
            'text' => $popup['text'],
            'link' => $popup['link'],
            'cta' => $popup['cta'],
            'frequency' => array_key_exists($popup['frequency'], self::FREQUENCIES) ? $popup['frequency'] : 'day',
            // Change à chaque nouvelle campagne : un visiteur qui a fermé l'ancienne voit la nouvelle.
            'version' => substr(md5(implode('|', [$popup['image'], $popup['title'], $popup['link'], $popup['starts_at']])), 0, 12),
        ];
    }
}

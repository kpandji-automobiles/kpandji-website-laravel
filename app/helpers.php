<?php

use App\Models\Content;

if (! function_exists('content')) {
    /** Contenu éditable (admin › Contenu). Les listes sont renvoyées en tableau. */
    function content(string $key, $default = null)
    {
        return Content::get($key, $default);
    }
}

if (! function_exists('cms_url')) {
    /** Transforme un lien saisi (« /showroom », « #offres », « https://… ») en URL. */
    function cms_url(?string $v): string
    {
        $v = trim((string) $v);
        if ($v === '') {
            return '#';
        }
        if (preg_match('~^(https?:|mailto:|tel:|#)~i', $v)) {
            return $v;
        }
        if (str_starts_with($v, '/#')) {
            return url('/').substr($v, 1);
        }

        return url($v);
    }
}

if (! function_exists('cms_asset')) {
    /** Fichier image/vidéo : chemin public (« images/logo.png », « storage/cms/… ») ou URL complète. */
    function cms_asset(?string $v): string
    {
        $v = (string) $v;

        return preg_match('~^https?://~i', $v) ? $v : asset($v);
    }
}

if (! function_exists('cms_active')) {
    /** Lien de menu actif pour la page courante. */
    function cms_active(?string $v): bool
    {
        $v = trim((string) $v);
        if ($v === '' || preg_match('~^(https?:|mailto:|tel:|#)~i', $v)) {
            return false;
        }
        $path = trim(parse_url($v, PHP_URL_PATH) ?? '', '/');
        $cur = trim(request()->path(), '/');
        if ($path === '') {
            return $cur === '';
        }
        if ($path === 'showroom' && request()->routeIs('cars.*')) {
            return true;
        }

        return $cur === $path;
    }
}

if (! function_exists('sp_t')) {
    /** Libellé des espaces Manager/Client (onglet CMS « espaces »), FR/EN. */
    function sp_t(string $k): string
    {
        $v = app()->getLocale() === 'en' ? (content("sp.{$k}_en") ?: content("sp.$k")) : content("sp.$k");

        return (string) ($v ?? $k);
    }
}
if (! function_exists('fcfa')) {
    function fcfa($n): string
    {
        return number_format((float) $n, 0, ',', ' ').' FCFA';
    }
}

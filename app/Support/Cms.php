<?php
namespace App\Support;

class Cms
{
    public const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><a><ul><ol><li><h2><h3><h4><blockquote><img><hr><span>';

    /** Nettoie le HTML du CMS selon une liste d’autorisation. */
    public static function cleanHtml(?string $html): string
{
    $config = \HTMLPurifier_Config::createDefault();

    $config->set('Core.Encoding', 'UTF-8');

    $config->set(
        'HTML.Allowed',
        'p,br,strong,b,em,i,u,a[href|title],ul,ol,li,' .
        'h2,h3,h4,blockquote,img[src|alt|title|width|height],hr,span'
    );

    $config->set('URI.AllowedSchemes', [
        'https' => true,
        'http' => true,
        'mailto' => true,
        'tel' => true,
    ]);

    $config->set('Cache.DefinitionImpl', null);

    $purifier = new \HTMLPurifier($config);

    return trim($purifier->purify($html ?? ''));
}
    /** Vérifie les formats de liens acceptés par le CMS. */
    public static function validUrl(?string $v): bool
    {
        $v = trim((string) $v);
        if ($v === '') return true;
        if (preg_match('~^(/(?!/)|#)~', $v)) return ! preg_match('~\s~', $v);
        if (preg_match('~^(mailto:|tel:)~i', $v)) return true;
        return (bool) filter_var($v, FILTER_VALIDATE_URL) && preg_match('~^https?://~i', $v);
    }
    /** Récupère les définitions des champs CMS, filtrées par onglet. */
    public static function fields(?string $tab = null): array
    {
        $all = config('cms.fields');
        return $tab ? array_filter($all, fn ($f) => $f[0] === $tab) : $all;
    }
}

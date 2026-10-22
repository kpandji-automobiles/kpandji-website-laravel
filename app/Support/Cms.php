<?php
namespace App\Support;

class Cms
{
    public const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><a><ul><ol><li><h2><h3><h4><blockquote><img><hr><span>';

    /** Nettoie le HTML riche : balises de base uniquement, pas de script ni d'attributs dangereux. */
    public static function cleanHtml(?string $html): string
    {
        $html = (string) $html;
        $html = preg_replace('~<(script|style|iframe|object|embed|svg|math|form)\b[^>]*>.*?</\1>~is', '', $html);
        $html = strip_tags($html, self::ALLOWED_TAGS);
        // attributs on*, style, et liens javascript:/data:
        $html = preg_replace('~\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)~i', '', $html);
        $html = preg_replace('~\s+style\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)~i', '', $html);
        $html = preg_replace_callback('~\s(href|src)\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))~i', function ($m) {
            $v = html_entity_decode($m[3] ?? $m[4] ?? $m[5] ?? '');
            $v = trim(preg_replace('~[\x00-\x20]~', '', $v));
            return self::validUrl($v) ? ' '.strtolower($m[1]).'="'.e($v).'"' : '';
        }, $html);
        return trim($html);
    }

    /** Lien accepté : /chemin, #ancre, https://…, http://…, mailto:, tel: */
    public static function validUrl(?string $v): bool
    {
        $v = trim((string) $v);
        if ($v === '') return true;
        if (preg_match('~^(/(?!/)|#)~', $v)) return ! preg_match('~\s~', $v);
        if (preg_match('~^(mailto:|tel:)~i', $v)) return true;
        return (bool) filter_var($v, FILTER_VALIDATE_URL) && preg_match('~^https?://~i', $v);
    }

    public static function fields(?string $tab = null): array
    {
        $all = config('cms.fields');
        return $tab ? array_filter($all, fn ($f) => $f[0] === $tab) : $all;
    }
}

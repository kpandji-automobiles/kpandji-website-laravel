<?php
namespace Database\Seeders;

use App\Models\Modele;
use Illuminate\Database\Seeder;

/** Données de kpandji2026 final/data/modeles.ts (MODELES). Non destructif : n'écrase pas une ligne existante. */
class ModeleSeeder extends Seeder
{
    /** id React → slug de la page modèle Laravel */
    private const CAR = ['djetranplus' => 'djetran-plus', 'djetran' => 'djetran', 'lathaye' => 'lathaye'];

    public function run(): void
    {
        // Showroom virtuel (components/kp/VirtualShowroom.tsx › MODELS) — complète uniquement les champs vides
        foreach (json_decode(file_get_contents(__DIR__.'/data/showroom.json'), true) as $i => $m) {
            $row = Modele::firstOrNew(['key' => $m['id']]);
            if (! $row->exists) $row->fill(['name' => $m['name'], 'car_slug' => self::CAR[$m['id']] ?? null, 'sort' => 100 + $i, 'is_active' => false]);
            if ($row->gallery !== null || $row->tagline_fr !== null) { $row->save(); continue; }
            $row->fill([
                'in_showroom' => true,
                'badge_fr' => $m['badge']['fr'] ?? null, 'badge_en' => $m['badge']['en'] ?? null,
                'tagline_fr' => $m['tagline']['fr'] ?? null, 'tagline_en' => $m['tagline']['en'] ?? null,
                'sr_description_fr' => $m['description']['fr'] ?? null, 'sr_description_en' => $m['description']['en'] ?? null,
                'brochure' => ltrim($m['brochureHref'] ?? '', '/') ?: null,
                'highlights' => array_map(fn ($h) => ['fr' => $h['fr'] ?? '', 'en' => $h['en'] ?? ''], $m['highlights'] ?? []),
                'gallery' => array_map(fn ($g) => ['src' => ltrim($g['src'], '/'), 'alt' => $g['alt'] ?? '', 'tag' => $g['tag'] ?? ''], $m['gallery'] ?? []),
            ])->save();
        }

        $rows = json_decode(file_get_contents(__DIR__.'/data/modeles.json'), true);
        foreach ($rows as $i => $m) {
            $row = Modele::firstOrNew(['key' => $m['id']]);
            if ($row->exists) continue;
            $p = fn ($v) => ltrim((string) $v, '/');
            $row->fill([
                'name' => $m['name'],
                'car_slug' => self::CAR[$m['id']] ?? null,
                'description_fr' => $m['description']['fr'] ?? null,
                'description_en' => $m['description']['en'] ?? null,
                'characteristics' => array_map(fn ($c) => ['fr' => $c['fr'] ?? '', 'en' => $c['en'] ?? ''], $m['characteristics'] ?? []),
                'top_image' => $p($m['media']['topImage']),
                'top_video' => $p($m['media']['topVideo']),
                'bottom_left_image' => $p($m['media']['bottomLeftImage']),
                'bottom_right_image' => $p($m['media']['bottomRightImage']),
                'sort' => $i,
                'is_active' => true,
            ])->save();
        }
    }
}

<?php
namespace Database\Seeders;

use App\Models\Content;
use App\Models\Page;
use Illuminate\Database\Seeder;

/** Non destructif : ajoute uniquement les contenus/pages manquants avec leur valeur d'origine. */
class CmsSeeder extends Seeder
{
    public function run(): void
    {
        $i = 0;
        foreach (config('cms.fields') as $key => [$group, $type, $label, $default]) {
            $i++;
            $c = Content::firstOrNew(['key' => $key]);
            if (! $c->exists) $c->value = is_array($default) ? json_encode($default, JSON_UNESCAPED_UNICODE) : $default;
            $c->fill(['group' => $group, 'type' => $type, 'label' => $label, 'sort' => $i])->save();
        }

        $pages = [
            ['opportunities', 'Opportunités', 'Offres du moment', 'Retrouvez ici les offres et opportunités KPANDJI. La grande promotion est en cours sur toute la gamme.', 3],
            ['sav', 'S.A.V.', 'Service après-vente', 'Entretien, pièces et assistance pour votre véhicule KPANDJI. Cette page sera bientôt complétée ; en attendant, contactez-nous via le formulaire.', 4],
            ['contact', 'Contact', 'Nous joindre', 'Une question sur un modèle ou sur la promotion ? Utilisez notre formulaire, un conseiller KPANDJI vous rappelle rapidement.', 5],
            ['kpandji-automobiles', 'KPANDJI Automobiles', 'La marque', "La force d'une racine, l'élan d'une nation. Véhicules assemblés en Côte d'Ivoire : DJETRAN, DJETRAN PLUS, LATHAYE, BANCO.", 0],
            ['ecologie', 'Eco-Kpandji', 'Écologie', "L'assemblage local de nos modèles (DJETRAN, DJETRAN PLUS, LATHAYE, BANCO) réduit significativement les émissions liées au transport maritime de véhicules finis.", 0],
            ['emplois', 'Kpandji-Emplois', 'Carrières', "Les offres d'emploi KPANDJI seront publiées ici prochainement.", 0],
        ];
        foreach ($pages as [$slug, $title, $kicker, $intro, $order]) {
            Page::firstOrCreate(['slug' => $slug], [
                'title' => $title, 'kicker' => $kicker, 'intro' => $intro, 'published' => true,
                'in_nav' => false, 'nav_order' => $order, 'is_system' => true,
            ]);
        }
        Content::query()->first()?->touch(); // vide le cache
    }
}

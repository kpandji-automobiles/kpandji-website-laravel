<?php
namespace Database\Seeders;
use App\Models\Car;
use Illuminate\Database\Seeder;
/*
| Sources : KPANDJI-BANCO-BVA-Fiche-technique.pdf (BANCO) et projet "kpandji2026 final" (data/modeles.ts).
| ⚠️ PRIX : aucun prix n'a été trouvé dans les sources. 'price' et 'promo_price' sont à null
|    => le site affiche "Prix sur demande". Renseignez-les ici (FCFA) puis :
|    php artisan migrate:fresh --seed
*/
class CarSeeder extends Seeder
{
    public function run(): void
    {
        $cars = [
            [
                'slug' => 'djetran-plus', 'name' => 'DJETRAN PLUS', 'category' => 'Pick-up',
                'description' => "Essence : 2.0GDI + boîte automatique 8 vitesses (Puissance : 145 kW / 197 ch, Couple : 360 Nm). Systèmes d'assistance à la conduite intelligents de niveau 2 : régulateur de vitesse adaptatif, alerte de franchissement de ligne, avertisseur de collision frontale, vision panoramique à 360°, allumage automatique des phares, essuie-glaces à induction.",
                'highlights' => ['Moteur essence 2.0 GDI', 'Boîte automatique 8 vitesses', '145 kW / 197 ch – 360 Nm', 'Vision panoramique 360°'],
                'specs' => [
                    'Motorisation' => ['Moteur' => 'Essence 2.0 GDI', 'Transmission' => 'Automatique 8 rapports (8AT)', 'Puissance' => '145 kW / 197 ch', 'Couple' => '360 Nm'],
                    'Dimensions' => ['L × l × H' => '5393 (5692) × 2045 × 1965 (1920) mm (Standard/Allégé)', 'Empattement' => '3230 / 3513 mm (Allégé)', 'Voie' => '1730 mm'],
                    'Habitacle' => ['Largeur banquette avant' => '1 660 mm', 'Largeur banquette arrière' => '1 650 mm', 'Volume habitacle' => '4,3 m³'],
                    'Sécurité & aides' => ['Aide à la conduite' => 'Niveau 2 : ACC, LDWS, FCW, vision 360°', 'Airbags' => 'Frontaux et latéraux', 'Autres' => 'Frein de stationnement électronique, TPMS, PEPS, commande vocale'],
                ],
                'image' => 'images/cars/djetran-plus.png', 'hero_image' => 'images/cars/djetran-plus-hero.jpg',
            ],
            [
                'slug' => 'djetran', 'name' => 'DJETRAN', 'category' => 'Pick-up 4x4',
                'description' => "Turbo Diesel et essence 2,3 L, développant 163 chevaux avec un couple de 340 Nm, offrant puissance et souplesse pour la route comme le tout-terrain. Cockpit intelligent, caméra 360°, contrôle électronique de stabilité et ABS + EBD. Se distingue par sa puissance et sa transmission intégrale.",
                'highlights' => ['Turbo Diesel / Essence 2.3 L', '163 ch – 340 Nm', 'Transmission intégrale', 'Caméra 360° & contrôle de stabilité'],
                'specs' => [
                    'Motorisation' => ['Moteur' => 'Turbo Diesel et essence 2.3 L', 'Puissance' => '163 ch', 'Couple' => '340 Nm', 'Transmission' => 'Intégrale'],
                    'Sécurité' => ['Freinage' => 'ABS + EBD', 'Stabilité' => 'Contrôle électronique de stabilité', 'Caméra' => '360°'],
                ],
                'image' => 'images/cars/djetran.png', 'hero_image' => 'images/cars/djetran-hero.jpg',
            ],
            [
                'slug' => 'lathaye', 'name' => 'LATHAYE', 'category' => 'SUV',
                'description' => "Avec un moteur 2.0T GDI Turbo compressé de 165 kW. Transmission automatique CVT à 8 rapports pour une vitesse maximale de 210 km/h. Un véritable bolide qui redéfinit la performance à chaque voyage.",
                'highlights' => ['Moteur 2.0T GDI Turbo', '165 kW', 'Boîte automatique CVT 8 vitesses', 'Vitesse max. 210 km/h'],
                'specs' => [
                    'Motorisation' => ['Moteur' => '2.0T GDI Turbo', 'Puissance maximale' => '165 kW', 'Transmission' => 'Automatique CVT 8 rapports', 'Vitesse maximale' => '210 km/h'],
                ],
                'image' => 'images/cars/lathaye.png', 'hero_image' => 'images/cars/lathaye-hero.jpg',
            ],
            [
                'slug' => 'banco-bva', 'name' => 'BANCO BVA', 'category' => 'Citadine',
                'description' => "Citadine 4 cylindres assemblée en Côte d'Ivoire : moteur essence 1 298 cc à injection électronique, boîte automatique 4 rapports, 5 places, climatisation, écran tactile avec caméra de recul et feux LED.",
                'highlights' => ['Essence 1 298 cc – 65 kW', 'Boîte automatique 4AT', '6 à 7,5 L/100 km', '5 places – Euro 6'],
                'specs' => [
                    'Motorisation & performance' => ['Motorisation' => 'Essence, 4 cylindres en ligne, injection électronique', 'Cylindrée' => '1 298 cc', 'Puissance fiscale' => '6 CV', 'Puissance mécanique' => '65 kW', 'Couple maximal' => '120 Nm', 'Norme antipollution' => 'Euro 6', 'Vitesse maximale' => '120 km/h', 'Transmission' => 'Automatique (4AT)', 'Mode de transmission' => 'Traction avant (2WD / FWD)'],
                    'Dimensions & capacités' => ['Longueur' => '3,780 m', 'Largeur' => '1,650 m', 'Hauteur' => '1,530 m', 'Empattement' => '2,632 m', 'Garde au sol' => '170 mm (à vide)', 'Poids à vide' => '1,336 tonne', 'Coffre' => '147 L (extensible)', 'Réservoir' => '38 L', 'Consommation' => '6 à 7,5 L/100 km', 'Places' => '5'],
                    'Châssis & suspension' => ['Suspension avant' => 'Indépendante McPherson', 'Suspension arrière' => 'Bras tirés (semi-drag) + ressorts hélicoïdaux', 'Freins avant / arrière' => 'Disques / Tambours', 'Direction' => 'Crémaillère, assistance électrique (EPS)', 'Jantes' => 'Alliage d’aluminium', 'Pneumatiques' => '165/65 R15'],
                    'Sécurité' => ['Freinage' => 'ABS + EBD', 'Airbags' => '2 frontaux (conducteur et passager)'],
                    'Confort & équipements' => ['Démarrage' => 'Clé ou bouton (selon option)', 'Sellerie' => 'Tissu ou similicuir', 'Climatisation' => 'Oui', 'Lève-vitres / Rétroviseurs' => 'Électriques', 'Tableau de bord' => 'Écran digital LCD', 'Verrouillage' => 'Centralisé', 'Multimédia' => 'Écran tactile, caméra de recul, Bluetooth/USB/GPS', 'Feux' => 'LED'],
                ],
                'image' => 'images/cars/banco.png', 'hero_image' => 'images/cars/banco.png', 'brochure' => 'fiches/banco-bva.pdf',
            ],
            // ===== Modèles ajoutés depuis « TARIF CAMPAGNE V.xlsx » (données du fichier uniquement) =====
            [
                'slug' => 'kambie', 'name' => 'KAMBIE intelligente', 'category' => 'SUV hybride rechargeable (PHEV)',
                'description' => "SUV hybride rechargeable : moteur 1,5 L turbo essence associé à un moteur électrique, batterie LFP 21,2 kWh, boîte hybride DHT, traction 4x2.",
                'highlights' => ['Hybride rechargeable (PHEV)', '1,5 L turbo essence + électrique', 'Batterie LFP 21,2 kWh', 'Boîte hybride DHT – 4x2 traction'],
                'specs' => ['Motorisation' => ['Version' => 'SUV hybride rechargeable (PHEV)', 'Motorisation' => '1,5 L turbo essence + moteur électrique, batterie LFP 21,2 kWh', 'Boîte' => 'Hybride DHT', 'Transmission' => '4x2 traction'], 'Garantie' => ['Garantie constructeur' => 'Proposition : 3 ans / 100 000 km · batterie 8 ans / 150 000 km']],
                'image' => 'images/cars/placeholder.svg', 'hero_image' => 'images/cars/placeholder.svg',
                'promo_price' => 27000000, 'maintenance_cost' => 1375000, 'campaign_price' => 28375000, 'warranty' => 'Proposition : 3 ans / 100 000 km · batterie 8 ans / 150 000 km',
            ],
            [
                'slug' => 'klaman-pros', 'name' => 'KLAMAN PROS', 'category' => 'SUV 100 % électrique (EV)',
                'description' => "SUV 100 % électrique : moteur 120 kW, batterie LFP 48 ou 61 kWh, réducteur 1 rapport, traction 4x2.",
                'highlights' => ['100 % électrique', 'Moteur 120 kW', 'Batterie LFP 48 ou 61 kWh', 'Réducteur 1 rapport – 4x2 traction'],
                'specs' => ['Motorisation' => ['Version' => 'SUV 100 % électrique (EV)', 'Motorisation' => 'Électrique 120 kW, batterie LFP 48 ou 61 kWh', 'Boîte' => 'Réducteur 1 rapport', 'Transmission' => '4x2 traction'], 'Garantie' => ['Garantie constructeur' => 'Proposition : 3 ans / 100 000 km · batterie 8 ans / 150 000 km']],
                'image' => 'images/cars/placeholder.svg', 'hero_image' => 'images/cars/placeholder.svg',
                'promo_price' => 27000000, 'maintenance_cost' => 434500, 'campaign_price' => 27434500, 'warranty' => 'Proposition : 3 ans / 100 000 km · batterie 8 ans / 150 000 km',
            ],
            [
                'slug' => 'souralai', 'name' => 'SOURALAI', 'category' => 'SUV essence CVT',
                'description' => "SUV polyvalent pour les déplacements du quotidien : moteur 1,5 L essence, boîte CVT, transmission 4x2.",
                'highlights' => ['Moteur 1,5 L essence', 'Boîte automatique CVT', 'Transmission 4x2', 'Garantie 3 ans / 100 000 km'],
                'specs' => ['Motorisation' => ['Version' => 'SUV essence CVT', 'Motorisation' => '1,5 L essence', 'Boîte' => 'CVT', 'Transmission' => '4x2'], 'Garantie' => ['Garantie constructeur' => 'Proposition : 3 ans / 100 000 km']],
                'image' => 'images/cars/souralai.png', 'hero_image' => 'images/cars/souralai.png', 'brochure' => 'fiches/souralai-suv.pdf',
                'promo_price' => 14900000, 'maintenance_cost' => 1100000, 'campaign_price' => 16000000, 'warranty' => 'Proposition : 3 ans / 100 000 km',
            ],
            [
                'slug' => 'gemba', 'name' => 'GEMBA', 'category' => 'Mini van essence BVM',
                'description' => "Mini van : moteur 1,5 L essence, boîte manuelle, transmission 4x2 propulsion.",
                'highlights' => ['Moteur 1,5 L essence', 'Boîte manuelle', '4x2 propulsion', 'Garantie 3 ans / 100 000 km'],
                'specs' => ['Motorisation' => ['Version' => 'Mini van essence BVM', 'Motorisation' => '1,5 L essence', 'Boîte' => 'Manuelle', 'Transmission' => '4x2 propulsion'], 'Garantie' => ['Garantie constructeur' => 'Proposition : 3 ans / 100 000 km']],
                'image' => 'images/cars/placeholder.svg', 'hero_image' => 'images/cars/placeholder.svg',
                'promo_price' => 9700000, 'maintenance_cost' => 1200000, 'campaign_price' => 10900000, 'warranty' => 'Proposition : 3 ans / 100 000 km',
            ],
        ];
        // ===== Tarifs campagne pour les modèles existants (fichier TARIF CAMPAGNE V.xlsx) =====
        $tarif = [
            'djetran-plus' => ['promo_price' => 39900000, 'maintenance_cost' => 1700000, 'campaign_price' => 41600000, 'warranty' => 'Proposition : 3 ans / 100 000 km',
                'versions' => [['version' => 'Pick-up essence BVA premium', 'motorisation' => '2,0 L turbo essence', 'boite' => 'Automatique', 'transmission' => '4x4', 'promo_price' => 39900000, 'maintenance_cost' => 1700000, 'campaign_price' => 41600000]]],
            'djetran' => ['promo_price' => 17600000, 'maintenance_cost' => 1400000, 'campaign_price' => 19000000, 'warranty' => 'Proposition : 3 ans / 100 000 km',
                'versions' => [
                    ['version' => 'Pick-up diesel BVM', 'motorisation' => '2,5 L diesel', 'boite' => 'Manuelle', 'transmission' => '4x4', 'promo_price' => 19600000, 'maintenance_cost' => 1700000, 'campaign_price' => 21300000],
                    ['version' => 'Pick-up diesel BVA', 'motorisation' => '2,5 L diesel', 'boite' => 'Automatique', 'transmission' => '4x4', 'promo_price' => 25700000, 'maintenance_cost' => 1600000, 'campaign_price' => 27300000],
                    ['version' => 'Pick-up essence BVM', 'motorisation' => '2,4 L essence', 'boite' => 'Manuelle', 'transmission' => '4x4', 'promo_price' => 17600000, 'maintenance_cost' => 1400000, 'campaign_price' => 19000000],
                    ['version' => 'Pick-up essence BVA', 'motorisation' => '2,4 L essence', 'boite' => 'Automatique', 'transmission' => '4x4', 'promo_price' => 19600000, 'maintenance_cost' => 1500000, 'campaign_price' => 21100000],
                ]],
            'lathaye' => ['promo_price' => 22600000, 'maintenance_cost' => 1300000, 'campaign_price' => 23900000, 'warranty' => 'Proposition : 3 ans / 100 000 km',
                'versions' => [['version' => 'Essence BVA', 'motorisation' => '2,0 L essence', 'boite' => 'Automatique', 'transmission' => '4x2', 'promo_price' => 22600000, 'maintenance_cost' => 1300000, 'campaign_price' => 23900000]]],
            // BANCO BVA : absent du fichier -> reste « Prix promo sur demande »
        ];
        foreach ($cars as $i => $c) {
            Car::updateOrCreate(['slug' => $c['slug']], ($tarif[$c['slug']] ?? []) + $c + [
                'position' => $i,
                'price' => null,       // Prix catalogue (avant promo) : absent du fichier tarif
                'promo_price' => null,
            ]);
        }
    }
}

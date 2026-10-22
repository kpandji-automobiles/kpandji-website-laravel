<?php
/* Champs CMS de /kpandji-automobiles (components/kp/BrandPageContent.tsx) — fusionnés dans config/cms.php ['fields']. Onglet « brand ». */
$T = 'brand';
$P = function (string $k, string $type, string $label, string $fr, string $en) use ($T) {
    return ["$T.$k" => [$T, $type, "$label (FR)", $fr], "$T.{$k}_en" => [$T, $type, "$label (EN)", $en]];
};
$S = fn (string $k, string $type, string $label, string $v) => ["$T.$k" => [$T, $type, $label, $v]];
$FE = fn (array $rows) => array_map(fn ($r) => ['fr' => $r[0], 'en' => $r[1]], $rows);
return array_merge(
  $S('seo_title', 'text', 'SEO — titre de l\'onglet', 'Kpandji Automobiles — KPANDJI AUTOMOBILES'),
  $S('seo_description', 'textarea', 'SEO — description', 'Découvrez KPANDJI Automobiles : notre histoire, notre vision et l’engagement d’une équipe au service de la mobilité en Côte d’Ivoire et en Afrique.'),
  $S('og_title', 'text', 'Open Graph — titre (partage)', 'Kpandji Automobiles — KPANDJI AUTOMOBILES'),
  $S('og_description', 'textarea', 'Open Graph — description (partage)', 'L’entreprise derrière KPANDJI AUTOMOBILES : ingénierie, assemblage et service client.'),
  // Hero
  $S('hero_image', 'image', 'Hero — image de fond', 'models/para/pic.jpg'),
  $P('hero_aria', 'text', 'Hero — libellé accessibilité', 'Kpandji Automobiles — l’entreprise', 'Kpandji Automobiles — the company'),
  $P('hero_chapter', 'text', 'Hero — mention verticale', 'Chapitre / 01', 'Chapter / 01'),
  $P('hero_kicker', 'text', 'Hero — surtitre', 'L’entreprise — depuis 2024', 'The company — since 2024'),
  $P('hero_title1', 'text', 'Hero — titre ligne 1', 'Kpandji', 'Kpandji'),
  $P('hero_title2', 'text', 'Hero — titre ligne 2 (dégradé)', 'Automobiles', 'Automobiles'),
  $P('hero_text1', 'textarea', 'Hero — texte (avant la marque)', 'Constructeur et distributeur engagé derrière la marque', 'A dedicated manufacturer and distributor behind the'),
  $S('hero_brand', 'text', 'Hero — marque mise en avant', 'KPANDJI AUTOMOBILES'),
  $P('hero_text2', 'textarea', 'Hero — texte (après la marque)', '— concevoir des véhicules adaptés aux réalités locales, avec une exigence de qualité et un service de proximité.', 'brand — designing vehicles suited to local realities, with a demand for quality and a service of proximity.'),
  $P('btn_discover', 'text', 'Hero — bouton principal', 'Découvrir nos véhicules', 'Discover our vehicles'),
  $S('discover_url', 'url', 'Hero — lien bouton principal', '/#modeles'),
  $P('btn_contact', 'text', 'Hero — bouton secondaire', 'Nous contacter', 'Contact us'),
  $S('contact_url', 'url', 'Hero — lien bouton secondaire', '/contact'),
  $P('scroll', 'text', 'Hero — indication défilement', 'Faire défiler', 'Scroll'),
  ['brand.stats' => [$T, 'list', 'Chiffres (bandeau du hero)', [
      ['value' => '2024', 'fr' => 'Année de fondation', 'en' => 'Year founded'],
      ['value' => '100%', 'fr' => 'Made in Côte d’Ivoire', 'en' => 'Made in Côte d’Ivoire'],
      ['value' => '4+', 'fr' => 'Modèles à venir', 'en' => 'Upcoming models'],
      ['value' => '∞', 'fr' => 'Vision panafricaine', 'en' => 'Pan-African vision'],
  ], ['value' => 'Valeur', 'fr' => 'Libellé FR', 'en' => 'Libellé EN']]],
  // Histoire
  $P('story_label', 'text', 'Histoire — surtitre', 'Notre histoire', 'Our story'),
  $P('story_title1', 'text', 'Histoire — titre ligne 1', 'Une vision industrielle,', 'An industrial vision,'),
  $P('story_title2', 'text', 'Histoire — titre ligne 2', 'ancrée en Afrique.', 'rooted in Africa.'),
  $S('founder_image', 'image', 'Fondateur — portrait', 'company/company3.jpg'),
  $P('founder_alt', 'text', 'Fondateur — texte alternatif', 'Fondateur — Kpandji Automobiles', 'Founder — Kpandji Automobiles'),
  $S('founder_seal', 'text', 'Fondateur — monogramme', 'KP'),
  $P('founder_kicker', 'text', 'Fondateur — surtitre', 'Le fondateur', 'The founder'),
  $P('founder_title', 'text', 'Fondateur — titre', 'Une parole engagée', 'A committed voice'),
  $S('founder_place', 'text', 'Fondateur — lieu', 'Côte d’Ivoire — Abidjan'),
  $S('founder_index', 'text', 'Fondateur — index', '01 / 03'),
  $P('origin_label', 'text', 'Origine — surtitre', 'Origine', 'Origin'),
  $P('origin_title', 'text', 'Origine — titre', 'Bâtir un constructeur africain.', 'Building an African manufacturer.'),
  $P('origin_text', 'textarea', 'Origine — texte', 'Kpandji Automobiles est née d’une conviction simple : la mobilité doit être fiable, accessible et au service du développement économique. En nous appuyant sur l’ingénierie, l’assemblage et un réseau de partenaires exigeants, nous bâtissons une offre automobile pensée pour les routes, les usages et les attentes de nos clients.', 'Kpandji Automobiles was born from a simple conviction: mobility must be reliable, accessible, and in service of economic development. Drawing on engineering, assembly, and a network of demanding partners, we build an automotive offering designed for our customers\' roads, uses, and expectations.'),
  $P('mission_label', 'text', 'Mission — surtitre', 'Mission', 'Mission'),
  $P('mission_title', 'text', 'Mission — titre', 'Une exigence, du showroom à l’atelier.', 'A standard of excellence, from the showroom to the workshop.'),
  $P('mission_text', 'textarea', 'Mission — texte', 'Proposer des véhicules robustes et modernes, accompagnés d’un service après-vente et d’équipes commerciales à l’écoute. Chaque étape — du premier essai à l’entretien — reflète notre engagement pour la transparence et la durabilité de la relation client.', 'Offering robust, modern vehicles, backed by after-sales service and attentive sales teams. Every step — from the first test drive to maintenance — reflects our commitment to transparency and lasting customer relationships.'),
  $P('quote', 'textarea', 'Citation', 'Nous investissons dans l’industrie automobile ici, avec des équipes locales et une vision long terme : faire du groupe KPANDJI un repère de confiance pour les particuliers comme pour les professionnels.', 'We are investing in the automotive industry here, with local teams and a long-term vision: making the KPANDJI group a trusted reference for individuals and professionals alike.'),
  $P('quote_by', 'text', 'Citation — signature', 'Le fondateur — Kpandji Automobiles', 'The founder — Kpandji Automobiles'),
  // Bandeau
  ['brand.marquee' => [$T, 'list', 'Bandeau défilant (valeurs)', $FE([['Ingénierie locale', 'Local engineering'], ['Qualité d’exécution', 'Build quality'], ['Service de proximité', 'Local service'], ['Vision durable', 'Sustainable vision'], ['Identité africaine', 'African identity'], ['Made in Côte d’Ivoire', 'Made in Côte d’Ivoire']]), ['fr' => 'Texte FR', 'en' => 'Texte EN']]],
  // Engagements
  $P('pil_label', 'text', 'Engagements — surtitre', 'Nos engagements', 'Our commitments'),
  $P('pil_title', 'text', 'Engagements — titre', 'Six piliers, une exigence.', 'Six pillars, one standard.'),
  $P('pil_text', 'textarea', 'Engagements — texte', 'Chaque promesse devient une discipline interne — pour que la qualité ne soit jamais une option.', 'Every promise becomes an internal discipline — so quality is never optional.'),
  $P('pil_word', 'text', 'Engagements — mot « Pilier »', 'Pilier', 'Pillar'),
  $S('pillar_image', 'image', 'Engagements — image du 1er pilier', 'models/para/pic2.jpg'),
  ['brand.pillars' => [$T, 'list', 'Piliers (icône : pulse, star, clock, pin, truck, globe)', [
      ['icon' => 'pulse', 'title' => 'Ingénierie locale', 'title_en' => 'Local engineering', 'body' => 'Conception et assemblage menés par des équipes ivoiriennes, formées aux standards internationaux.', 'body_en' => 'Design and assembly carried out by Ivorian teams, trained to international standards.'],
      ['icon' => 'star', 'title' => 'Qualité d’exécution', 'title_en' => 'Build quality', 'body' => 'Contrôle qualité strict avant livraison : finitions, sécurité, fiabilité mécanique vérifiées.', 'body_en' => 'Strict quality control before delivery: finish, safety, and mechanical reliability all verified.'],
      ['icon' => 'clock', 'title' => 'Service de proximité', 'title_en' => 'Local service', 'body' => 'Un réseau SAV dédié, des pièces disponibles et des conseillers à l’écoute, partout dans le pays.', 'body_en' => 'A dedicated after-sales network, available parts, and attentive advisors, all across the country.'],
      ['icon' => 'pin', 'title' => 'Vision durable', 'title_en' => 'Sustainable vision', 'body' => 'Une feuille de route long terme : développer une industrie automobile africaine, exigeante et responsable.', 'body_en' => 'A long-term roadmap: building a demanding and responsible African automotive industry.'],
      ['icon' => 'truck', 'title' => 'Réseau & logistique', 'title_en' => 'Network & logistics', 'body' => 'Showrooms, centres techniques et logistique pensés pour servir les réalités du terrain.', 'body_en' => 'Showrooms, technical centers, and logistics designed to meet real, on-the-ground needs.'],
      ['icon' => 'globe', 'title' => 'Identité africaine', 'title_en' => 'African identity', 'body' => 'Un design et des choix techniques inspirés des routes, des usages et des aspirations du continent.', 'body_en' => 'A design and technical choices inspired by the continent\'s roads, uses, and aspirations.'],
  ], ['icon' => 'Icône', 'title' => 'Titre FR', 'title_en' => 'Titre EN', 'body' => 'Texte FR', 'body_en' => 'Texte EN']]],
  // Trajectoire
  $P('jr_label', 'text', 'Trajectoire — surtitre', 'Notre trajectoire', 'Our journey'),
  $P('jr_title', 'text', 'Trajectoire — titre', 'Hier, aujourd’hui, demain.', 'Yesterday, today, tomorrow.'),
  $P('jr_text', 'textarea', 'Trajectoire — texte', 'Trois étapes pour comprendre notre démarche : d’une conviction à l’action, jusqu’à une marque pensée pour le continent.', 'Three steps to understand our approach: from conviction to action, all the way to a brand designed for the continent.'),
  $P('jr_step', 'text', 'Trajectoire — mot « Étape »', 'Étape', 'Step'),
  ['brand.journey' => [$T, 'list', 'Trajectoire — étapes', [
      ['year' => 'Origine', 'year_en' => 'Origin', 'title' => 'Une conviction industrielle', 'title_en' => 'An industrial conviction', 'body' => 'Faire émerger un constructeur ivoirien capable de concilier exigence technique et ancrage local.', 'body_en' => 'Building an Ivorian manufacturer capable of combining technical excellence with local roots.'],
      ['year' => 'Aujourd’hui', 'year_en' => 'Today', 'title' => 'Conception & assemblage', 'title_en' => 'Design & assembly', 'body' => 'Une gamme pensée pour les routes africaines, du SUV familial à l’utilitaire de travail.', 'body_en' => 'A range designed for African roads, from the family SUV to the work utility vehicle.'],
      ['year' => 'Demain', 'year_en' => 'Tomorrow', 'title' => 'Une marque panafricaine', 'title_en' => 'A pan-African brand', 'body' => 'Étendre le réseau, structurer le SAV et porter une signature automobile africaine reconnue.', 'body_en' => 'Expanding the network, structuring after-sales service, and carrying a recognized African automotive signature.'],
  ], ['year' => 'Période FR', 'year_en' => 'Période EN', 'title' => 'Titre FR', 'title_en' => 'Titre EN', 'body' => 'Texte FR', 'body_en' => 'Texte EN']]],
  // Installations
  $P('gal_label', 'text', 'Installations — surtitre', 'Nos installations', 'Our facilities'),
  $P('gal_title', 'text', 'Installations — titre', 'Là où nos véhicules prennent vie.', 'Where our vehicles come to life.'),
  $P('gal_text', 'textarea', 'Installations — texte', 'Espaces d’accueil, atelier et suivi technique pour entretenir votre véhicule dans les règles de l’art.', 'Reception spaces, workshop, and technical follow-up to service your vehicle to the highest standard.'),
  ['brand.gallery' => [$T, 'list', 'Installations — photos (la 1re est la grande)', [
      ['image' => 'garage5.jpeg', 'alt' => 'Atelier principal — Kpandji Automobiles', 'alt_en' => 'Main workshop — Kpandji Automobiles', 'tag' => '01 — Atelier', 'tag_en' => '01 — Workshop', 'kicker' => 'Atelier principal', 'kicker_en' => 'Main workshop', 'title' => 'Assemblage & contrôle qualité', 'title_en' => 'Assembly & quality control', 'body' => 'Une chaîne de production rigoureuse, où chaque véhicule est inspecté avant livraison.', 'body_en' => 'A rigorous production line, where every vehicle is inspected before delivery.'],
      ['image' => 'garage2.jpg', 'alt' => 'Espace d’accueil — Kpandji Automobiles', 'alt_en' => 'Reception area — Kpandji Automobiles', 'tag' => '02 — Showroom', 'tag_en' => '02 — Showroom', 'kicker' => 'Showroom', 'kicker_en' => 'Showroom', 'title' => 'Accueil client', 'title_en' => 'Customer reception', 'body' => '', 'body_en' => ''],
      ['image' => 'garage3.jpg', 'alt' => 'Suivi technique — Kpandji Automobiles', 'alt_en' => 'Technical follow-up — Kpandji Automobiles', 'tag' => '03 — Service', 'tag_en' => '03 — Service', 'kicker' => 'Service technique', 'kicker_en' => 'Technical service', 'title' => 'Entretien & SAV', 'title_en' => 'Maintenance & after-sales', 'body' => '', 'body_en' => ''],
  ], ['image' => 'Image', 'alt' => 'Texte alternatif FR', 'alt_en' => 'Texte alternatif EN', 'tag' => 'Étiquette FR', 'tag_en' => 'Étiquette EN', 'kicker' => 'Surtitre FR', 'kicker_en' => 'Surtitre EN', 'title' => 'Titre FR', 'title_en' => 'Titre EN', 'body' => 'Texte FR (grande photo)', 'body_en' => 'Texte EN (grande photo)']]],
  // CTA
  $S('cta_image', 'image', 'Fin de page — image de fond', 'models/para/pic1.jpg'),
  $P('cta_kicker', 'text', 'Fin de page — surtitre', 'Kpandji AUTOMOBILES — Côte d’Ivoire', 'Kpandji AUTOMOBILES — Côte d’Ivoire'),
  $P('cta_title1', 'text', 'Fin de page — titre ligne 1', 'La force d’une racine,', 'The strength of a root,'),
  $P('cta_title2', 'text', 'Fin de page — titre ligne 2 (dégradé)', 'l’élan d’une nation.', 'the drive of a nation.'),
  $P('cta_text', 'textarea', 'Fin de page — texte', 'Découvrez nos véhicules, planifiez un essai ou échangez avec nos équipes — nous sommes à votre écoute.', 'Discover our vehicles, schedule a test drive, or speak with our teams — we\'re here to listen.'),
  $P('cta_btn_models', 'text', 'Fin de page — bouton principal', 'Voir les modèles', 'View the models'),
  $S('cta_models_url', 'url', 'Fin de page — lien bouton principal', '/#modeles'),
  $P('cta_btn_contact', 'text', 'Fin de page — bouton secondaire', 'Nous contacter', 'Contact us'),
  $S('cta_contact_url', 'url', 'Fin de page — lien bouton secondaire', '/contact'),
  ['brand.cta_badges' => [$T, 'list', 'Fin de page — mentions', $FE([['Abidjan', 'Abidjan'], ['Made in Côte d’Ivoire', 'Made in Côte d’Ivoire'], ['Vision panafricaine', 'Pan-African vision']]), ['fr' => 'Texte FR', 'en' => 'Texte EN']]],
);

<?php
/* Champs CMS de /ecologie (components/kp/EcologyPageContent.tsx) — fusionnés dans config/cms.php ['fields']. Onglet « eco ». */
$T = 'eco';
$P = function (string $k, string $type, string $label, string $fr, string $en) use ($T) {
    return ["$T.$k" => [$T, $type, "$label (FR)", $fr], "$T.{$k}_en" => [$T, $type, "$label (EN)", $en]];
};
$S = fn (string $k, string $type, string $label, string $v) => ["$T.$k" => [$T, $type, $label, $v]];
$FE = fn (array $rows) => array_map(fn ($r) => ['fr' => $r[0], 'en' => $r[1]], $rows);
return array_merge(
  $S('seo_title', 'text', 'SEO — titre de l\'onglet', 'ECO KPANDJI — Développement durable — KPANDJI AUTOMOBILES'),
  $S('seo_description', 'textarea', 'SEO — description', 'ECO KPANDJI : assemblage local, partenariats ONG pour le reboisement et éco-conception adaptée au Grand Abidjan.'),
  $S('og_title', 'text', 'Open Graph — titre (partage)', 'ECO KPANDJI — KPANDJI AUTOMOBILES'),
  $S('og_description', 'textarea', 'Open Graph — description (partage)', 'Excellence technologique, responsabilité environnementale et progrès social au bénéfice de la jeunesse ivoirienne.'),
  // Hero
  $S('hero_image', 'image', 'Hero — image de fond', 'hero/pic21.jpg'),
  $P('hero_alt', 'text', 'Hero — texte alternatif de l\'image', 'Engagement environnemental KPANDJI AUTOMOBILES', 'KPANDJI AUTOMOBILES environmental commitment'),
  $S('hero_aria', 'text', 'Hero — libellé accessibilité', 'ECO KPANDJI — KPANDJI AUTOMOBILES'),
  $S('hero_kicker', 'text', 'Hero — surtitre (et mention verticale)', 'ECO KPANDJI'),
  $P('hero_title1', 'text', 'Hero — titre ligne 1', 'Un vecteur de', 'A driver of'),
  $P('hero_title2', 'text', 'Hero — titre ligne 2 (dégradé)', 'développement durable', 'sustainable development'),
  $P('hero_text', 'textarea', 'Hero — texte (ambition)', 'Notre ambition est de démontrer qu’une marque automobile ivoirienne constitue un vecteur de développement durable, en conjuguant excellence technologique, responsabilité environnementale et progrès social au bénéfice de la jeunesse ivoirienne.', 'Our ambition is to demonstrate that an Ivorian car brand is a driver of sustainable development, combining technological excellence, environmental responsibility, and social progress for the benefit of Ivorian youth.'),
  $P('btn_commit', 'text', 'Hero — bouton principal', 'Nos engagements', 'Our commitments'),
  $S('btn_commit_url', 'url', 'Hero — lien bouton principal', '#engagements'),
  $P('btn_practices', 'text', 'Hero — bouton secondaire', 'Nos pratiques usine', 'Our factory practices'),
  $S('btn_practices_url', 'url', 'Hero — lien bouton secondaire', '#pratiques'),
  // Priorités
  $P('prio_aria', 'text', 'Priorités — libellé accessibilité', 'Engagements environnementaux', 'Environmental commitments'),
  $P('prio_label', 'text', 'Priorités — surtitre', 'Nos priorités', 'Our priorities'),
  $P('prio_title', 'text', 'Priorités — titre (ordinateur)', 'Une industrie responsable, à chaque étape.', 'A responsible industry, every step of the way.'),
  $S('prio_image', 'image', 'Priorités — image de fond (ordinateur)', 'nature.jpg'),
  ['eco.commitments' => [$T, 'list', 'Priorités — liste', $FE([['Certifications environnementales', 'Environmental certifications'], ['Actions sociales et environnementales', 'Social and environmental initiatives'], ['Réduction de la consommation d’énergie', 'Reduced energy consumption'], ['Gestion responsable des déchets', 'Responsible waste management']]), ['fr' => 'Texte FR', 'en' => 'Texte EN']]],
  // Pratiques (bandeau)
  $P('mq_aria', 'text', 'Pratiques — libellé accessibilité', 'Pratiques usine', 'Factory practices'),
  $P('mq_sr', 'text', 'Pratiques — titre pour lecteurs d\'écran', 'Pratiques écologiques en usine KPANDJI', 'Eco-friendly practices at the KPANDJI factory'),
  ['eco.marquee' => [$T, 'list', 'Pratiques usine — bandeau défilant', $FE([['Éclairages LED dans l’usine KPANDJI', 'LED lighting in the KPANDJI factory'], ['Machines industrielles basse consommation', 'Low-consumption industrial machinery'], ['Arrêt automatique des équipements inutilisés', 'Automatic shutdown of idle equipment'], ['Véhicules électriques pour la logistique interne', 'Electric vehicles for internal logistics'], ['Optimisation des transports et livraisons', 'Optimized transport and deliveries'], ['Recyclage des métaux, batteries et pneus', 'Recycling of metals, batteries, and tires'], ['Fournisseurs certifiés écologiques', 'Certified eco-friendly suppliers'], ['Véhicules hybrides ou électriques', 'Hybrid or electric vehicles'], ['Sensibilisation écologique du personnel', 'Environmental awareness for staff']]), ['fr' => 'Texte FR', 'en' => 'Texte EN']]],
  // Leviers
  $P('lev_aria', 'text', 'Leviers — libellé accessibilité', 'Aperçu des leviers', 'Overview of our levers'),
  $S('lev_image', 'image', 'Leviers — image de fond', 'nature1.jpg'),
  $P('lev_more', 'text', 'Leviers — « En savoir plus »', 'En savoir plus', 'Learn more'),
  ['eco.levers' => [$T, 'list', 'Leviers — cartes', [
      ['tag' => 'Souveraineté', 'tag_en' => 'Sovereignty', 'title' => 'Assemblage local & empreinte carbone', 'title_en' => 'Local assembly & carbon footprint', 'url' => '#engagements'],
      ['tag' => 'Éco-citoyenneté', 'tag_en' => 'Eco-citizenship', 'title' => 'Partenariat avec des ONG', 'title_en' => 'Partnership with NGOs', 'url' => '#engagements'],
      ['tag' => 'Éco-conception', 'tag_en' => 'Eco-design', 'title' => 'Matériaux durables & efficacité énergétique', 'title_en' => 'Sustainable materials & energy efficiency', 'url' => '#engagements'],
  ], ['tag' => 'Étiquette FR', 'tag_en' => 'Étiquette EN', 'title' => 'Titre FR', 'title_en' => 'Titre EN', 'url' => 'Lien']]],
  // Galerie
  $P('gal_label', 'text', 'Galerie — surtitre', 'Industrie locale', 'Local industry'),
  $P('gal_title', 'text', 'Galerie — titre', 'L\'assemblage en images.', 'Assembly, in pictures.'),
  $P('gal_text', 'textarea', 'Galerie — texte', 'Nos sites de production et d\'entretien incarnent la souveraineté industrielle et l\'exigence environnementale de KPANDJI.', 'Our production and maintenance sites embody KPANDJI\'s industrial sovereignty and environmental standards.'),
  ['eco.gallery' => [$T, 'list', 'Galerie — photos (la 1re est la grande)', [
      ['image' => 'garage5.jpeg', 'alt' => 'Infrastructure KPANDJI — montage et contrôle qualité', 'alt_en' => 'KPANDJI infrastructure — assembly and quality control', 'tag' => '01 — Assemblage', 'tag_en' => '01 — Assembly', 'kicker' => 'Souveraineté', 'kicker_en' => 'Sovereignty', 'title' => 'Produire localement', 'title_en' => 'Producing locally'],
      ['image' => 'garage2.jpg', 'alt' => 'Atelier KPANDJI — espace structuré', 'alt_en' => 'KPANDJI workshop — organized space', 'tag' => '02 — Atelier', 'tag_en' => '02 — Workshop', 'kicker' => '', 'kicker_en' => '', 'title' => 'Chaîne maîtrisée', 'title_en' => 'A controlled production line'],
      ['image' => 'garage3.jpg', 'alt' => 'Maintenance et traitement maîtrisés', 'alt_en' => 'Controlled maintenance and processing', 'tag' => '03 — SAV', 'tag_en' => '03 — After-sales', 'kicker' => '', 'kicker_en' => '', 'title' => 'Qualité & SAV', 'title_en' => 'Quality & after-sales'],
  ], ['image' => 'Image', 'alt' => 'Texte alternatif FR', 'alt_en' => 'Texte alternatif EN', 'tag' => 'Étiquette FR', 'tag_en' => 'Étiquette EN', 'kicker' => 'Surtitre FR (grande photo)', 'kicker_en' => 'Surtitre EN (grande photo)', 'title' => 'Titre FR', 'title_en' => 'Titre EN']]],
  // CTA
  $S('cta_image', 'image', 'Fin de page — image de fond', 'garage2.jpg'),
  $P('cta_kicker', 'text', 'Fin de page — surtitre', 'Partenariats & questions', 'Partnerships & questions'),
  $P('cta_title1', 'text', 'Fin de page — titre ligne 1', 'Aller plus loin', 'Going further'),
  $P('cta_title2', 'text', 'Fin de page — titre ligne 2 (dégradé)', 'ensemble', 'together'),
  $P('cta_text', 'textarea', 'Fin de page — texte', 'Vous partagez notre vision d\'une industrie automobile ivoirienne responsable ? Contactez-nous pour échanger sur nos engagements ECO KPANDJI.', 'Do you share our vision of a responsible Ivorian automotive industry? Contact us to discuss our ECO KPANDJI commitments.'),
  $P('cta_btn1', 'text', 'Fin de page — bouton 1', 'Nous contacter', 'Contact us'),
  $S('cta_btn1_url', 'url', 'Fin de page — lien bouton 1', '/contact'),
  $P('cta_btn2', 'text', 'Fin de page — bouton 2', 'Service après-vente', 'After-sales service'),
  $S('cta_btn2_url', 'url', 'Fin de page — lien bouton 2', '/sav'),
  $P('cta_btn3', 'text', 'Fin de page — bouton 3', 'L\'entreprise', 'The company'),
  $S('cta_btn3_url', 'url', 'Fin de page — lien bouton 3', '/kpandji-automobiles'),
  ['eco.cta_badges' => [$T, 'list', 'Fin de page — mentions', $FE([['Assemblage local', 'Local assembly'], ['Partenariats ONG', 'NGO partnerships'], ['Éco-conception', 'Eco-design']]), ['fr' => 'Texte FR', 'en' => 'Texte EN']]],
);

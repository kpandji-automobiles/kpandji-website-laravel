<?php
/*
| Configuration de la promotion KPANDJI.
| ⚠️ Aucun prix promo n'a été fourni dans les documents sources.
| - Renseignez les prix catalogue dans database/seeders/CarSeeder.php (champ 'price', en FCFA)
|   ou un prix promo exact par modèle ('promo_price').
| - OU définissez une réduction globale en % ici / dans .env (PROMO_REDUCTION_PERCENT).
*/
return [
    'title' => env('PROMO_TITLE', 'Grande promotion KPANDJI'),
    'subtitle' => env('PROMO_SUBTITLE', 'Nous avons réduit nos prix sur toute la gamme. Profitez-en dès maintenant !'),
    'reduction_percent' => (float) env('PROMO_REDUCTION_PERCENT', 0), // ex: 10 pour -10 %
    'ends_at' => env('PROMO_ENDS_AT'), // ex: 2026-12-31
    'currency' => 'FCFA',
    'active' => (bool) env('PROMO_ACTIVE', true),
];

# Refonte KPANDJI — plan de sprints

Branche : `refonte` (PR vers `main` en fin de refonte). Une fonctionnalité terminée = tests verts + un commit.

- **Back-office** : Filament 5, panneau `/backoffice` (remplace l'ancien `/admin` maison à la fin).
- **Front** : design system KPANDJI (Claude Design) — constructeur premium × achat transparent, noir / or / sable, Montserrat.
  Référence : https://claude.ai/artifact/F9ofFAi7mbT9mrGKu6JuHJ · maquettes : https://claude.ai/artifact/WWLftgj12zqf46o59DHkhN
- **Outils et consignes** : consignes Laravel Boost du dépôt (`CLAUDE.md`, règles Filament incluses), skills `ecc:laravel-tdd`, `ecc:laravel-patterns`, `ecc:laravel-security`, `ecc:laravel-verification`, `ui-ux-pro-max`.

## Définition de « terminé » (chaque fonctionnalité)

1. Tests feature (ou Livewire pour Filament) couvrant accès + comportement ; `php artisan test` vert.
2. `vendor/bin/pint --dirty` (jamais sur `routes/web.php` avant son nettoyage).
3. `php artisan route:cache` OK (la finalisation de déploiement fait `optimize`).
4. Un commit `feat(...)` / `refactor(...)` par fonctionnalité.

## Sprint 0 — Fondations

| # | Fonctionnalité | Statut |
|---|---|---|
| 0.1 | Filament 5 installé, panneau `/backoffice`, accès réservé aux admins, thème KPANDJI | ✅ `49f41c3` |
| 0.2 | Tokens et composants du design system dans `public/css/kpandji/` + layout Blade de base | ✅ `acc2213` |

## Sprint 1 — Back-office : catalogue et contenus

| # | Fonctionnalité | Remplace |
|---|---|---|
| 1.1 | ✅ `e971478` Ressource **Voitures** (CRUD, image, brochure, visibilité, ordre glisser-déposer) | `Admin\CarController` |
| 1.2 | ✅ `cabe608` Ressource **Modèles** accueil + showroom (FR/EN, médias, galerie) | `Admin\ModeleController`, `ShowroomController` |
| 1.3 | ✅ `0bb494a` Ressource **Pages libres** (HTML filtré, SEO, menu) | `Admin\PageController` |
| 1.4 | ✅ `968f111` Page **Promotion** (titre, réduction, date de fin) | `DashboardController@promo` |
| 1.5 | ✅ `a0d5423` Page **Contenus** : formulaires générés depuis `config/cms*.php`, un onglet par rubrique | `ContentController`, Brand/Eco/Contact/Sav/Sira/Privilege/Emplois (contenus) |

## Sprint 2 — Back-office : demandes

| # | Fonctionnalité |
|---|---|
| 2.1 | ✅ `d9c1adc` Demandes d'essai et de contact (filtres, marquer traité, supprimer) |
| 2.2 | ✅ `b6843b0` Demandes SAV, inscriptions SIRA, demandes Privilège |
| 2.3 | ✅ `3b94fb8` Candidatures (fiche, téléchargement sécurisé du CV) |
| 2.4 | ✅ `bd42c18` Tableau de bord : chiffres clés et dernières demandes |

## Sprint 3 — Espace manager (panneau Filament `/manager`, `/espace-manager` redirigé au sprint 6)

| # | Fonctionnalité |
|---|---|
| 3.1 | ✅ Accès managers + admins, validation / refus des comptes |
| 3.2 | ✅ Clients : fiche, investissements et écritures de gains (relation managers) |
| 3.3 | Invitations : envoyer, renvoyer (limité), révoquer |
| 3.4 | Boîte de réception unifiée et conversations (réponse par e-mail) |

## Sprint 4 — Front public : vitrine

| # | Fonctionnalité |
|---|---|
| 4.1 | Header, footer, accueil (visuel plein cadre, recherche, types, tuiles, offres, budget) |
| 4.2 | Catalogue avec filtres et fiche modèle |
| 4.3 | Showroom, opportunités, marque / écologie / pages libres |

## Sprint 5 — Front : formulaires et espace client

| # | Fonctionnalité |
|---|---|
| 5.1 | Essai, contact, SAV, SIRA, Privilège, emplois |
| 5.2 | Connexion, compte en attente, mot de passe |
| 5.3 | Espace client : tableau de bord, gains, export CSV, messagerie |

## Sprint 6 — Bascule et nettoyage

| # | Fonctionnalité |
|---|---|
| 6.1 | Suppression de l'ancien admin et de l'ancien espace manager, redirection `/admin` → `/backoffice` |
| 6.2 | Tests de non-régression, Pint sur les fichiers touchés, PR `refonte` → `main` |

<?php
/* Fenêtre de connexion du header (icône utilisateur) + menu connecté — onglet « header ». Fusionné dans config/cms.php ['fields']. */
$T = 'header';
$P = function (string $k, string $type, string $label, string $fr, string $en) use ($T) {
    return ["$T.$k" => [$T, $type, "$label (FR)", $fr], "$T.{$k}_en" => [$T, $type, "$label (EN)", $en]];
};
return array_merge(
  $P('login_kicker', 'text', 'Connexion — surtitre', 'Espace KPANDJI', 'KPANDJI workspace'),
  $P('login_title', 'text', 'Connexion — titre', 'Connexion', 'Sign in'),
  $P('login_subtitle', 'textarea', 'Connexion — sous-titre', 'Identifiez-vous pour accéder à votre espace de travail.', 'Sign in to access your workspace.'),
  $P('login_email', 'text', 'Connexion — libellé e-mail', 'Adresse e-mail', 'Email address'),
  $P('login_password', 'text', 'Connexion — libellé mot de passe', 'Mot de passe', 'Password'),
  $P('login_remember', 'text', 'Connexion — se souvenir de moi', 'Se souvenir de moi', 'Remember me'),
  $P('login_submit', 'text', 'Connexion — bouton', 'Se connecter', 'Sign in'),
  $P('login_sending', 'text', 'Connexion — bouton (en cours)', 'Connexion…', 'Signing in…'),
  $P('login_e_required', 'text', 'Connexion — erreur champs manquants', 'Indiquez votre e-mail et votre mot de passe.', 'Please enter your email and password.'),
  $P('login_e_invalid', 'text', 'Connexion — erreur identifiants', 'Identifiants incorrects.', 'Incorrect credentials.'),
  $P('login_e_throttle', 'text', 'Connexion — erreur trop de tentatives (:s = secondes)', 'Trop de tentatives. Réessayez dans :s secondes.', 'Too many attempts. Try again in :s seconds.'),
  $P('login_e_network', 'text', 'Connexion — erreur réseau', 'Connexion impossible. Vérifiez votre réseau.', 'Unable to connect. Please check your network.'),
  $P('login_close', 'text', 'Connexion — bouton fermer (accessibilité)', 'Fermer', 'Close'),
  $P('account_menu_admin', 'text', 'Menu connecté — lien administrateur', 'Tableau de bord', 'Dashboard'),
  $P('account_menu_user', 'text', 'Menu connecté — lien utilisateur', 'Mon espace', 'My workspace'),
  $P('account_menu_manager', 'text', 'Menu connecté — lien manager', 'Espace manager', 'Manager space'),
  $P('account_logout', 'text', 'Menu connecté — déconnexion', 'Se déconnecter', 'Sign out'),
);

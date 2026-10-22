<?php
/* Libellés des espaces Manager (/espace-manager) et Client (/mon-espace). Onglet « espaces ». Lus via sp_t('clé'). */
$T = 'espaces';
$rows = [
  // Commun
  'brand' => ['Espace', 'Space'], 'manager_title' => ['Espace manager', 'Manager space'], 'client_title' => ['Mon espace', 'My space'],
  'hello' => ['Bonjour', 'Hello'], 'logout' => ['Déconnexion', 'Log out'], 'save' => ['Enregistrer', 'Save'], 'cancel' => ['Annuler', 'Cancel'],
  'edit' => ['Modifier', 'Edit'], 'delete' => ['Supprimer', 'Delete'], 'confirm_delete' => ['Supprimer définitivement ?', 'Delete permanently?'],
  'filter' => ['Filtrer', 'Filter'], 'reset' => ['Réinitialiser', 'Reset'], 'all' => ['Tous', 'All'], 'none' => ['Aucun élément pour le moment.', 'Nothing yet.'],
  'name' => ['Nom complet', 'Full name'], 'email' => ['E-mail', 'Email'], 'phone' => ['Téléphone (facultatif)', 'Phone (optional)'],
  'date' => ['Date', 'Date'], 'amount' => ['Montant (FCFA)', 'Amount (FCFA)'], 'type' => ['Type', 'Type'], 'description' => ['Description', 'Description'],
  'status' => ['Statut', 'Status'], 'notes' => ['Notes', 'Notes'], 'from' => ['Du', 'From'], 'to' => ['Au', 'To'], 'saved' => ['Modifications enregistrées.', 'Changes saved.'],
  // Navigation
  'nav_dashboard' => ['Tableau de bord', 'Dashboard'], 'nav_inbox' => ['Messages', 'Inbox'], 'nav_invitations' => ['Invitations', 'Invitations'],
  'nav_clients' => ['Clients & investissements', 'Clients & investments'], 'nav_password' => ['Mot de passe', 'Password'], 'nav_admin' => ['Administration', 'Administration'],
  // Mot de passe
  'pw_title' => ['Changer mon mot de passe', 'Change my password'], 'pw_current' => ['Mot de passe actuel', 'Current password'],
  'pw_new' => ['Nouveau mot de passe (8 caractères min.)', 'New password (min. 8 characters)'], 'pw_confirm' => ['Confirmer le nouveau mot de passe', 'Confirm new password'],
  'pw_done' => ['Mot de passe modifié.', 'Password updated.'], 'pw_wrong' => ['Mot de passe actuel incorrect.', 'Current password is incorrect.'],
  // Invitations
  'inv_title' => ['Inviter un client', 'Invite a client'], 'inv_intro' => ['Le client reçoit un lien personnel, valable 7 jours, pour créer son mot de passe.', 'The client receives a personal link, valid for 7 days, to set a password.'],
  'inv_send' => ['Envoyer l\'invitation', 'Send invitation'], 'inv_list' => ['Invitations envoyées', 'Sent invitations'],
  'inv_pending' => ['En attente', 'Pending'], 'inv_accepted' => ['Acceptée', 'Accepted'], 'inv_expired' => ['Expirée', 'Expired'], 'inv_revoked' => ['Révoquée', 'Revoked'],
  'inv_resend' => ['Renvoyer (nouveau lien)', 'Resend (new link)'], 'inv_revoke' => ['Révoquer', 'Revoke'], 'inv_copy' => ['Copier le lien', 'Copy link'], 'inv_copied' => ['Lien copié', 'Link copied'],
  'inv_whatsapp' => ['Partager par WhatsApp', 'Share via WhatsApp'], 'inv_expires' => ['Expire le', 'Expires'],
  'inv_link_ready' => ['Invitation créée. Lien à transmettre au client :', 'Invitation created. Link to share with the client:'],
  'inv_mail_logged' => ['Le serveur d\'e-mail n\'est pas configuré (mode journal) : aucun e-mail réel n\'est parti. Transmettez ce lien vous-même.', 'No mail server configured (log mode): no real email was sent. Share this link yourself.'],
  'inv_mail_sent' => ['Un e-mail a été envoyé au client.', 'An email was sent to the client.'],
  'inv_mail_failed' => ['L\'e-mail n\'a pas pu partir. Transmettez ce lien vous-même.', 'The email could not be sent. Share this link yourself.'],
  'inv_exists' => ['Un compte existe déjà avec cet e-mail.', 'An account already exists with this email.'],
  'inv_mail_subject' => ['Votre espace client KPANDJI', 'Your KPANDJI client space'],
  'inv_mail_body' => ["Bonjour :name,\n\nKPANDJI vous invite à créer votre espace client pour suivre vos investissements.\nCréez votre mot de passe avec ce lien (valable 7 jours) :\n:link\n\nKPANDJI AUTOMOBILES", "Hello :name,\n\nKPANDJI invites you to create your client space to follow your investments.\nSet your password with this link (valid for 7 days):\n:link\n\nKPANDJI AUTOMOBILES"],
  // Acceptation
  'acc_title' => ['Créer mon espace client', 'Create my client space'], 'acc_intro' => ['Choisissez votre mot de passe pour activer votre compte.', 'Choose your password to activate your account.'],
  'acc_submit' => ['Activer mon compte', 'Activate my account'], 'acc_invalid' => ['Ce lien d\'invitation n\'est plus valide (expiré, déjà utilisé ou révoqué). Demandez un nouveau lien à votre conseiller KPANDJI.', 'This invitation link is no longer valid (expired, used or revoked). Ask your KPANDJI advisor for a new link.'],
  // Messages
  'inbox_title' => ['Messages reçus', 'Inbox'], 'inbox_search' => ['Rechercher (nom, e-mail, objet)', 'Search (name, email, subject)'],
  'src_contact' => ['Contact', 'Contact'], 'src_essai' => ['Essai / réservation', 'Test drive / booking'], 'src_sav' => ['S.A.V.', 'After-sales'], 'src_sira' => ['Kpandji Sira', 'Kpandji Sira'],
  'src_privilege' => ['Kpandji Privilège', 'Kpandji Privilège'], 'src_emplois' => ['Candidature', 'Application'], 'src_client' => ['Demande client', 'Client request'],
  'st_nouveau' => ['Nouveau', 'New'], 'st_en_cours' => ['En cours', 'In progress'], 'st_traite' => ['Traité', 'Done'], 'replied' => ['Répondu', 'Replied'],
  'msg_from' => ['De', 'From'], 'msg_subject' => ['Objet', 'Subject'], 'msg_details' => ['Détails', 'Details'], 'thread' => ['Historique des réponses', 'Reply history'],
  'reply' => ['Répondre', 'Reply'], 'reply_send' => ['Envoyer la réponse', 'Send reply'], 'reply_no_email' => ['Pas d\'e-mail pour ce contact : la réponse est enregistrée, à transmettre par téléphone.', 'No email for this contact: the reply is stored, follow up by phone.'],
  'reply_subject' => ['Réponse de KPANDJI', 'Reply from KPANDJI'], 'reply_done' => ['Réponse enregistrée.', 'Reply saved.'],
  'mail_logged' => ['journalisé (pas d\'envoi réel)', 'logged (not actually sent)'], 'mail_sent' => ['envoyé', 'sent'], 'mail_failed' => ['échec d\'envoi', 'send failed'], 'mail_none' => ['non envoyé', 'not sent'],
  'back_inbox' => ['Retour aux messages', 'Back to inbox'],
  // Clients / investissements
  'clients_title' => ['Clients', 'Clients'], 'client_since' => ['Client depuis', 'Client since'], 'open' => ['Ouvrir', 'Open'],
  'inv_add' => ['Ajouter un investissement', 'Add an investment'], 'inv_label' => ['Libellé (ex. véhicule, programme)', 'Label (e.g. vehicle, program)'], 'program' => ['Programme', 'Program'],
  'prog_vehicule' => ['Véhicule', 'Vehicle'], 'prog_sira' => ['Kpandji Sira', 'Kpandji Sira'], 'prog_privilege' => ['Kpandji Privilège', 'Kpandji Privilège'], 'prog_autre' => ['Autre', 'Other'],
  'invested' => ['Montant investi', 'Amount invested'], 'start_date' => ['Date de début', 'Start date'],
  'ist_actif' => ['Actif', 'Active'], 'ist_en_attente' => ['En attente', 'Pending'], 'ist_cloture' => ['Clôturé', 'Closed'],
  'entry_add' => ['Ajouter une écriture', 'Add an entry'], 'type_revenu' => ['Revenu', 'Revenue'], 'type_versement' => ['Versement', 'Payout'], 'type_frais' => ['Frais', 'Fee'],
  'back_clients' => ['Retour aux clients', 'Back to clients'],
  // Tableau de bord client
  'kpi_invested' => ['Total investi', 'Total invested'], 'kpi_revenue' => ['Revenus perçus', 'Revenue received'], 'kpi_fees' => ['Frais', 'Fees'],
  'kpi_net' => ['Net (revenus − frais)', 'Net (revenue − fees)'], 'kpi_paid' => ['Versements reçus', 'Payouts received'],
  'my_investments' => ['Mes investissements', 'My investments'], 'history' => ['Historique', 'History'], 'chart' => ['Revenus mensuels', 'Monthly revenue'],
  'export_csv' => ['Exporter en CSV', 'Export CSV'], 'investment' => ['Investissement', 'Investment'],
  'req_title' => ['Une demande ?', 'A request?'], 'req_intro' => ['Demande de versement, question… votre message arrive directement chez votre conseiller.', 'Payout request, question… your message goes straight to your advisor.'],
  'req_subject' => ['Objet', 'Subject'], 'req_message' => ['Message', 'Message'], 'req_send' => ['Envoyer', 'Send'], 'req_done' => ['Demande envoyée à votre conseiller.', 'Request sent to your advisor.'],
  'req_default' => ['Demande de versement', 'Payout request'], 'my_requests' => ['Mes demandes', 'My requests'],
  'no_investment' => ['Aucun investissement n\'est encore enregistré sur votre espace.', 'No investment has been recorded in your space yet.'],
  // Tableau de bord manager
  'kpi_new_msgs' => ['Messages non traités', 'Open messages'], 'kpi_pending_inv' => ['Invitations en attente', 'Pending invitations'], 'kpi_clients' => ['Clients', 'Clients'],
  'latest_msgs' => ['Derniers messages', 'Latest messages'],
  // Barre de navigation manager (v2) + validation des comptes
  'nav_messages' => ['Messages', 'Messages'], 'nav_reservations' => ['Réservations', 'Bookings'], 'nav_sav' => ['S.A.V.', 'After-sales'], 'nav_other' => ['Autres demandes', 'Other requests'],
  'nav_menu' => ['Menu de l\'espace', 'Space menu'],
  'tab_messages_title' => ['Messages du formulaire de contact', 'Contact form messages'], 'tab_messages_intro' => ['Messages envoyés depuis la page Contact.', 'Messages sent from the Contact page.'],
  'tab_reservations_title' => ['Réservations d\'essai', 'Test drive bookings'], 'tab_reservations_intro' => ['Demandes envoyées depuis le formulaire « Demande d\'essai ».', 'Requests sent from the test drive form.'],
  'tab_sav_title' => ['Messages S.A.V.', 'After-sales messages'], 'tab_sav_intro' => ['Messages envoyés depuis « Écrire au S.A.V. ».', 'Messages sent from “Write to after-sales”.'],
  'tab_autres_title' => ['Autres demandes', 'Other requests'], 'tab_autres_intro' => ['Demandes clients, inscriptions Sira et Privilège, candidatures.', 'Client requests, Sira and Privilège sign-ups, job applications.'],
  'st_confirme' => ['Confirmé', 'Confirmed'], 'st_annule' => ['Annulé', 'Cancelled'],
  'model' => ['Modèle', 'Model'], 'wish_date' => ['Date souhaitée', 'Requested date'], 'received' => ['Reçu le', 'Received'], 'location' => ['Localisation', 'Location'],
  'kind_essai' => ['Essai', 'Test drive'], 'kind_information' => ['Information', 'Information'],
  'role' => ['Rôle du compte', 'Account role'], 'role_client' => ['Client', 'Client'], 'role_manager' => ['Manager', 'Manager'],
  'val_title' => ['Comptes à valider', 'Accounts to validate'], 'val_intro' => ['Ces personnes ont accepté leur invitation et créé leur mot de passe. Elles n\'accèdent à leur espace qu\'après validation.', 'These people accepted their invitation and set a password. They can only access their space once validated.'],
  'val_ok' => ['Valider', 'Approve'], 'val_no' => ['Refuser', 'Decline'], 'val_done' => ['Compte validé.', 'Account approved.'], 'val_refused_done' => ['Compte refusé.', 'Account declined.'],
  'val_refused_list' => ['Comptes refusés', 'Declined accounts'], 'val_reapprove' => ['Valider quand même', 'Approve anyway'], 'val_none' => ['Aucun compte en attente.', 'No account waiting.'],
  'val_mail_subject' => ['Votre espace KPANDJI est activé', 'Your KPANDJI space is active'],
  'val_mail_body' => ["Bonjour :name,\n\nVotre compte KPANDJI a été validé. Connectez-vous depuis l'icône compte du site :\n:link\n\nKPANDJI AUTOMOBILES", "Hello :name,\n\nYour KPANDJI account has been approved. Sign in from the account icon on the website:\n:link\n\nKPANDJI AUTOMOBILES"],
  'wait_title' => ['Compte en attente de validation', 'Account awaiting approval'],
  'wait_text' => ['Merci, votre mot de passe est enregistré. Votre conseiller KPANDJI doit maintenant valider votre compte ; vous pourrez ensuite accéder à votre espace avec votre e-mail et ce mot de passe.', 'Thank you, your password is saved. Your KPANDJI advisor now needs to approve your account; you will then be able to access your space with your email and this password.'],
  'wait_refused_title' => ['Accès non activé', 'Access not activated'],
  'wait_refused_text' => ['Votre compte n\'a pas été activé. Pour toute question, contactez votre conseiller KPANDJI.', 'Your account has not been activated. For any question, please contact your KPANDJI advisor.'],
  'wait_refresh' => ['Vérifier à nouveau', 'Check again'], 'wait_home' => ['Retour au site', 'Back to the website'],
  // Espace client v2
  'cnav_overview' => ['Tableau de bord', 'Overview'], 'cnav_messages' => ['Messages reçus', 'Messages'], 'cnav_opps' => ['Opportunités', 'Opportunities'], 'cnav_gains' => ['Gains', 'Earnings'],
  'msg_title' => ['Messages reçus', 'Messages'], 'msg_intro' => ['Réponses de votre conseiller KPANDJI et messages de votre espace.', 'Replies from your KPANDJI advisor and messages about your space.'],
  'msg_compose' => ['Envoyer un message', 'Send a message'], 'msg_unread' => ['non lu(s)', 'unread'], 'msg_mark_all' => ['Tout marquer comme lu', 'Mark all as read'],
  'msg_none' => ['Aucun message pour le moment.', 'No messages yet.'], 'msg_you' => ['Vous', 'You'], 'msg_advisor' => ['Conseiller KPANDJI', 'KPANDJI advisor'],
  'msg_system' => ['Message KPANDJI', 'KPANDJI message'], 'msg_back' => ['Retour aux messages', 'Back to messages'], 'msg_followup' => ['Répondre à votre conseiller', 'Reply to your advisor'],
  'msg_your_request' => ['Votre demande', 'Your request'], 'msg_new' => ['Nouveau', 'New'], 'msg_latest' => ['Derniers messages', 'Latest messages'], 'see_all' => ['Tout voir', 'See all'],
  'opp_title' => ['Mes opportunités', 'My opportunities'], 'opp_intro' => ['Vos demandes Kpandji Sira et Kpandji Privilège et leur suivi.', 'Your Kpandji Sira and Kpandji Privilège requests and their follow-up.'],
  'opp_new' => ['Soumettre une nouvelle demande', 'Submit a new request'], 'opp_url' => ['/opportunities', '/opportunities'], 'opp_none' => ['Vous n\'avez encore soumis aucune demande.', 'You have not submitted any request yet.'],
  'opp_program' => ['Programme', 'Program'], 'opp_city' => ['Ville / pays', 'City / country'], 'opp_channel' => ['Canal', 'Channel'], 'opp_ch_contact' => ['Formulaire de contact', 'Contact form'], 'opp_ch_advisor' => ['Demande de conseiller', 'Advisor request'],
  'gains_title' => ['Mes gains', 'My earnings'], 'gains_intro' => ['Montants reçus (revenus et versements). Les frais ne sont pas comptés dans les gains.', 'Amounts received (revenue and payouts). Fees are not counted as earnings.'],
  'gains_total' => ['Total des gains', 'Total earnings'], 'gains_net_note' => ['Net après frais', 'Net after fees'], 'per_month' => ['Par mois', 'Per month'], 'per_year' => ['Par année', 'Per year'],
  'month' => ['Mois', 'Month'], 'year' => ['Année', 'Year'], 'total' => ['Total', 'Total'], 'gains_chart' => ['Gains mensuels', 'Monthly earnings'],
  // Messages directs (manager -> utilisateurs)
  'dm_button' => ['Message', 'Message'], 'dm_title' => ['Écrire un message', 'Write a message'], 'dm_close' => ['Fermer', 'Close'],
  'dm_recipients' => ['Destinataires', 'Recipients'], 'dm_search' => ['Rechercher un utilisateur (nom, e-mail)', 'Search a user (name, email)'],
  'dm_all_users' => ['Sélectionner tous les utilisateurs', 'Select all users'], 'dm_all_role' => ['Tout cocher : :role', 'Select all: :role'],
  'dm_role_all' => ['Tous les rôles', 'All roles'], 'dm_selected' => [':n sélectionné(s)', ':n selected'], 'dm_clear' => ['Vider', 'Clear'],
  'dm_no_users' => ['Aucun utilisateur validé.', 'No validated user.'], 'dm_no_match' => ['Aucun résultat.', 'No result.'],
  'dm_private_note' => ['Chaque destinataire reçoit une conversation séparée : personne ne voit les autres destinataires.', 'Each recipient gets a separate conversation: nobody sees the other recipients.'],
  'dm_send' => ['Envoyer', 'Send'], 'dm_sending' => ['Envoi…', 'Sending…'], 'dm_sent_ok' => ['Message envoyé à :n destinataire(s).', 'Message sent to :n recipient(s).'],
  'dm_e_recipients' => ['Choisissez au moins un destinataire.', 'Choose at least one recipient.'], 'dm_e_subject' => ['L\'objet est obligatoire.', 'Subject is required.'], 'dm_e_body' => ['Le message est obligatoire.', 'Message is required.'],
  'dm_e_server' => ['L\'envoi a échoué. Réessayez.', 'Sending failed. Please try again.'],
  'box_forms' => ['Formulaire de contact', 'Contact form'], 'box_sent' => ['Messages envoyés', 'Sent messages'], 'box_received' => ['Messages reçus', 'Received messages'],
  'dm_to' => ['À', 'To'], 'dm_from' => ['De', 'From'], 'dm_read' => ['Lu', 'Read'], 'dm_unread' => ['Non lu', 'Unread'], 'dm_replies' => ['réponse(s)', 'reply(ies)'],
  'dm_back' => ['Retour aux messages', 'Back to messages'], 'dm_reply' => ['Répondre', 'Reply'], 'dm_read_on' => ['Lu le', 'Read on'],
];
$out = [];
foreach ($rows as $k => [$fr, $en]) {
    $type = str_contains($fr, "\n") || mb_strlen($fr) > 90 ? 'textarea' : 'text';
    $out["sp.$k"] = [$T, $type, "$k (FR)", $fr];
    $out["sp.{$k}_en"] = [$T, $type, "$k (EN)", $en];
}
return $out;

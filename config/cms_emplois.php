<?php
/* Champs CMS de /emplois (components/kp/EmploisPageContent.tsx + EmploisPageHero.tsx + JobApplicationForm.tsx) — fusionnés dans config/cms.php ['fields']. Onglet « emp ».
   Les champs marqués « ajout Laravel » n'existent pas dans la source (CV joint, confirmation d'envoi) : la source ouvrait seulement un brouillon e-mail. */
$T = 'emp';
$P = function (string $k, string $type, string $label, string $fr, string $en) use ($T) {
    return ["$T.$k" => [$T, $type, "$label (FR)", $fr], "$T.{$k}_en" => [$T, $type, "$label (EN)", $en]];
};
$S = fn (string $k, string $type, string $label, string $v) => ["$T.$k" => [$T, $type, $label, $v]];
$FE = fn (array $rows) => array_map(fn ($r) => ['fr' => $r[0], 'en' => $r[1]], $rows);
return array_merge(
  $S('seo_title', 'text', 'SEO — titre de l\'onglet', 'Candidatures — KPANDJI AUTOMOBILES'),
  $S('seo_description', 'textarea', 'SEO — description', 'Envoyez votre candidature à KPANDJI AUTOMOBILES : parcours, compétences et CV joignez à votre message à notre équipe RH.'),
  $S('og_title', 'text', 'Open Graph — titre (partage)', 'Candidatures — KPANDJI AUTOMOBILES'),
  $S('og_description', 'textarea', 'Open Graph — description (partage)', 'Postulez auprès de KPANDJI AUTOMOBILES : formulaire guidé puis envoi par e-mail avec votre CV.'),
  // Hero
  $S('hero_image', 'image', 'En-tête — image de fond', 'models/plus/pic3-emplois.jpg'),
  $P('hero_kicker', 'text', 'En-tête — surtitre', 'Rejoignez l\'équipe', 'Join the team'),
  $P('hero_title', 'text', 'En-tête — titre', 'Candidature', 'Application'),
  $P('hero_text', 'textarea', 'En-tête — texte', 'Partagez votre parcours, vos compétences et une synthèse de votre CV. Une fois le brouillon e-mail créé, ajoutez votre pièce jointe PDF avant validation.', 'Share your background, your skills and a summary of your résumé. Once the email draft is created, attach your PDF before sending.'),
  $S('hero_chips_aria', 'text', 'En-tête — libellé accessibilité des points clés', 'Points clés'),
  ['emp.chips' => [$T, 'list', 'En-tête — points clés', $FE([['Formulaire CV & compétences', 'CV & skills form'], ['PJ PDF recommandée', 'PDF attachment recommended'], ['Contact RH', 'HR contact']]), ['fr' => 'Texte FR', 'en' => 'Texte EN']]],
  // Conseils
  $P('tips_label', 'text', 'Conseils — surtitre', 'Conseils', 'Tips'),
  ['emp.tips' => [$T, 'list', 'Conseils — cartes (icône : paper, spark)', [
      ['icon' => 'paper', 'title' => 'CV lisible & court', 'title_en' => 'A clear, concise résumé', 'body' => 'En deux ou trois phrases dans le champ dédié, mettez en avant ce qui nous aidera à comprendre votre profil avant d\'ouvrir votre PDF.', 'body_en' => 'In two or three sentences in the dedicated field, highlight what will help us understand your profile before opening your PDF.'],
      ['icon' => 'spark', 'title' => 'Compétences ciblées', 'title_en' => 'Targeted skills', 'body' => 'Reliez explicitement vos savoir-faire au poste visé ; les langues et les outils métiers comptent.', 'body_en' => 'Explicitly connect your skills to the target role; languages and job-specific tools matter.'],
  ], ['icon' => 'Icône', 'title' => 'Titre FR', 'title_en' => 'Titre EN', 'body' => 'Texte FR', 'body_en' => 'Texte EN']]],
  $P('help_title', 'text', 'Encadré contact — titre', 'Besoin d\'échanger avant d\'envoyer ?', 'Need to talk before sending?'),
  $P('help_text', 'textarea', 'Encadré contact — texte', 'Pour une question générale ou un premier contact, vous pouvez aussi nous écrire depuis la page Contact.', 'For a general question or an initial contact, you can also write to us from the Contact page.'),
  $P('help_btn', 'text', 'Encadré contact — bouton', 'Aller à la page Contact', 'Go to the Contact page'),
  $S('help_url', 'url', 'Encadré contact — lien', '/contact'),
  // Formulaire
  $P('f_kicker', 'text', 'Formulaire — surtitre', 'Candidature', 'Application'),
  $P('f_title', 'text', 'Formulaire — titre', 'Postuler chez Kpandji Automobiles', 'Apply at Kpandji Automobiles'),
  $P('f_intro', 'textarea', 'Formulaire — introduction', 'Décrivez votre parcours et vos compétences. Le bouton final ouvre un brouillon e-mail : joignez-y votre CV au format PDF avant d\'envoyer.', 'Describe your background and skills. The final button opens an email draft: attach your résumé as a PDF before sending.'),
  $P('f_optional', 'text', 'Formulaire — mention « optionnel »', 'optionnel', 'optional'),
  $P('f_sec_identity', 'text', 'Rubrique — identité', 'Votre identité', 'Your identity'),
  $P('f_name', 'text', 'Champ — nom complet', 'Nom complet', 'Full name'),
  $P('f_name_ph', 'text', 'Champ — nom complet (exemple)', 'Prénom et nom', 'First and last name'),
  $P('f_email', 'text', 'Champ — e-mail', 'E-mail', 'Email'),
  $S('f_email_ph', 'text', 'Champ — e-mail (exemple)', 'vous@exemple.com'),
  $P('f_phone', 'text', 'Champ — téléphone', 'Téléphone', 'Phone'),
  $S('f_phone_ph', 'text', 'Champ — téléphone (exemple)', '+225 …'),
  $P('f_sec_position', 'text', 'Rubrique — poste', 'Poste & candidature', 'Position & application'),
  $P('f_position', 'text', 'Champ — poste visé', 'Poste visé', 'Target position'),
  $P('f_position_ph', 'text', 'Champ — poste visé (exemple)', 'Ex. Mécanicien, commercial SAV, logistique… ou « Candidature spontanée »', 'E.g. Mechanic, after-sales sales rep, logistics… or "Open application"'),
  $P('f_sec_cv', 'text', 'Rubrique — CV', 'CV & parcours', 'Résumé & background'),
  $P('f_cv', 'text', 'Champ — synthèse du CV', 'Synthèse de votre CV', 'Résumé summary'),
  $P('f_cv_ph', 'textarea', 'Champ — synthèse du CV (exemple)', 'Résumé de votre formation, années d\'expérience, derniers employeurs ou secteurs, réalisations marquantes…', 'Summary of your education, years of experience, recent employers or industries, notable achievements…'),
  $P('f_file', 'text', 'Champ — fichier CV (ajout Laravel)', 'CV en pièce jointe', 'Résumé attachment'),
  $P('f_file_hint', 'text', 'Champ — fichier CV, aide (ajout Laravel)', 'PDF, DOC ou DOCX — 2 Mo maximum', 'PDF, DOC or DOCX — 2 MB maximum'),
  $P('f_education', 'text', 'Champ — formation', 'Formation & certifications', 'Education & certifications'),
  $P('f_education_ph', 'text', 'Champ — formation (exemple)', 'Diplômes, habilitations, formations continues…', 'Degrees, certifications, continuing education…'),
  $P('f_sec_skills', 'text', 'Rubrique — compétences', 'Compétences', 'Skills'),
  $P('f_skills', 'text', 'Champ — compétences', 'Compétences techniques & métiers', 'Technical & professional skills'),
  $P('f_skills_ph', 'textarea', 'Champ — compétences (exemple)', 'Listez vos savoir-faire (réparation véhicules, méthodes, ERP, CRM, gestion de stock, HSE…). Séparez par des virgules ou des lignes.', 'List your skills (vehicle repair, methods, ERP, CRM, inventory management, HSE…). Separate with commas or line breaks.'),
  $P('f_languages', 'text', 'Champ — langues', 'Langues', 'Languages'),
  $P('f_languages_ph', 'text', 'Champ — langues (exemple)', 'Ex. Français (natif), anglais (B2)…', 'E.g. French (native), English (B2)…'),
  $P('f_extras', 'text', 'Champ — message complémentaire', 'Message complémentaire', 'Additional message'),
  $P('f_extras_ph', 'text', 'Champ — message complémentaire (exemple)', 'Disponibilité, mobilité, permis de conduire, lien LinkedIn…', 'Availability, mobility, driver\'s license, LinkedIn link…'),
  $P('f_submit', 'text', 'Bouton — envoyer', 'Ouvrir l\'e-mail', 'Open the email'),
  $P('f_sending', 'text', 'Bouton — envoi en cours (ajout Laravel)', 'Envoi en cours…', 'Sending…'),
  $P('f_dest_label', 'text', 'Mention — « Destination »', 'Destination', 'Destination'),
  $S('f_dest_email', 'text', 'Adresse e-mail RH (destination du brouillon)', 'contact@kpandji.com'),
  $P('f_dest_hint', 'text', 'Mention — rappel pièce jointe', 'Pensez à joindre votre CV en PDF après ouverture de la fenêtre mail.', 'Remember to attach your résumé as a PDF after the mail window opens.'),
  $P('f_ok_title', 'text', 'Confirmation — titre (ajout Laravel)', 'Candidature enregistrée', 'Application received'),
  $P('f_ok_text', 'textarea', 'Confirmation — texte (ajout Laravel)', 'Merci. Votre candidature a bien été transmise à notre équipe RH.', 'Thank you. Your application has been sent to our HR team.'),
  $P('f_ok_mail', 'text', 'Confirmation — lien brouillon e-mail (ajout Laravel)', 'Ouvrir aussi le brouillon e-mail', 'Also open the email draft'),
  $P('mail_subject', 'text', 'Brouillon e-mail — préfixe du sujet', '[KPANDJI Automobiles — Candidature]', '[KPANDJI Automobiles — Application]'),
  $P('mail_intro', 'textarea', 'Brouillon e-mail — introduction', 'Je souhaite rejoindre l\'équipe de Kpandji Automobiles. Voici les informations figurant aussi sur mon CV (pièce jointe à ajouter après ouverture de ce message).', 'I would like to join the Kpandji Automobiles team. Below is the information also included in my résumé (attachment to add after opening this message).'),
  // Erreurs
  $P('e_name', 'text', 'Erreur — nom', 'Indiquez votre nom et prénom.', 'Please enter your full name.'),
  $P('e_email', 'text', 'Erreur — e-mail manquant', 'Indiquez votre adresse e-mail.', 'Please enter your email address.'),
  $P('e_email_invalid', 'text', 'Erreur — e-mail invalide', 'Adresse e-mail invalide.', 'Invalid email address.'),
  $P('e_position', 'text', 'Erreur — poste', 'Indiquez le poste visé ou précisez « Candidature spontanée ».', 'Please indicate the target position or specify "Open application".'),
  $P('e_cv', 'text', 'Erreur — synthèse du CV', 'Rédigez une courte synthèse de votre CV (parcours et expériences clés).', 'Please write a short summary of your résumé (background and key experience).'),
  $P('e_skills', 'text', 'Erreur — compétences', 'Listez vos compétences techniques et professionnelles.', 'Please list your technical and professional skills.'),
  $P('e_file', 'text', 'Erreur — fichier CV (ajout Laravel)', 'Le CV doit être un fichier PDF, DOC ou DOCX de 2 Mo maximum.', 'The résumé must be a PDF, DOC or DOCX file of 2 MB maximum.'),
  $P('e_server', 'text', 'Erreur — serveur (ajout Laravel)', 'Une erreur est survenue. Réessayez plus tard.', 'An error occurred. Please try again later.'),
  $P('e_network', 'text', 'Erreur — connexion (ajout Laravel)', 'Impossible d\'envoyer la candidature. Vérifiez votre connexion.', 'Unable to send the application. Please check your connection.'),
);

<?php
       /* Nano-SKUD FRANÇAIS Chaînes de localisation - MACHINE TRANSLATION */
  /* Varialbles for replacing; Set $localization = 'fr'; dans la configuration pour l'application. */

  $loc_user_agent_admin_redirect = 'Navigateur du client <Br/><B>%s</B><Br/> détecté - rediriger vers la zone d\'administration ...';
  $loc_options_parameter = 'Option';
  $loc_options_value = 'Évaluer';
  $loc_options_description = 'La description';
  $loc_options_checkbox_hint = 'Définir une marque pour l\'option d\'activation';
  $loc_options_numberinput_hint = 'Valeur numérique d\'entrée';
  $loc_button_save_changes = 'Enregistrer les paramètres';

  $loc_opts_show_anonym_stat = 'Afficher les statistiques aux anonymes';
  $loc_opts_ua_regexp_root_redirect = 'Rediriger les navigateurs du client vers la zone d\'administration (RegExp)';
  $loc_opts_restrict_open_door_ips = 'Autoriser l\'ouverture de la porte à distance uniquement à partir de ces IP';
  $loc_opts_restrict_manage_keys_ips = 'Autoriser les clés de modification uniquement à partir de ces adresses IP';
  $loc_opts_restrict_enroll_keys_ips = 'Autoriser l\'inscription des clés uniquement à partir de ces adresses IP';
  $loc_opts_restrict_anonim_view_ips = 'Autoriser l\'affichage de statistiques anonymes uniquement sur ces adresses IP';
  $loc_opts_allow_autoreg_controllers = 'Enregistrer automatiquement les nouveaux contrôleurs dans la base de données';
  $loc_opts_allow_autoreg_auto_ip_filt = 'Lors de l\'enregistrement automatique des contrôleurs, liez ceci à l\'IP';
  $loc_opts_hardware_z5r_interval = 'Intervalle d\'interrogation pour Z5R-Web';
  $loc_opts_global_sysname = 'Nom du système';
  $loc_opts_email_recovery_from = 'Adresse de pour les e-mails de récupération de mot de passe';
  $loc_opts_allow_profile_edit_pswd = 'Autoriser les utilisateurs à changer leur mot de passe';
  $loc_opts_allow_profile_edit_iprange = 'Autoriser les utilisateurs à modifier l\'adresse IP auto-autorisée';
  $loc_opts_allow_profile_edit_email = 'Autoriser les utilisateurs à modifier leur propre adresse e-mail';
  $loc_opts_allow_profile_edit_comment = 'Autoriser les utilisateurs à modifier leur propre commentaire';
  $loc_opts_allow_passwd_email_recovery = 'Autoriser la récupération des mots de passe par e-mail';
  $loc_opts_email_recovery_use_captcha = 'Exiger captcha lors de la récupération du mot de passe par e-mail';

  $loc_reports_presence_in_office = 'Rapport de présence';
  $loc_reports_presence_in_office_per_days = 'Rapport de présence par jours';
  $loc_reports_outage_in_office = 'Rapport d\'absence';
  $loc_reports_inactive_keys = 'Rapport sur les clés inactives';
  $loc_reports_alarm_events = 'Rapport d\'événements d\'alarme';

  $loc_menu_element_controllers = 'Contrôleurs';
  $loc_menu_element_keys = 'Clés';
  $loc_menu_element_offices = 'Des bureaux';
  $loc_menu_element_logins = 'Connexions';
  $loc_menu_element_badkeys = 'Mauvaises clés';
  $loc_menu_element_proxy_events = 'Procuration';
  $loc_menu_element_options = 'Choix';
  $loc_menu_element_converter = 'Convertisseur';
  $loc_menu_element_profile = 'Votre profil';
  $loc_menu_element_reports = 'Rapports';

  $loc_susbys_greeting = 'Bienvenu !';
  $loc_susbys_email_pswd_recovery = 'récupération de mot de passe par e-mail';
  $loc_susbys_email_pswd_recovery_mail_body1 = 'Si vous ne demandez pas la récupération du mot de passe, supprimez simplement cet e-mail.';
  $loc_susbys_email_pswd_recovery_mail_body2 = 'Ne divulguez, ne partagez ou ne soumettez jamais vos mots de passe et les liens des e-mails de récupération de mot de passe !<Br/>
        Liens de récupération de mot de passe valides uniquement jusqu\'au jour en cours (vingt-quatre heures) et même adresse IP. L\'authentification à deux facteurs ne sera pas réinitialisée';
  $loc_susbys_email_pswd_recovery_ok_inform = 'D\'ACCORD. Vérifiez votre e-mail et cliquez sur le lien dans l\'e-mail pour réinitialiser le mot de passe';
  $loc_susbys_email_pswd_recovery_bad_hash = 'Le lien a expiré ou a déjà été utilisé.';
  $loc_susbys_list_events = 'Liste des événements';
  $loc_susbys_list_queue = 'File d\'attente des commandes';
  $loc_susbys_controllers_autoreg = 'Enregistrement automatique des contrôleurs';
  $loc_susbys_controllers_need_name = '⚠ Attribuer un nom au contrôleur ⚠';
  $loc_susbys_src_ip_bind = 'Liaison IP';
  $loc_susbys_src_ip_bind_help = 'Lors du changement d\'adresse IP, vous devez modifier l\'adresse IP dans la base de données ou ajouter des sous-réseaux à la liste blanche.';
  $loc_susbys_src_ip_bind_help2 = 'N\'importe quelle adresse IP peut envoyer les données du nouveau contrôleur ajouté.';
  $loc_susbys_src_ip_bind_help3 = 'Les nouveaux contrôleurs ne peuvent être ajoutés que manuellement.';
  $loc_susbys_controllers_no_data = 'PAS de données de contrôleurs actifs. Au moins un contrôleur doit envoyer des données.';
  $loc_susbys_addkeys_help1 = 'Si la clé n\'a pas été trouvée dans la base de données locale, elle sera ajoutée.<Br />Votre clé ne peut être enregistrée qu\'au contrôleur enregistré (par numéro de série)';
  $loc_susbys_addkeys_tzhelp1 = 'Exemple TZ (masque binaire de régions temporelles)';
  $loc_susbys_delkeys_help1 = 'Supprimer de la base de données et de TOUS les contrôleurs';
  $loc_susbys_delkeys_help2 = 'Les contrôleurs éteints ou inactifs depuis longtemps peuvent éviter la suppression des clés';
  $loc_susbys_logins_help1 = 'Les connexions sont réservées aux tâches de gestion. N\'affecte pas le fonctionnement du contrôleur.';
  $loc_susbys_badkeys_help1 = 'Les clés incorrectes ou compromises ne peuvent pas être ajoutées aux contrôleurs';
  $loc_susbys_confirm_mail_send = 'Confirmez l\'envoi de l\'e-mail avec le code.';
  $loc_susbys_mail_sending = 'Envoyer un e-mail ...';
  $loc_susbys_2fa_method = 'Méthode';
  $loc_susbys_2fa_sign = 'Signature';
  $loc_susbys_2fa_wallet = 'Porte monnaie';
  $loc_susbys_2fa_shared_secret = 'Secret partagé';
  $loc_susbys_2fa_test_string = 'Tester la chaîne';
  $loc_common_2fa_confirm = 'Confirmation';
  $loc_susbys_2fa_help1 = 'Utilisez le portefeuille de crypto-monnaie Legacy (P2PKH) ou Script (P2SH) ... ou tout portefeuille de crypto-monnaie compatible BitcoinECDSA<Br>
                   Electrum : make_seed(seed_type="standard") , phrase d\'importation<Br/>Signer la chaîne de test et envoyer la signature détachée<Br/>
                   Votre portefeuille sera requis pour l\'authentification.';
  $loc_susbys_2fa_help2 = 'signer la chaîne de test avec votre portefeuille';
  $loc_susbys_open_door = 'Ouvre la porte';

  $loc_common_phrase_not_accessible = 'pas accessible';
  $loc_common_phrase_ip_not_allowed = 'cette IP n\'est pas autorisée';
  $loc_common_phrase_disabled_global_options = 'désactivé dans les options globales';
  $loc_common_phrase_disabled_user_profile = 'inaccessible pour le profil utilisateur';
  $loc_common_phrase_no_datarecords = 'Pas de données. Ajoutez au moins un enregistrement.';
  $loc_common_phrase_on = 'SUR';
  $loc_common_phrase_off = 'DÉSACTIVÉ';
  $loc_common_phrase_sn = 'numéro de série';
  $loc_common_phrase_hw = 'équipement';
  $loc_common_phrase_2fa = 'authentification à deux facteurs';
  $loc_common_phrase_type = 'Taper';
  $loc_common_phrase_activity = 'Activité';

  $loc_entity_name_controller = 'contrôleur';
  $loc_entity_name_key = 'clé';
  $loc_entity_name_office = 'Bureau';
  $loc_entity_name_username = 'Nom d\'utilisateur';
  $loc_entity_name_statistic = 'Statistique';
  $loc_entity_name_badkey = 'mauvaise clé';
  $loc_entity_name_proxyevent = 'événement-proxyt';
  $loc_entity_name_event = 'un événement';
  $loc_entity_name_button = 'bouton';
  $loc_entity_name_network = 'réseau';

  $loc_property_name_access = 'Accéder';
  $loc_property_name_reject = 'Rejeter';
  $loc_property_name_created = 'Établi';
  $loc_property_name_last_activity = 'Dernière Activité';
  $loc_property_name_name = 'Nom';
  $loc_property_name_address = 'Adresse';
  $loc_property_name_enable = 'SUR';
  $loc_property_name_accessrights = 'Des droits d\'accès';
  $loc_property_name_description = 'La description';
  $loc_property_name_code = 'Code';
  $loc_property_name_url = 'Cible URL';
  $loc_property_name_ipsubnets = 'Sous-réseaux IP';
  $loc_property_name_time = 'temps';
  $loc_property_name_command = 'Commande';
  $loc_property_name_executed = 'Réalisé';
  $loc_property_name_executor = 'sous-système exec';

  $loc_common_phrase_add = 'Ajouter';
  $loc_common_phrase_save = 'Sauver';
  $loc_common_phrase_enroll = 'Inscrire';
  $loc_common_phrase_edit = 'Éditer';
  $loc_common_phrase_manage = 'Faire en sorte';
  $loc_common_phrase_del = 'Effacer';
  $loc_common_phrase_login = 'S\'identifier';
  $loc_common_phrase_logout = 'Déconnexion';
  $loc_common_phrase_send = 'Envoyer';
  $loc_common_phrase_username = 'Connexion';
  $loc_common_phrase_password = 'Mot de passe';
  $loc_common_phrase_email_address = 'Email';
  $loc_common_phrase_not_active = 'PAS ACTIF';
  $loc_common_phrase_always = 'Toujours';
  $loc_common_phrase_never = 'Jamais';
  $loc_common_phrase_help = 'aider';

  $loc_common_phrase_must_be_filled = 'doit être rempli';
  $loc_common_phrase_not_found = 'pas trouvé';
  $loc_common_phrase_error = 'Erreur';

?>

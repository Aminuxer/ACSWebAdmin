<?php
       /* Nano-SKUD DEUTSCH Localization strings */
  /* Varialbles for replacing; Set $localization = 'de'; in config for apply */

  $loc_user_agent_admin_redirect = 'Browser des Clients <Br/><B>%s</B><Br/> erkannt - Weiterleitung zum Admin-Bereich ...';
  $loc_options_parameter = 'Option';
  $loc_options_value = 'Wert';
  $loc_options_description = 'Beschreibung';
  $loc_options_checkbox_hint = 'Markierung für Aktivierungsoption setzen';
  $loc_options_numberinput_hint = 'Zahlenwert eingeben';
  $loc_button_save_changes = 'Einstellungen speichern';

  $loc_opts_show_anonym_stat = 'Statistiken anonym anzeigen';
  $loc_opts_ua_regexp_root_redirect = 'Leiten Sie die Browser des Clients in den Admin-Bereich um (RegExp)';
  $loc_opts_restrict_open_door_ips = 'Remote-Türöffnen nur von diesen IPs zulassen';
  $loc_opts_restrict_manage_keys_ips = 'Bearbeitungsschlüssel nur von diesen IPs zulassen';
  $loc_opts_restrict_enroll_keys_ips = 'Registrierungsschlüssel nur von diesen IPs zulassen';
  $loc_opts_restrict_anonim_view_ips = 'Anzeige anonymer Statistiken nur für diese IPs zulassen';
  $loc_opts_allow_autoreg_controllers = 'Registrieren Sie neue Controller automatisch in der Datenbank';
  $loc_opts_allow_autoreg_auto_ip_filt = 'Binden Sie dies bei der automatischen Registrierung des Controllers an die IP';
  $loc_opts_hardware_z5r_interval = 'Abfrageintervall für Z5R-Web';
  $loc_opts_global_sysname = 'Systemname';
  $loc_opts_email_recovery_from = 'Adresse von für E-Mails zur Kennwortwiederherstellung';
  $loc_opts_allow_profile_edit_pswd = 'Erlauben Sie Benutzern, das eigene Passwort zu ändern';
  $loc_opts_allow_profile_edit_iprange = 'Erlauben Sie Benutzern, selbst erlaubte IPs zu ändern';
  $loc_opts_allow_profile_edit_email = 'Erlauben Sie Benutzern, ihre eigene E-Mail-Adresse zu ändern';
  $loc_opts_allow_profile_edit_comment = 'Erlauben Sie Benutzern, den Selbstkommentar zu ändern';
  $loc_opts_allow_passwd_email_recovery = 'Passwortwiederherstellung per E-Mail zulassen';
  $loc_opts_email_recovery_use_captcha = 'Fordern Sie ein Captcha bei der Passwortwiederherstellung per E-Mail an';

  $loc_reports_presence_in_office = 'Anwesenheitsbericht';
  $loc_reports_presence_in_office_per_days = 'Anwesenheitsmeldung pro Tag';
  $loc_reports_outage_in_office = 'Abwesenheitsbericht';
  $loc_reports_inactive_keys = 'Bericht über inaktive Schlüssel';
  $loc_reports_alarm_events = 'Alarmereignisbericht';

  $loc_menu_element_controllers = 'Controllers';
  $loc_menu_element_keys = 'Schlüssel';
  $loc_menu_element_offices = 'Büros';
  $loc_menu_element_logins = 'Anmeldungen';
  $loc_menu_element_badkeys = 'Schlechte Schlüssel';
  $loc_menu_element_proxy_events = 'Proxy';
  $loc_menu_element_options = 'Optionen';
  $loc_menu_element_converter = 'Konverter';
  $loc_menu_element_profile = 'Dein Profil';
  $loc_menu_element_reports = 'Berichte';

  $loc_susbys_greeting = 'Herzlich willkommen !';
  $loc_susbys_email_pswd_recovery = 'Passwortwiederherstellung per E-Mail';
  $loc_susbys_email_pswd_recovery_mail_body1 = 'Wenn Sie keine Passwortwiederherstellung anfordern, entfernen Sie einfach diese E-Mail.';
  $loc_susbys_email_pswd_recovery_mail_body2 = 'Geben Sie niemals Ihre Passwörter und Links aus E-Mails zur Passwortwiederherstellung preis, teilen oder übermitteln Sie sie nicht!<Br/>
        Links zur Kennwortwiederherstellung sind nur bis zum aktuellen Tag (vierundzwanzig Stunden) und derselben IP-Adresse gültig. Zwei-Faktor-Authentifizierung wird nicht zurückgesetzt';
  $loc_susbys_email_pswd_recovery_ok_inform = 'OK. Überprüfen Sie Ihre E-Mails und klicken Sie auf den Link in der E-Mail, um das Passwort zurückzusetzen';
  $loc_susbys_email_pswd_recovery_bad_hash = 'Der Link ist abgelaufen oder wurde bereits verwendet.';
  $loc_susbys_list_events = 'Liste der Veranstaltungen';
  $loc_susbys_list_queue = 'Befehlswarteschlange';
  $loc_susbys_controllers_autoreg = 'Automatische Registrierung von Controllern';
  $loc_susbys_controllers_need_name = '⚠ Controller einen Namen zuweisen ⚠';
  $loc_susbys_src_ip_bind = 'IP-Bindung';
  $loc_susbys_src_ip_bind_help = 'Bei der Änderung der IP-Adresse müssen Sie die IP in der Datenbank bearbeiten oder Subnetze der Whitelist hinzufügen.';
  $loc_susbys_src_ip_bind_help2 = 'Jede IP kann die Daten des neu hinzugefügten Controllers senden.';
  $loc_susbys_src_ip_bind_help3 = 'Neue Controller können nur manuell hinzugefügt werden.';
  $loc_susbys_controllers_no_data = 'KEINE aktiven Controller-Daten. Mindestens ein Controller muss Daten senden.';
  $loc_susbys_addkeys_help1 = 'Wenn der Schlüssel nicht in der lokalen Datenbank gefunden wurde, wird er hinzugefügt.<Br />Ihr Schlüssel kann nur für einen registrierten Controller (nach Seriennummer) registriert werden.';
  $loc_susbys_addkeys_tzhelp1 = 'Beispiel TZ (Zeitregionen-Bitmaske)';
  $loc_susbys_delkeys_help1 = 'Aus Datenbank und ALLEN Controllern löschen';
  $loc_susbys_delkeys_help2 = 'Ausgeschaltete oder längere Zeit inaktive Controller können das Löschen von Schlüsseln vermeiden';
  $loc_susbys_logins_help1 = 'Anmeldungen dienen nur Verwaltungsaufgaben. Beeinträchtigen Sie nicht den Betrieb des Controllers.';
  $loc_susbys_badkeys_help1 = 'Fehlerhafte oder kompromittierte Schlüssel können Controllern nicht hinzugefügt werden';
  $loc_susbys_confirm_mail_send = 'Bestätigen Sie das Senden der E-Mail mit Code.';
  $loc_susbys_mail_sending = 'E-Mail senden ...';
  $loc_susbys_2fa_method = 'Methode';
  $loc_susbys_2fa_sign = 'Unterschrift';
  $loc_susbys_2fa_wallet = 'Geldbörse';
  $loc_susbys_2fa_shared_secret = 'Geteiltes Geheimnis';
  $loc_susbys_2fa_test_string = 'Testzeichenfolge';
  $loc_common_2fa_confirm = 'Bestätigung';
  $loc_susbys_2fa_help1 = 'Verwenden Sie Legacy (P2PKH) oder Script (P2SH) Kryptowährungs-Wallet ... oder jede BitcoinECDSA-kompatible Krypto-Wallet<Br>
                    Electrum: make_seed(seed_type="standard") , Phrase importieren<Br/>Teststring signieren und getrennte Signatur senden<Br/>
                    Ihre Brieftasche wird für die Authentifizierung benötigt.';
  $loc_susbys_2fa_help2 = 'Signieren Sie die Testzeichenfolge mit Ihrer Brieftasche';
  $loc_susbys_open_door = 'Öffne die Tür';

  $loc_common_phrase_not_accessible = 'nicht zugänglich';
  $loc_common_phrase_ip_not_allowed = 'diese IP nicht erlaubt';
  $loc_common_phrase_disabled_global_options = 'in den globalen Optionen deaktiviert';
  $loc_common_phrase_disabled_user_profile = 'unzugänglich für Benutzerprofil';
  $loc_common_phrase_no_datarecords = 'Keine Daten. Fügen Sie mindestens einen Datensatz hinzu.';
  $loc_common_phrase_on = 'AN';
  $loc_common_phrase_off = 'AUS';
  $loc_common_phrase_sn = 'Seriennummer';
  $loc_common_phrase_hw = 'Ausrüstung';
  $loc_common_phrase_2fa = 'Zwei-Faktor-Authentifizierung';
  $loc_common_phrase_type = 'Typ';
  $loc_common_phrase_activity = 'Aktivität';

  $loc_entity_name_controller = 'Controller';
  $loc_entity_name_key = 'Schlüssel';
  $loc_entity_name_office = 'Büro';
  $loc_entity_name_username = 'Nutzername';
  $loc_entity_name_statistic = 'Statistik';
  $loc_entity_name_badkey = 'schlechter Schlüssel';
  $loc_entity_name_proxyevent = 'Proxy-Ereignis';
  $loc_entity_name_event = 'Ereignis';
  $loc_entity_name_button = 'Taste';
  $loc_entity_name_network = 'Netzwerk';

  $loc_property_name_access = 'Zugang';
  $loc_property_name_reject = 'Ablehnen';
  $loc_property_name_created = 'Erstellt';
  $loc_property_name_last_activity = 'Letzte Aktivität';
  $loc_property_name_name = 'Name';
  $loc_property_name_address = 'Adresse';
  $loc_property_name_enable = 'AN';
  $loc_property_name_accessrights = 'Zugangsrechte';
  $loc_property_name_description = 'Beschreibung';
  $loc_property_name_code = 'Code';
  $loc_property_name_url = 'Ziel-URL';
  $loc_property_name_ipsubnets = 'IP-Subnetze';
  $loc_property_name_time = 'Zeit';
  $loc_property_name_command = 'Befehl';
  $loc_property_name_executed = 'Hingerichtet';
  $loc_property_name_executor = 'Teilsystem';

  $loc_common_phrase_add = 'Hinzufügen';
  $loc_common_phrase_save = 'Speichern';
  $loc_common_phrase_enroll = 'Anmelden';
  $loc_common_phrase_edit = 'Bearbeiten';
  $loc_common_phrase_manage = 'Verwalten';
  $loc_common_phrase_del = 'Löschen';
  $loc_common_phrase_login = 'Einloggen';
  $loc_common_phrase_logout = 'Ausloggen';
  $loc_common_phrase_send = 'Senden';
  $loc_common_phrase_username = 'Anmeldung';
  $loc_common_phrase_password = 'Passwort';
  $loc_common_phrase_email_address = 'Email';
  $loc_common_phrase_not_active = 'NICHT AKTIV';
  $loc_common_phrase_always = 'Stets';
  $loc_common_phrase_never = 'Niemals';
  $loc_common_phrase_help = 'Hilfe';

  $loc_common_phrase_must_be_filled = 'Muss ausgefüllt sein';
  $loc_common_phrase_not_found = 'nicht gefunden';
  $loc_common_phrase_error = 'Fehler';

?>

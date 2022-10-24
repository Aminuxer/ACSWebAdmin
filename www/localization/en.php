<?php
       /* Nano-SKUD ENGLISH Localization strings */
  /* Varialbles for replacing; Set $localization = 'en'; in config for apply */

  $loc_user_agent_admin_redirect = 'Client \'s browser <Br/><B>%s</B><Br/> detected - redirect to admin area ...';
  $loc_options_parameter = 'Option';
  $loc_options_value = 'Value';
  $loc_options_description = 'Description';
  $loc_options_checkbox_hint = 'Set mark for enable option';
  $loc_options_numberinput_hint = 'Input number value';
  $loc_button_save_changes = 'Save settings';

  $loc_opts_show_anonym_stat = 'Show statistics to anonymous';
  $loc_opts_ua_regexp_root_redirect = 'Redirect client \'s browsers to admin area (RegExp)';
  $loc_opts_restrict_open_door_ips = 'Allow remote door open only from this IPs';
  $loc_opts_restrict_manage_keys_ips = 'Allow edit keys only from this IPs';
  $loc_opts_restrict_enroll_keys_ips = 'Allow enroll keys only from this IPs';
  $loc_opts_restrict_anonim_view_ips = 'Allow display anonymous statistics only to this IPs';
  $loc_opts_allow_autoreg_controllers = 'Automatically register new controllers in DB';
  $loc_opts_allow_autoreg_auto_ip_filt = 'At controllers autoregistration bind this to IP';
  $loc_opts_hardware_z5r_interval = 'Polling interval for Z5R-Web';
  $loc_opts_global_sysname = 'System name';
  $loc_opts_email_recovery_from = 'Address From for password recovery emails';
  $loc_opts_allow_profile_edit_pswd = 'Allow users change self password';
  $loc_opts_allow_profile_edit_iprange = 'Allow users change self allowed IP';
  $loc_opts_allow_profile_edit_email = 'Allow users change self email';
  $loc_opts_allow_profile_edit_comment = 'Allow users change self comment';
  $loc_opts_allow_passwd_email_recovery = 'Allow passwords recovery via email';

  $loc_reports_presence_in_office = 'Presence report';
  $loc_reports_outage_in_office = 'Outage report';
  $loc_reports_inactive_keys = 'Inactive keys report';
  $loc_reports_alarm_events = 'Alarm events report';

  $loc_menu_element_controllers = 'Controllers';
  $loc_menu_element_keys = 'Keys';
  $loc_menu_element_offices = 'Offices';
  $loc_menu_element_logins = 'Logins';
  $loc_menu_element_badkeys = 'Bad keys';
  $loc_menu_element_proxy_events = 'Proxy';
  $loc_menu_element_options = 'Options';
  $loc_menu_element_converter = 'Converter';
  $loc_menu_element_profile = 'Your profile';
  $loc_menu_element_reports = 'Reports';

  $loc_susbys_greeting = 'Welcome !';
  $loc_susbys_email_pswd_recovery = 'E-Mail password recovery';
  $loc_susbys_email_pswd_recovery_mail_body1 = 'If you don\'t request password recovery, just remove this email.';
  $loc_susbys_email_pswd_recovery_mail_body2 = 'Never disclosure, share or submit your passwords and links from password-recovery emails !<Br/>
        Password recovery links valid only through current day (twenty-four hours) and same IP-address. Two-factor authentication won\'t reset';
  $loc_susbys_email_pswd_recovery_ok_inform = 'OK. Check your email and click link in email for password reset';
  $loc_susbys_email_pswd_recovery_bad_hash = 'The link has expired or has been used before.';
  $loc_susbys_list_events = 'List of events';
  $loc_susbys_list_queue = 'Commands queue';
  $loc_susbys_controllers_autoreg = 'Controllers auto-registration';
  $loc_susbys_controllers_need_name = '⚠ Assign name to controller ⚠';
  $loc_susbys_src_ip_bind = 'IP-binding';
  $loc_susbys_src_ip_bind_help = 'At IP-address change your must edit IP in database or add whitelist subnets.';
  $loc_susbys_src_ip_bind_help2 = 'Any IP can send new-added controller\'s data.';
  $loc_susbys_src_ip_bind_help3 = 'New controllers can be added manually only.';
  $loc_susbys_controllers_no_data = 'NO active contollers data. At least one controller must send data.';
  $loc_susbys_addkeys_help1 = 'If key wasn\'t found in local DB - it will be added.<Br />Your can enroll key only to registered controller (by serail number)';
  $loc_susbys_addkeys_tzhelp1 = 'Example TZ (timezones bitmap)';
  $loc_susbys_delkeys_help1 = 'Delete from DB and ALL controllers';
  $loc_susbys_delkeys_help2 = 'Powered off or long-time inactive controllers can avoid deleting keys';
  $loc_susbys_logins_help1 = 'Logins for management tasks only. Don\'t rely to controller\'s work.';
  $loc_susbys_badkeys_help1 = 'Bad or compromized keys cannot be added to controllers';
  $loc_susbys_confirm_mail_send = 'Confirm sendind email with code.';
  $loc_susbys_mail_sending = 'Sening email ...';
  $loc_susbys_2fa_method = 'Method';
  $loc_susbys_2fa_sign = 'Sign';
  $loc_susbys_2fa_wallet = 'Wallet';
  $loc_susbys_2fa_shared_secret = 'Shared secret';
  $loc_susbys_2fa_test_string = 'Test string';
  $loc_common_2fa_confirm = 'Confirmation';
  $loc_susbys_2fa_help1 = 'Use Legacy (P2PKH) or Script (P2SH) crypto-currency wallet ... or any BitcoinECDSA compatible crypto-wallet <Br>
                    Electrum: make_seed(seed_type="standard") , import phrase<Br/>Sign test string and send detached signature<Br/>
                    Your wallet will required for authentication.';
  $loc_susbys_2fa_help2 = 'sign test string with your wallet';
  $loc_susbys_open_door = 'Open door';

  $loc_common_phrase_not_accessible = 'not accessible';
  $loc_common_phrase_ip_not_alllowed = 'this IP not allowed';
  $loc_common_phrase_disabled_global_options = 'disabled in global options';
  $loc_common_phrase_disabled_user_profile = 'inaccessible for user profile';
  $loc_common_phrase_no_datarecords = 'No data. Add at least one record.';
  $loc_common_phrase_on = 'ON';
  $loc_common_phrase_off = 'OFF';
  $loc_common_phrase_sn = 'SN';
  $loc_common_phrase_hw = 'HW';
  $loc_common_phrase_2fa = '2FA';
  $loc_common_phrase_type = 'Type';
  $loc_common_phrase_activity = 'Activity';

  $loc_entity_name_controller = 'controller';
  $loc_entity_name_key = 'key';
  $loc_entity_name_office = 'office';
  $loc_entity_name_username = 'Username';
  $loc_entity_name_statistic = 'Statistic';
  $loc_entity_name_badkey = 'bad-key';
  $loc_entity_name_proxyevent = 'proxy-event';
  $loc_entity_name_event = 'event';
  $loc_entity_name_button = 'button';
  $loc_entity_name_network = 'network';

  $loc_property_name_access = 'Access';
  $loc_property_name_reject = 'Reject';
  $loc_property_name_created = 'Created';
  $loc_property_name_last_activity = 'Last activity';
  $loc_property_name_name = 'Name';
  $loc_property_name_address = 'Address';
  $loc_property_name_enable = 'ON';
  $loc_property_name_accessrights = 'Access Rights';
  $loc_property_name_description = 'Description';
  $loc_property_name_code = 'Code';
  $loc_property_name_url = 'Target URL';
  $loc_property_name_ipsubnets = 'IP-subnets';
  $loc_property_name_time = 'time';
  $loc_property_name_command = 'Command';
  $loc_property_name_executed = 'Executed';
  $loc_property_name_executor = 'exec subsystem';

  $loc_common_phrase_add = 'Add';
  $loc_common_phrase_save = 'Save';
  $loc_common_phrase_enroll = 'Enroll';
  $loc_common_phrase_edit = 'Edit';
  $loc_common_phrase_manage = 'Manage';
  $loc_common_phrase_del = 'Delete';
  $loc_common_phrase_login = 'Sign-in';
  $loc_common_phrase_logout = 'Sign-out';
  $loc_common_phrase_send = 'Send';
  $loc_common_phrase_username = 'Login';
  $loc_common_phrase_password = 'Password';
  $loc_common_phrase_email_address = 'E-Mail';
  $loc_common_phrase_not_active = 'NOT ACTIVE';
  $loc_common_phrase_always = 'Always';
  $loc_common_phrase_never = 'Never';

  $loc_common_phrase_must_be_filled = 'must be filled';
  $loc_common_phrase_not_found = 'not found';
  $loc_common_phrase_error = 'Error';

?>

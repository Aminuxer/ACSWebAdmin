<?php
       /* Nano-SKUD SPANISH Localization strings (MACHINE TRANSLATION */
  /* Varialbles for replacing; Set $localization = 'es'; in config for apply */

  $loc_user_agent_admin_redirect = 'Navegador del cliente <Br/><B>%s</B><Br/> detectado - redirigir al área de administración ...';
  $loc_options_parameter = 'Opción';
  $loc_options_value = 'Valor';
  $loc_options_description = 'Descripción';
  $loc_options_checkbox_hint = 'Establecer marca para habilitar la opción';
  $loc_options_numberinput_hint = 'Valor del número de entrada';
  $loc_button_save_changes = 'Guardar ajustes';

  $loc_opts_show_anonym_stat = 'Mostrar estadísticas a anónimo';
  $loc_opts_ua_regexp_root_redirect = 'Redirigir los navegadores de los clientes al área de administración (RegExp)';
  $loc_opts_restrict_open_door_ips = 'Permitir que la puerta remota se abra solo desde estas direcciones IP';
  $loc_opts_restrict_manage_keys_ips = 'Permitir editar claves solo desde estas IP';
  $loc_opts_restrict_enroll_keys_ips = 'Permitir inscribir claves solo desde estas IP';
  $loc_opts_restrict_anonim_view_ips = 'Permitir mostrar estadísticas anónimas solo a estas IP';
  $loc_opts_allow_autoreg_controllers = 'Registrar automáticamente nuevos controladores en la base de datos';
  $loc_opts_allow_autoreg_auto_ip_filt = 'En el registro automático de los controladores, vincule esto a la IP';
  $loc_opts_hardware_z5r_interval = 'Intervalo de sondeo para Z5R-Web';
  $loc_opts_global_sysname = 'Nombre del sistema';
  $loc_opts_email_recovery_from = 'Dirección de para correos electrónicos de recuperación de contraseña';
  $loc_opts_allow_profile_edit_pswd = 'Permitir a los usuarios cambiar la contraseña personal';
  $loc_opts_allow_profile_edit_iprange = 'Permitir que los usuarios cambien la IP autopermitida';
  $loc_opts_allow_profile_edit_email = 'Permitir que los usuarios cambien su propio correo electrónico';
  $loc_opts_allow_profile_edit_comment = 'Permitir a los usuarios cambiar auto comentario';
  $loc_opts_allow_passwd_email_recovery = 'Permitir la recuperación de contraseñas por correo electrónico';
  $loc_opts_email_recovery_use_captcha = 'Requerir captcha en la recuperación de contraseña por correo electrónico';

  $loc_reports_presence_in_office = 'Informe de presencia';
  $loc_reports_presence_in_office_per_days = 'Informe de presencia por días';
  $loc_reports_outage_in_office = 'Informe de ausencia';
  $loc_reports_inactive_keys = 'Informe de claves inactivas';
  $loc_reports_alarm_events = 'Informe de eventos de alarma';

  $loc_menu_element_controllers = 'Controladores';
  $loc_menu_element_keys = 'Claves';
  $loc_menu_element_offices = 'Oficinas';
  $loc_menu_element_logins = 'Inicios de sesión';
  $loc_menu_element_badkeys = 'Malas claves';
  $loc_menu_element_proxy_events = 'Apoderado';
  $loc_menu_element_options = 'Opciones';
  $loc_menu_element_converter = 'Convertidor';
  $loc_menu_element_profile = 'Tu perfil';
  $loc_menu_element_reports = 'Informes';

  $loc_susbys_greeting = 'Bienvenido !';
  $loc_susbys_email_pswd_recovery = 'recuperación de contraseña por correo electrónico';
  $loc_susbys_email_pswd_recovery_mail_body1 = 'Si no solicita la recuperación de contraseña, simplemente elimine este correo electrónico.';
  $loc_susbys_email_pswd_recovery_mail_body2 = '¡Nunca revele, comparta o envíe sus contraseñas y enlaces de correos electrónicos de recuperación de contraseñas!<Br/>
        Enlaces de recuperación de contraseña válidos solo hasta el día actual (veinticuatro horas) y la misma dirección IP. La autenticación de dos factores no se restablecerá';
  $loc_susbys_email_pswd_recovery_ok_inform = 'ESTÁ BIEN. Revise su correo electrónico y haga clic en el enlace del correo electrónico para restablecer la contraseña';
  $loc_susbys_email_pswd_recovery_bad_hash = 'El enlace ha caducado o se ha utilizado antes.';
  $loc_susbys_list_events = 'Lista de eventos';
  $loc_susbys_list_queue = 'Cola de comandos';
  $loc_susbys_controllers_autoreg = 'Registro automático de controladores';
  $loc_susbys_controllers_need_name = '⚠ Asignar nombre al controlador ⚠';
  $loc_susbys_src_ip_bind = 'vinculación de IP';
  $loc_susbys_src_ip_bind_help = 'En el cambio de dirección IP, debe editar la IP en la base de datos o agregar subredes a la lista blanca.';
  $loc_susbys_src_ip_bind_help2 = 'Cualquier IP puede enviar los datos del controlador recién agregado.';
  $loc_susbys_src_ip_bind_help3 = 'Los nuevos controladores solo se pueden agregar manualmente.';
  $loc_susbys_controllers_no_data = 'NO hay datos de controladores activos. Al menos un controlador debe enviar datos.';
  $loc_susbys_addkeys_help1 = 'Si no se encontró la clave en la base de datos local, se agregará.<Br />Puede registrar la clave solo en el controlador registrado (por número de serie)';
  $loc_susbys_addkeys_tzhelp1 = 'Ejemplo TZ (máscara de bits de regiones de tiempo)';
  $loc_susbys_delkeys_help1 = 'Eliminar de la base de datos y TODOS los controladores';
  $loc_susbys_delkeys_help2 = 'Los controladores apagados o inactivos durante mucho tiempo pueden evitar la eliminación de claves';
  $loc_susbys_logins_help1 = 'Los inicios de sesión son solo para tareas de administración. No afecte el funcionamiento del controlador.';
  $loc_susbys_badkeys_help1 = 'Las claves malas o comprometidas no se pueden agregar a los controladores';
  $loc_susbys_confirm_mail_send = 'Confirme el envío de correo electrónico con el código.';
  $loc_susbys_mail_sending = 'Envío de correo electrónico ...';
  $loc_susbys_2fa_method = 'Método';
  $loc_susbys_2fa_sign = 'Firma';
  $loc_susbys_2fa_wallet = 'Billetera';
  $loc_susbys_2fa_shared_secret = 'Secreto compartido';
  $loc_susbys_2fa_test_string = 'Cadena de prueba';
  $loc_common_2fa_confirm = 'Confirmación';
  $loc_susbys_2fa_help1 = 'Use la billetera de criptomonedas Legacy (P2PKH) o Script (P2SH) ... o cualquier billetera de criptomonedas compatible con BitcoinECDSA <Br>
                    Electrum: make_seed(seed_type="standard") , frase de importación<Br/>Firmar cadena de prueba y enviar firma separada<Br/>
                    Se requerirá su billetera para la autenticación.';
  $loc_susbys_2fa_help2 = 'firma una cadena de prueba con tu billetera';
  $loc_susbys_open_door = 'Abre la puerta';

  $loc_common_phrase_not_accessible = 'Inaccesible';
  $loc_common_phrase_ip_not_allowed = 'esta IP no permitida';
  $loc_common_phrase_disabled_global_options = 'Deshabilitado en opciones globales';
  $loc_common_phrase_disabled_user_profile = 'inaccesible para el perfil de usuario';
  $loc_common_phrase_no_datarecords = 'Sin datos. Agregue al menos un registro.';
  $loc_common_phrase_on = 'EN';
  $loc_common_phrase_off = 'apagado';
  $loc_common_phrase_sn = 'número de serie';
  $loc_common_phrase_hw = 'equipo';
  $loc_common_phrase_2fa = 'Autenticación de dos factores';
  $loc_common_phrase_type = 'Escribe';
  $loc_common_phrase_activity = 'Actividad';

  $loc_entity_name_controller = 'controlador';
  $loc_entity_name_key = 'clave';
  $loc_entity_name_office = 'oficina';
  $loc_entity_name_username = 'Nombre de usuario';
  $loc_entity_name_statistic = 'Estadística';
  $loc_entity_name_badkey = 'clave mala';
  $loc_entity_name_proxyevent = 'evento proxy';
  $loc_entity_name_event = 'evento';
  $loc_entity_name_button = 'botón';
  $loc_entity_name_network = 'la red';

  $loc_property_name_access = 'Acceso';
  $loc_property_name_reject = 'Rechazar';
  $loc_property_name_created = 'Creado';
  $loc_property_name_last_activity = 'Última actividad';
  $loc_property_name_name = 'Nombre';
  $loc_property_name_address = 'Dirección';
  $loc_property_name_enable = 'EN';
  $loc_property_name_accessrights = 'Derechos de acceso';
  $loc_property_name_description = 'Descripción';
  $loc_property_name_code = 'Código';
  $loc_property_name_url = 'URL de destino';
  $loc_property_name_ipsubnets = 'subredes IP';
  $loc_property_name_time = 'tiempo';
  $loc_property_name_command = 'Dominio';
  $loc_property_name_executed = 'Ejecutado';
  $loc_property_name_executor = 'subsistema';

  $loc_common_phrase_add = 'Agregar';
  $loc_common_phrase_save = 'Ahorrar';
  $loc_common_phrase_enroll = 'Inscribirse';
  $loc_common_phrase_edit = 'Editar';
  $loc_common_phrase_manage = 'Administrar';
  $loc_common_phrase_del = 'Borrar';
  $loc_common_phrase_login = 'Iniciar sesión';
  $loc_common_phrase_logout = 'Desconectar';
  $loc_common_phrase_send = 'Enviar';
  $loc_common_phrase_username = 'Acceso';
  $loc_common_phrase_password = 'Clave';
  $loc_common_phrase_email_address = 'Email';
  $loc_common_phrase_not_active = 'No activo';
  $loc_common_phrase_always = 'Siempre';
  $loc_common_phrase_never = 'Nunca';
  $loc_common_phrase_help = 'ayuda';

  $loc_common_phrase_must_be_filled = 'debe ser llenado';
  $loc_common_phrase_not_found = 'extraviado';
  $loc_common_phrase_error = 'Error';

?>

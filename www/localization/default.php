<?php
       /* Nano-SKUD Default Localization strings (RUSSIAN) */
  /* Varialbles with strings for localization; Russian by default; */

  $loc_user_agent_admin_redirect = 'Обнаружен клиентский браузер <Br/><B>%s</B><Br/> - редирект в админку ...';
  $loc_options_parameter = 'Параметр';
  $loc_options_value = 'Значение';
  $loc_options_description = 'Описание';
  $loc_options_checkbox_hint = 'Поставьте галку, чтобы включить опцию';
  $loc_options_numberinput_hint = 'Введитте числовое значение';
  $loc_button_save_changes = 'Сохранить настройки';
  
  $loc_opts_show_anonym_stat = 'Показывать анонимам статистику';
  $loc_opts_ua_regexp_root_redirect = 'Перенаправлять в админку браузеры (регулярное выражение)';
  $loc_opts_restrict_open_door_ips = 'Разрешать удалённо открывать двери только с этих IP';
  $loc_opts_restrict_manage_keys_ips = 'Разрешать редактирование ключей только с этих IP';
  $loc_opts_restrict_enroll_keys_ips = 'Разрешать добавлять ключи в контроллеры только с этих IP';
  $loc_opts_restrict_anonim_view_ips = 'Разрешать показ анонимной статистики только этим IP';
  $loc_opts_allow_autoreg_controllers = 'Автоматически регистрировать новые контроллеры в базе';
  $loc_opts_allow_autoreg_auto_ip_filt = 'При авторегистрации контроллеров сразу привязывать их к IP';
  $loc_opts_hardware_z5r_interval = 'Интервал опроса контроллеров Z5R-Web';
  $loc_opts_global_sysname = 'Название системы';
  $loc_opts_email_recovery_from = 'Адрес отправителя для писем восстановления пароля';
  $loc_opts_allow_profile_edit_pswd = 'Разрешать пользователям менять свой пароль';
  $loc_opts_allow_profile_edit_iprange = 'Разрешать пользователям менять свой диапазон доступных IP';
  $loc_opts_allow_profile_edit_email = 'Разрешать пользователям менять свой email';
  $loc_opts_allow_profile_edit_comment = 'Разрешать пользователям менять комментарий';
  $loc_opts_allow_passwd_email_recovery = 'Включить возможность восстановления паролей по email';
  $loc_opts_email_recovery_use_captcha = 'Требовать капчу при восстановления паролей по email';

  $loc_reports_presence_in_office = 'Отчёт по присутствию';
  $loc_reports_outage_in_office = 'Отчёт по отсутствию';
  $loc_reports_inactive_keys = 'Отчёт по неактивным ключам';
  $loc_reports_alarm_events = 'Отчёт по тревожным событиям';

  $loc_menu_element_controllers = 'Контроллеры';
  $loc_menu_element_keys = 'Ключи';
  $loc_menu_element_offices = 'Офисы';
  $loc_menu_element_logins = 'Логины';
  $loc_menu_element_badkeys = 'Говноключи';
  $loc_menu_element_proxy_events = 'Прокси';
  $loc_menu_element_options = 'Настройки';
  $loc_menu_element_converter = 'Конвертер';
  $loc_menu_element_profile = 'Ваш профиль';
  $loc_menu_element_reports = 'Отчёты';

  $loc_susbys_greeting = 'Добро пожаловать !';
  $loc_susbys_email_pswd_recovery = 'Восстановление пароля по E-Mail';
  $loc_susbys_email_pswd_recovery_mail_body1 = 'Если вы не запрашивали сброс пароля, просто удалите это письмо.';
  $loc_susbys_email_pswd_recovery_mail_body2 = 'В любом случае, никому не сообщайте свои пароли и не передавайте никому ссылки, сгенерированные системой восстановления доступа !<Br/>
        Ссылки сброса пароля валидны только в пределах суток выдачи и только для того же IP адреса. Двухфакторная аутентификация не сбрасывается.';
  $loc_susbys_email_pswd_recovery_ok_inform = 'Готово. Проверьте почту и откройте ссылку в письме для сброса пароля';
  $loc_susbys_email_pswd_recovery_bad_hash = 'Ссылка устарела или уже была использована ранее';
  $loc_susbys_list_events = 'Список событий';
  $loc_susbys_list_queue = 'Очередь команд';
  $loc_susbys_controllers_autoreg = 'Авторегистрация контроллеров';
  $loc_susbys_controllers_need_name = '⚠ Назначьте имя контроллеру ⚠';
  $loc_susbys_src_ip_bind = 'привязка к IP-адресу';
  $loc_susbys_src_ip_bind_help = 'При смене IP-адреса потребуется поправить IP в базе или прописать разрешённые подсети.';
  $loc_susbys_src_ip_bind_help2 = 'Любой IP может отправить данные о вновь добавленном контроллере.';
  $loc_susbys_src_ip_bind_help3 = 'Новые устройства можно добавить только вручную.';
  $loc_susbys_controllers_no_data = 'Нет данных об активных контроллерах. Хотя бы один контроллер должен сперва отправить данные.';
  $loc_susbys_addkeys_help1 = 'Если ключа нет в локальной базе - он будет добавлен.<Br />Накатить ключ можно только на контроллер, серийник которого есть в базе.';
  $loc_susbys_addkeys_tzhelp1 = 'Пример значений TZ (битовой маски временных зон)';
  $loc_susbys_delkeys_help1 = 'Удалить из БД и ВСЕХ контроллеров';
  $loc_susbys_delkeys_help2 = 'Выключенные или давно не активные контроллеры могут не принять команды удаления';
  $loc_susbys_logins_help1 = 'Логины нужны только для управления, непосредственно на работу контроллеров не влияют.';
  $loc_susbys_badkeys_help1 = 'Плохие, скомпрометированные ключи не могут быть добавлены в контроллеры.';
  $loc_susbys_confirm_mail_send = 'Подтвердите отправку письма с кодом.';
  $loc_susbys_mail_sending = 'Отправляем письмо ...';
  $loc_susbys_2fa_method = 'Метод';
  $loc_susbys_2fa_sign = 'Подпись';
  $loc_susbys_2fa_wallet = 'Кошелёк';
  $loc_susbys_2fa_shared_secret = 'Общий секрет';
  $loc_susbys_2fa_test_string = 'Тестовая строка';
  $loc_common_2fa_confirm = 'Подтверждение';
  $loc_susbys_2fa_help1 = 'Используйте классические Legacy (P2PKH) или Script (P2SH) кошельки ... или любые BitcoinECDSA-совместимые крипто-кошельки<Br>
                    Для Electrum: команда в консоли make_seed(seed_type="standard")   , импортируйте фразу<Br/>Подпишите тестовую строку и отправьте подпись<Br/>Ваш кошелёк будет требоваться для аутентификации.';
  $loc_susbys_2fa_help2 = 'Подпишите тестовую строку вашим крипто-кошельком';
  $loc_susbys_open_door = 'Открытие двери';

  $loc_common_phrase_not_accessible = 'не доступно';
  $loc_common_phrase_ip_not_allowed = 'этот IP не разрешён';
  $loc_common_phrase_disabled_global_options = 'выключено в глобальных настройках';
  $loc_common_phrase_disabled_user_profile = 'недоступно для данного пользователя';
  $loc_common_phrase_no_datarecords = 'нет данных. Добавьте хотя бы одну запись';
  $loc_common_phrase_on = 'включена';
  $loc_common_phrase_off = 'выключена';
  $loc_common_phrase_sn = 'SN';
  $loc_common_phrase_hw = 'HW';
  $loc_common_phrase_2fa = '2FA';
  $loc_common_phrase_type = 'Тип';
  $loc_common_phrase_activity = 'Активность';

  $loc_entity_name_controller = 'контроллер';
  $loc_entity_name_key = 'ключ';
  $loc_entity_name_office = 'офис';
  $loc_entity_name_username = 'Имя пользователя';
  $loc_entity_name_statistic = 'Статистика';
  $loc_entity_name_badkey = 'говно-ключ';
  $loc_entity_name_proxyevent = 'прокси-событие';
  $loc_entity_name_event = 'событие';
  $loc_entity_name_button = 'кнопка';
  $loc_entity_name_network = 'сеть';

  $loc_property_name_access = 'Доступ';
  $loc_property_name_reject = 'Отказ';
  $loc_property_name_created = 'Создан';
  $loc_property_name_last_activity = 'Последняя активность';
  $loc_property_name_name = 'Название';
  $loc_property_name_address = 'Адрес';
  $loc_property_name_enable = 'ВКЛ';
  $loc_property_name_accessrights = 'Права';
  $loc_property_name_description = 'Описание';
  $loc_property_name_code = 'Код';
  $loc_property_name_url = 'Целевой адрес';
  $loc_property_name_ipsubnets = 'IP-подсети';
  $loc_property_name_time = 'время';
  $loc_property_name_command = 'команда';
  $loc_property_name_executed = 'Выполнено';
  $loc_property_name_executor = 'казнилка';

  $loc_common_phrase_add = 'Добавить';
  $loc_common_phrase_save = 'Сохранить';
  $loc_common_phrase_enroll = 'Накатить';
  $loc_common_phrase_edit = 'Редактировать';
  $loc_common_phrase_manage = 'Управление';
  $loc_common_phrase_del = 'Удалить';
  $loc_common_phrase_login = 'Войти';
  $loc_common_phrase_logout = 'Выйти';
  $loc_common_phrase_send = 'Отправить';
  $loc_common_phrase_username = 'Логин';
  $loc_common_phrase_password = 'Пароль';
  $loc_common_phrase_email_address = 'Почта';
  $loc_common_phrase_not_active = 'НЕ АКТИВЕН';
  $loc_common_phrase_always = 'Всегда';
  $loc_common_phrase_never = 'Никогда';

  $loc_common_phrase_must_be_filled = 'должны быть заполнены';
  $loc_common_phrase_not_found = 'не найдены';
  $loc_common_phrase_error = 'Ошибка';

  /* If localization != RU - overwrite localization variables */
  if ( $localization != '' and $localization != 'ru' ) {
      include ("$localization.php");
  }

?>

<?php
       /* Nano-SKUD KOREAN Localization strings */
  /* Varialbles for replacing; 대체 변수; $localization = 'kr' 설정; 적용을 위한 구성에서 */

  $loc_user_agent_admin_redirect = '클라이언트의 브라우저 <Br/><B>%s</B><Br/> 감지됨 - 관리 영역으로 리디렉션 ...';
  $loc_options_parameter = '옵션';
  $loc_options_value = '값';
  $loc_options_description = '설명';
  $loc_options_checkbox_hint = '활성화하려면 확인';
  $loc_options_numberinput_hint = '숫자 값 입력';
  $loc_button_save_changes = '설정 저장';

  $loc_opts_show_anonym_stat = '익명에게 통계 표시';
  $loc_opts_ua_regexp_root_redirect = '클라이언트의 브라우저를 관리 영역(RegExp)으로 리디렉션';
  $loc_opts_restrict_open_door_ips = '이 IP에서만 원격 도어 열기 허용';
  $loc_opts_restrict_manage_keys_ips = '이 IP의 편집 키만 허용';
  $loc_opts_restrict_enroll_keys_ips = '이 IP의 등록 키만 허용';
  $loc_opts_restrict_anonim_view_ips = '이 IP에만 익명 통계 표시 허용';
  $loc_opts_allow_autoreg_controllers = '데이터베이스에 새 컨트롤러 자동 등록';
  $loc_opts_allow_autoreg_auto_ip_filt = '컨트롤러에서 자동 등록은 이것을 IP에 바인딩합니다.';
  $loc_opts_hardware_z5r_interval = 'Z5R-Web의 폴링 간격';
  $loc_opts_global_sysname = '시스템 이름';
  $loc_opts_email_recovery_from = '비밀번호 복구 이메일 주소 발신자';
  $loc_opts_allow_profile_edit_pswd = '사용자가 자신의 암호를 변경하도록 허용';
  $loc_opts_allow_profile_edit_iprange = '사용자가 자체 허용 IP 변경 허용';
  $loc_opts_allow_profile_edit_email = '사용자가 자신의 이메일을 변경하도록 허용';
  $loc_opts_allow_profile_edit_comment = '사용자가 자신의 댓글을 변경할 수 있도록 허용';
  $loc_opts_allow_passwd_email_recovery = '이메일을 통한 비밀번호 복구 허용';
  $loc_opts_email_recovery_use_captcha = '이메일을 통한 비밀번호 복구 시 보안 문자 요구';

  $loc_reports_presence_in_office = '존재 보고서';
  $loc_reports_presence_in_office_per_days = '일별 존재 보고서';
  $loc_reports_outage_in_office = '부재 보고';
  $loc_reports_inactive_keys = '비활성 키 보고서';
  $loc_reports_alarm_events = '알람 이벤트 보고서';

  $loc_menu_element_controllers = '컨트롤러';
  $loc_menu_element_keys = '열쇠';
  $loc_menu_element_offices = '진력';
  $loc_menu_element_logins = '로그인';
  $loc_menu_element_badkeys = '잘못된 키';
  $loc_menu_element_proxy_events = '대리';
  $loc_menu_element_options = '옵션';
  $loc_menu_element_converter = '변환기';
  $loc_menu_element_profile = '프로필';
  $loc_menu_element_reports = '보고서';

  $loc_susbys_greeting = '어서 오십시오 !';
  $loc_susbys_email_pswd_recovery = '이메일을 통한 비밀번호 복구';
  $loc_susbys_email_pswd_recovery_mail_body1 = '비밀번호 복구를 요청하지 않으려면 이 이메일을 삭제하세요.';
  $loc_susbys_email_pswd_recovery_mail_body2 = '비밀번호 복구 이메일에서 비밀번호와 링크를 공개, 공유 또는 제출하지 마십시오!<Br/>
        비밀번호 복구 링크는 현재 날짜(24시간) 및 동일한 IP 주소까지만 유효합니다. 이중 인증이 재설정되지 않습니다.';
  $loc_susbys_email_pswd_recovery_ok_inform = '확인. 이메일을 확인하고 이메일의 링크를 클릭하여 비밀번호 재설정';
  $loc_susbys_email_pswd_recovery_bad_hash = '링크가 만료되었거나 이전에 사용되었습니다.';
  $loc_susbys_list_events = '이벤트 목록';
  $loc_susbys_list_queue = '명령 대기열';
  $loc_susbys_controllers_autoreg = '컨트롤러 자동 등록';
  $loc_susbys_controllers_need_name = '⚠ 컨트롤러에 이름 할당 ⚠';
  $loc_susbys_src_ip_bind = 'IP 바인딩';
  $loc_susbys_src_ip_bind_help = 'IP 주소 변경 시 데이터베이스의 IP를 편집하거나 서브넷을 화이트리스트에 추가해야 합니다.';
  $loc_susbys_src_ip_bind_help2 = '모든 IP는 새로 추가된 컨트롤러의 데이터를 보낼 수 있습니다.';
  $loc_susbys_src_ip_bind_help3 = '새 컨트롤러는 수동으로만 추가할 수 있습니다.';
  $loc_susbys_controllers_no_data = '활성 컨트롤러 데이터가 없습니다. 최소한 하나의 컨트롤러가 데이터를 보내야 합니다.';
  $loc_susbys_addkeys_help1 = '로컬 데이터베이스에 키가 없으면 추가됩니다.<Br />등록된 컨트롤러에만 키를 등록할 수 있습니다(일련 번호로).';
  $loc_susbys_addkeys_tzhelp1 = '예제 TZ(시간 영역 비트마스크)';
  $loc_susbys_delkeys_help1 = '데이터베이스 및 모든 컨트롤러에서 삭제';
  $loc_susbys_delkeys_help2 = '전원이 꺼져 있거나 오랫동안 비활성화된 컨트롤러는 키 삭제를 방지할 수 있습니다.';
  $loc_susbys_logins_help1 = '로그인은 관리 작업 전용입니다. 컨트롤러의 작동에 영향을 주지 마십시오.';
  $loc_susbys_badkeys_help1 = '잘못되었거나 손상된 키는 컨트롤러에 추가할 수 없습니다.';
  $loc_susbys_confirm_mail_send = '코드가 포함된 이메일 전송을 확인합니다.';
  $loc_susbys_mail_sending = '이메일 보내기 ...';
  $loc_susbys_2fa_method = '방법';
  $loc_susbys_2fa_sign = '서명';
  $loc_susbys_2fa_wallet = '지갑';
  $loc_susbys_2fa_shared_secret = '공유 비밀';
  $loc_susbys_2fa_test_string = '테스트 문자열';
  $loc_common_2fa_confirm = '확인';
  $loc_susbys_2fa_help1 = '레거시(P2PKH) 또는 스크립트(P2SH) 암호화폐 지갑 또는 모든 BitcoinECDSA 호환 암호화폐 지갑 사용<Br>
                    Electrum: make_seed(seed_type="standard") , import 구문<Br/>테스트 문자열에 서명하고 분리된 서명 보내기<Br/>
                    인증을 위해 지갑이 필요합니다.';
  $loc_susbys_2fa_help2 = '지갑으로 테스트 문자열 서명';
  $loc_susbys_open_door = '문을 열어';

  $loc_common_phrase_not_accessible = '접근 불가';
  $loc_common_phrase_ip_not_allowed = '이 IP는 허용되지 않습니다';
  $loc_common_phrase_disabled_global_options = '전역 옵션에서 비활성화';
  $loc_common_phrase_disabled_user_profile = '사용자가 액세스할 수 없음';
  $loc_common_phrase_no_datarecords = '데이터가 없습니다. 하나 이상의 레코드를 추가하십시오.';
  $loc_common_phrase_on = '활성화';
  $loc_common_phrase_off = '끄다';
  $loc_common_phrase_sn = '일련 번호';
  $loc_common_phrase_hw = '장비';
  $loc_common_phrase_2fa = '이중 인증';
  $loc_common_phrase_type = '유형';
  $loc_common_phrase_activity = '활동';

  $loc_entity_name_controller = '제어 장치';
  $loc_entity_name_key = '열쇠';
  $loc_entity_name_office = '사무실';
  $loc_entity_name_username = '사용자 이름';
  $loc_entity_name_statistic = '통계량';
  $loc_entity_name_badkey = '잘못된 키';
  $loc_entity_name_proxyevent = '프록시 이벤트';
  $loc_entity_name_event = '이벤트';
  $loc_entity_name_button = '단추';
  $loc_entity_name_network = '회로망';

  $loc_property_name_access = '입장';
  $loc_property_name_reject = '거부하다';
  $loc_property_name_created = '만들어진';
  $loc_property_name_last_activity = '마지막 활동';
  $loc_property_name_name = '이름';
  $loc_property_name_address = '주소';
  $loc_property_name_enable = '활성화';
  $loc_property_name_accessrights = '액세스 권한';
  $loc_property_name_description = '설명';
  $loc_property_name_code = '암호';
  $loc_property_name_url = '대상 URL';
  $loc_property_name_ipsubnets = 'IP 서브넷';
  $loc_property_name_time = '시각';
  $loc_property_name_command = '명령';
  $loc_property_name_executed = '실행됨';
  $loc_property_name_executor = '실행 하위 시스템';

  $loc_common_phrase_add = '추가하다';
  $loc_common_phrase_save = '구하다';
  $loc_common_phrase_enroll = '등록';
  $loc_common_phrase_edit = '편집하다';
  $loc_common_phrase_manage = '관리하다';
  $loc_common_phrase_del = '삭제';
  $loc_common_phrase_login = '로그인';
  $loc_common_phrase_logout = '로그아웃';
  $loc_common_phrase_send = '보내다';
  $loc_common_phrase_username = '로그인';
  $loc_common_phrase_password = '비밀번호';
  $loc_common_phrase_email_address = '이메일';
  $loc_common_phrase_not_active = '활성화되지 않은';
  $loc_common_phrase_always = '언제나';
  $loc_common_phrase_never = '절대';
  $loc_common_phrase_help = '돕다';

  $loc_common_phrase_must_be_filled = '채워야 한다';
  $loc_common_phrase_not_found = '찾을 수 없음';
  $loc_common_phrase_error = '오류';

?>

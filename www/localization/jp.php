<?php
       /* Nano-SKUD JAPAN Localization strings (MACHINE TRANSLATION) */
  /* Varialbles for replacing; Set $localization = 'jp'; ローカライズのための文字列を含む変数 ; */

  $loc_user_agent_admin_redirect = 'クライアントのブラウザ <Br/><B>%s</B><Br/> が検出されました - 管理領域にリダイレクトします ...';
  $loc_options_parameter = 'オプション';
  $loc_options_value = '値';
  $loc_options_description = '説明';
  $loc_options_checkbox_hint = '有効化オプションにマークを設定';
  $loc_options_numberinput_hint = '入力数値';
  $loc_button_save_changes = '設定を保存する';

  $loc_opts_show_anonym_stat = '統計を匿名に表示';
  $loc_opts_ua_regexp_root_redirect = 'クライアントのブラウザを管理領域 (正規表現) にリダイレクトします';
  $loc_opts_restrict_open_door_ips = 'この IP からのみリモート ドアを開くことを許可する';
  $loc_opts_restrict_manage_keys_ips = 'この IP からの編集キーのみを許可する';
  $loc_opts_restrict_enroll_keys_ips = 'この IP からの登録キーのみを許可する';
  $loc_opts_restrict_anonim_view_ips = 'この IP に対してのみ匿名統計の表示を許可する';
  $loc_opts_allow_autoreg_controllers = '新しいコントローラをデータベースに自動登録';
  $loc_opts_allow_autoreg_auto_ip_filt = 'コントローラーの自動登録で、これを IP にバインドします';
  $loc_opts_hardware_z5r_interval = 'Z5R-Web のポーリング間隔';
  $loc_opts_global_sysname = 'システム名';
  $loc_opts_email_recovery_from = 'パスワード回復メールの送信元アドレス';
  $loc_opts_allow_profile_edit_pswd = 'ユーザーが自分のパスワードを変更できるようにする';
  $loc_opts_allow_profile_edit_iprange = 'ユーザーが自己許可 IP を変更できるようにする';
  $loc_opts_allow_profile_edit_email = 'ユーザーが自分の電子メールを変更できるようにする';
  $loc_opts_allow_profile_edit_comment = 'ユーザーが自己コメントを変更できるようにする';
  $loc_opts_allow_passwd_email_recovery = 'メールによるパスワードの回復を許可する';
  $loc_opts_email_recovery_use_captcha = '電子メールによるパスワード回復でキャプチャを要求する';

  $loc_reports_presence_in_office = 'プレゼンスレポート';
  $loc_reports_presence_in_office_per_days = '1 日あたりのプレゼンス レポート';
  $loc_reports_outage_in_office = '欠勤届';
  $loc_reports_inactive_keys = '非アクティブ キー レポート';
  $loc_reports_alarm_events = 'アラーム イベント レポート';

  $loc_menu_element_controllers = 'コントローラー';
  $loc_menu_element_keys = '鍵';
  $loc_menu_element_offices = '事務所';
  $loc_menu_element_logins = 'ログイン';
  $loc_menu_element_badkeys = '不良キー';
  $loc_menu_element_proxy_events = '代理人';
  $loc_menu_element_options = 'オプション';
  $loc_menu_element_converter = '変換器';
  $loc_menu_element_profile = '概要';
  $loc_menu_element_reports = '報告';

  $loc_susbys_greeting = 'いらっしゃいませ ！';
  $loc_susbys_email_pswd_recovery = '電子メールによるパスワード回復';
  $loc_susbys_email_pswd_recovery_mail_body1 = 'パスワードの回復を要求しない場合は、このメールを削除してください。';
  $loc_susbys_email_pswd_recovery_mail_body2 = 'パスワード回復メールからパスワードやリンクを開示、共有、送信しないでください。<Br/>
        パスワード回復リンクは、当日 (24 時間) および同じ IP アドレスでのみ有効です。 二要素認証がリセットされない';
  $loc_susbys_email_pswd_recovery_ok_inform = 'わかった。 メールを確認し、メール内のリンクをクリックしてパスワードをリセットします';
  $loc_susbys_email_pswd_recovery_bad_hash = 'リンクの有効期限が切れているか、以前に使用されています。';
  $loc_susbys_list_events = 'イベントのリスト';
  $loc_susbys_list_queue = 'コマンド キュー';
  $loc_susbys_controllers_autoreg = 'コントローラーの自動登録';
  $loc_susbys_controllers_need_name = '⚠ コントローラーに名前を割り当てる ⚠';
  $loc_susbys_src_ip_bind = 'IPバインディング';
  $loc_susbys_src_ip_bind_help = 'IP アドレスの変更では、データベースで IP を編集するか、ホワイトリスト サブネットを追加する必要があります。';
  $loc_susbys_src_ip_bind_help2 = 'どの IP でも、新しく追加されたコントローラーのデータを送信できます。';
  $loc_susbys_src_ip_bind_help3 = '新しいコントローラは手動でのみ追加できます。';
  $loc_susbys_controllers_no_data = 'アクティブなコントローラ データがありません。 少なくとも 1 つのコントローラーがデータを送信する必要があります。';
  $loc_susbys_addkeys_help1 = 'キーがローカル データベースに見つからない場合は、追加されます。<Br />登録済みのコントローラにのみキーを登録できます (シリアル番号による)。';
  $loc_susbys_addkeys_tzhelp1 = '例 TZ (時間領域のビットマスク)';
  $loc_susbys_delkeys_help1 = 'データベースとすべてのコントローラから削除';
  $loc_susbys_delkeys_help2 = '電源がオフになっている、または長時間非アクティブなコントローラーは、キーの削除を回避できます';
  $loc_susbys_logins_help1 = 'ログインは管理タスク専用です。 コントローラの動作に影響を与えないでください。';
  $loc_susbys_badkeys_help1 = '不良または侵害されたキーをコントローラに追加できない';
  $loc_susbys_confirm_mail_send = 'コード付きメールの送信を確認します。';
  $loc_susbys_mail_sending = 'メールを送信中 ...';
  $loc_susbys_2fa_method = '方法';
  $loc_susbys_2fa_sign = 'サイン';
  $loc_susbys_2fa_wallet = '財布';
  $loc_susbys_2fa_shared_secret = '共有シークレット';
  $loc_susbys_2fa_test_string = 'テスト文字列';
  $loc_common_2fa_confirm = '確認';
  $loc_susbys_2fa_help1 = 'レガシー (P2PKH) またはスクリプト (P2SH) 暗号通貨ウォレットを使用...または任意の BitcoinECDSA 互換の暗号ウォレット<Br>
                    Electrum: make_seed(seed_type="standard") , import phrase<Br/>テスト文字列に署名し、切り離された署名を送信<Br/>
                    認証にはウォレットが必要です。';
  $loc_susbys_2fa_help2 = 'ウォレットでテスト文字列に署名する';
  $loc_susbys_open_door = 'ドアを開ける';

  $loc_common_phrase_not_accessible = '接続できない';
  $loc_common_phrase_ip_not_allowed = 'この IP は許可されていません';
  $loc_common_phrase_disabled_global_options = 'グローバルオプションで無効';
  $loc_common_phrase_disabled_user_profile = 'ユーザーはアクセスできません';
  $loc_common_phrase_no_datarecords = 'データなし。 少なくとも 1 つのレコードを追加します。';
  $loc_common_phrase_on = 'オン';
  $loc_common_phrase_off = 'オフ';
  $loc_common_phrase_sn = 'シリアル番号';
  $loc_common_phrase_hw = '装置';
  $loc_common_phrase_2fa = '二要素認証';
  $loc_common_phrase_type = '種類';
  $loc_common_phrase_activity = '活動';

  $loc_entity_name_controller = 'コントローラ';
  $loc_entity_name_key = '鍵';
  $loc_entity_name_office = '事務所';
  $loc_entity_name_username = 'ユーザー名';
  $loc_entity_name_statistic = '統計';
  $loc_entity_name_badkey = '不良キー';
  $loc_entity_name_proxyevent = '代理イベント';
  $loc_entity_name_event = 'イベント';
  $loc_entity_name_button = 'ボタン';
  $loc_entity_name_network = '通信網';

  $loc_property_name_access = 'アクセス';
  $loc_property_name_reject = '拒絶';
  $loc_property_name_created = '作成した';
  $loc_property_name_last_activity = '最後の活動';
  $loc_property_name_name = '名前';
  $loc_property_name_address = '住所';
  $loc_property_name_enable = 'オン';
  $loc_property_name_accessrights = 'アクセス権';
  $loc_property_name_description = '説明';
  $loc_property_name_code = 'コード';
  $loc_property_name_url = '対象URL';
  $loc_property_name_ipsubnets = 'IPサブネット';
  $loc_property_name_time = '時間';
  $loc_property_name_command = '指示';
  $loc_property_name_executed = '実行された';
  $loc_property_name_executor = '実行サブシステム';

  $loc_common_phrase_add = '追加';
  $loc_common_phrase_save = '保存する';
  $loc_common_phrase_enroll = '登録';
  $loc_common_phrase_edit = '編集';
  $loc_common_phrase_manage = '管理';
  $loc_common_phrase_del = '消去';
  $loc_common_phrase_login = 'ログイン';
  $loc_common_phrase_logout = 'サインアウト';
  $loc_common_phrase_send = '送信';
  $loc_common_phrase_username = 'ログイン';
  $loc_common_phrase_password = 'パスワード';
  $loc_common_phrase_email_address = 'Eメール';
  $loc_common_phrase_not_active = '非活動中';
  $loc_common_phrase_always = '常に';
  $loc_common_phrase_never = '決して';

  $loc_common_phrase_must_be_filled = '満たさなければならない';
  $loc_common_phrase_not_found = '見つかりません';
  $loc_common_phrase_error = 'エラー';

?>

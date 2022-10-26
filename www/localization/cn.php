<?php
       /* Nano-SKUD CHINESE Localization strings (MACHINE TRANSLATION) */
  /* Varialbles for replacing; Set $localization = 'cn'; in config for apply */

  $loc_user_agent_admin_redirect = '客户的浏览器 <Br/><B>%s</B><Br/> 检测到 - 重定向到管理区域 ...';
  $loc_options_parameter = '选项';
  $loc_options_value = '价值';
  $loc_options_description = '描述';
  $loc_options_checkbox_hint = '为启用选项设置标记';
  $loc_options_numberinput_hint = '输入编号值';
  $loc_button_save_changes = '保存设置';

  $loc_opts_show_anonym_stat = '向匿名者显示统计信息';
  $loc_opts_ua_regexp_root_redirect = '将客户端的浏览器重定向到管理区域（正则表达式）';
  $loc_opts_restrict_open_door_ips = '仅允许从此 IP 远程开门';
  $loc_opts_restrict_manage_keys_ips = '仅允许来自此 IP 的编辑密钥';
  $loc_opts_restrict_enroll_keys_ips = '仅允许从此 IP 注册密钥';
  $loc_opts_restrict_anonim_view_ips = '只允许向该 IP 显示匿名统计信息';
  $loc_opts_allow_autoreg_controllers = '自动在数据库中注册新控制器';
  $loc_opts_allow_autoreg_auto_ip_filt = '自动注册控制器时，将其绑定到 IP';
  $loc_opts_hardware_z5r_interval = 'Z5R-Web 的轮询间隔';
  $loc_opts_global_sysname = '系统名称';
  $loc_opts_email_recovery_from = '密码恢复电子邮件的地址发件人';
  $loc_opts_allow_profile_edit_pswd = '允许用户更改自己的密码';
  $loc_opts_allow_profile_edit_iprange = '允许用户更改自己允许的 IP';
  $loc_opts_allow_profile_edit_email = '允许用户更改自己的电子邮件';
  $loc_opts_allow_profile_edit_comment = '允许用户更改自我评论';
  $loc_opts_allow_passwd_email_recovery = '允许通过电子邮件恢复密码';
  $loc_opts_email_recovery_use_captcha = '通过电子邮件恢复密码时需要验证码';

  $loc_reports_presence_in_office = '存在报告';
  $loc_reports_presence_in_office_per_days = '每天的存在报告';
  $loc_reports_outage_in_office = '缺勤报告';
  $loc_reports_inactive_keys = '非活动密钥报告';
  $loc_reports_alarm_events = '报警事件上报';

  $loc_menu_element_controllers = '控制器';
  $loc_menu_element_keys = '钥匙';
  $loc_menu_element_offices = '办公室';
  $loc_menu_element_logins = '登录';
  $loc_menu_element_badkeys = '坏键';
  $loc_menu_element_proxy_events = '代理人';
  $loc_menu_element_options = '选项';
  $loc_menu_element_converter = '转换器';
  $loc_menu_element_profile = '你的个人资料';
  $loc_menu_element_reports = '报告';

  $loc_susbys_greeting = '欢迎！';
  $loc_susbys_email_pswd_recovery = '通过电子邮件恢复密码';
  $loc_susbys_email_pswd_recovery_mail_body1 = '如果您不要求恢复密码，请删除此电子邮件。';
  $loc_susbys_email_pswd_recovery_mail_body2 = '切勿从密码恢复电子邮件中披露、共享或提交您的密码和链接！<Br/>
        密码恢复链接仅在当天（二十四小时）和相同的 IP 地址有效。 两因素身份验证不会重置';
  $loc_susbys_email_pswd_recovery_ok_inform = '好的。 检查您的电子邮件并单击电子邮件中的链接以重置密码';
  $loc_susbys_email_pswd_recovery_bad_hash = '该链接已过期或以前被使用过。';
  $loc_susbys_list_events = '事件列表';
  $loc_susbys_list_queue = '命令队列';
  $loc_susbys_controllers_autoreg = '控制器自动注册';
  $loc_susbys_controllers_need_name = '⚠ 为控制器分配名称 ⚠';
  $loc_susbys_src_ip_bind = 'IP绑定';
  $loc_susbys_src_ip_bind_help = '在 IP 地址更改时，您必须在数据库中编辑 IP 或添加白名单子网。';
  $loc_susbys_src_ip_bind_help2 = '任何IP都可以发送新增控制器的数据。';
  $loc_susbys_src_ip_bind_help3 = '只能手动添加新控制器。';
  $loc_susbys_controllers_no_data = '没有活动的控制器数据。 至少一个控制器必须发送数据。';
  $loc_susbys_addkeys_help1 = '如果在本地数据库中找不到密钥 - 它将被添加。<Br />您只能将密钥注册到已注册的控制器（按序列号）';
  $loc_susbys_addkeys_tzhelp1 = '示例 TZ（时间区域位掩码）';
  $loc_susbys_delkeys_help1 = '从 数据库 和所有控制器中删除';
  $loc_susbys_delkeys_help2 = '从数据库和所有控制器中删除';
  $loc_susbys_logins_help1 = '登录仅用于管理任务。 不影响控制器的运行。';
  $loc_susbys_badkeys_help1 = '无法将错误或受损的密钥添加到控制器s';
  $loc_susbys_confirm_mail_send = '确认发送带有代码的电子邮件。';
  $loc_susbys_mail_sending = '发送电子邮件 ...';
  $loc_susbys_2fa_method = '方法';
  $loc_susbys_2fa_sign = '签字';
  $loc_susbys_2fa_wallet = '钱包';
  $loc_susbys_2fa_shared_secret = '共享秘密';
  $loc_susbys_2fa_test_string = '测试字符串';
  $loc_common_2fa_confirm = '确认';
  $loc_susbys_2fa_help1 = '使用 Legacy (P2PKH) 或 Script (P2SH) 加密货币钱包……或任何与 BitcoinECDSA 兼容的加密钱包<Br>
                    Electrum： make_seed(seed_type="standard") ，导入短语<Br/>签名测试字符串并发送分离签名<Br/>
                    您的钱包将需要进行身份验证。';
  $loc_susbys_2fa_help2 = '用你的钱包签名测试字符串';
  $loc_susbys_open_door = '打开门';

  $loc_common_phrase_not_accessible = '无法访问';
  $loc_common_phrase_ip_not_alllowed = '这个IP不允许';
  $loc_common_phrase_disabled_global_options = '在全局选项中禁用';
  $loc_common_phrase_disabled_user_profile = '此用户无法访问';
  $loc_common_phrase_no_datarecords = '没有数据。 添加至少一条记录。';
  $loc_common_phrase_on = '包括在内的';
  $loc_common_phrase_off = '关掉了';
  $loc_common_phrase_sn = '序列号';
  $loc_common_phrase_hw = '设备';
  $loc_common_phrase_2fa = '双重身份验证';
  $loc_common_phrase_type = '类型';
  $loc_common_phrase_activity = '活动';

  $loc_entity_name_controller = '控制器';
  $loc_entity_name_key = '钥匙';
  $loc_entity_name_office = '办公室';
  $loc_entity_name_username = '用户名';
  $loc_entity_name_statistic = '统计';
  $loc_entity_name_badkey = '坏键';
  $loc_entity_name_proxyevent = '代理事件';
  $loc_entity_name_event = '事件';
  $loc_entity_name_button = '按钮';
  $loc_entity_name_network = '网络';

  $loc_property_name_access = '使用权';
  $loc_property_name_reject = '拒绝';
  $loc_property_name_created = '已创建';
  $loc_property_name_last_activity = '上次活动';
  $loc_property_name_name = '姓名';
  $loc_property_name_address = '地址';
  $loc_property_name_enable = '包括';
  $loc_property_name_accessrights = '访问权';
  $loc_property_name_description = '描述';
  $loc_property_name_code = '代码';
  $loc_property_name_url = '目标网址';
  $loc_property_name_ipsubnets = 'IP子网';
  $loc_property_name_time = '时间';
  $loc_property_name_command = '命令';
  $loc_property_name_executed = '已执行';
  $loc_property_name_executor = '执行子系统';

  $loc_common_phrase_add = '添加';
  $loc_common_phrase_save = '节省';
  $loc_common_phrase_enroll = '注册';
  $loc_common_phrase_edit = '编辑';
  $loc_common_phrase_manage = '管理';
  $loc_common_phrase_del = '删除';
  $loc_common_phrase_login = '登入';
  $loc_common_phrase_logout = '登出';
  $loc_common_phrase_send = '发送';
  $loc_common_phrase_username = '用户登录';
  $loc_common_phrase_password = '密码';
  $loc_common_phrase_email_address = '电子邮件';
  $loc_common_phrase_not_active = '不活跃';
  $loc_common_phrase_always = '总是';
  $loc_common_phrase_never = '决不';

  $loc_common_phrase_must_be_filled = '必须填写';
  $loc_common_phrase_not_found = '未找到';
  $loc_common_phrase_error = '误差';

?>

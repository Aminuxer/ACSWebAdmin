-- Adminer 4.8.1 MySQL Z5RWeb ACS Admin

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `z5r` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `z5r`;

CREATE TABLE `bad_keys` (
  `card` varchar(17) NOT NULL DEFAULT '' COMMENT 'HEXX,DEC,DECCC keycard number',
  `card_hex` varchar(16) NOT NULL DEFAULT '' COMMENT 'Pure HEX card Long-number',
  `description` varchar(64) NOT NULL DEFAULT '' COMMENT 'description',
  `active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'is_used',
  PRIMARY KEY (`card`),
  UNIQUE KEY `card_hex` (`card_hex`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Universal domofon, widely-compromised, etc';

INSERT INTO `bad_keys` (`card`, `card_hex`, `description`, `active`) VALUES
('0000,000,00000',	'000000000000',	'zeros; Cyfral CCD-20',	1),
('0000,000,00061',	'010000000000003D',	'???-Cyfral CCD-20 - up to 70%',	1),
('0000,001,65535',	'00000001FFFF',	'Cyfral filtered',	1),
('0000,009,15588',	'000000093CE4A9',	'Eltis',	1),
('0000,144,06655',	'01000000009019FF',	'???-old domofomes',	1),
('0000,254,54355',	'000000FED453',	'Visit',	1),
('0001,255,65535',	'000001FFFFFFFF',	'Cyfral UKP-1',	1),
('000F,046,47222',	'00000F2EB876',	'domofon Forward',	1),
('0036,090,04416',	'0000365A1140BE',	'Metakom UKP-1',	1),
('00AA,017,48640',	'0000AA11BE00',	'Keynman',	1),
('00FF,255,65535',	'0000FFFFFFFF',	'Metakom - scaner master key',	1),
('1100,000,00119',	'01BE401100000077',	'widely compromised',	1),
('110A,000,00029',	'01BE40110A00001D',	'Vizit, some Keymans',	1),
('115A,054,00158',	'0FBE40115A36009E',	'',	1),
('115A,054,00225',	'01BE40115A3600E1',	'universal old-Vizit',	1),
('115A,086,00187',	'01BE40115A5600BB',	'widely compromised',	1),
('11AA,000,00251',	'0100BE11AA0000FB',	'???-KEYMAN',	1),
('255,65535',	'5,65,083,00005',	'2e32',	1),
('2E0F,000,00092',	'0176B82E0F00005C',	'???-Forward',	1),
('365A,017,16574',	'00365A1140BE',	'Visit',	1),
('365A,057,33097',	'00365A398149',	'Vizit UKP-2 series 3',	1),
('565A,017,16574',	'00565A1140BE',	'Master key from Scaner/advert-spree - domofoni',	1),
('888A,000,00077',	'016F2E888A00004D',	'?? compromised',	1),
('FE00,000,00111',	'0153D4FE0000006F',	'???-Vizit up to 99%',	1),
('FE00,000,32392',	'0153D4FE00007E88',	'???-CyfralMetakom',	1),
('FFFF,000,00155',	'01FFFFFFFF00009B',	'',	1),
('FFFF,255,65300',	'FFFFFFFFFFFFFF14',	'???-98%_Metkom_some_Cyfral',	1),
('FFFF,255,65535',	'FFFFFFFFFFFF',	'Metakom',	1);

CREATE TABLE `controller_names` (
  `sn` varchar(64) NOT NULL COMMENT 'serial number',
  `hw_type` varchar(64) NOT NULL COMMENT 'device type from event_codes.hw_type',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT 'name',
  `office_id` int(11) DEFAULT NULL COMMENT 'id office',
  `hardware_login` varchar(64) DEFAULT NULL COMMENT 'login from controller',
  `hardware_password_sha1` varchar(64) DEFAULT NULL COMMENT 'sha1(password from controller)',
  `allowed_ip_range` varchar(255) DEFAULT NULL COMMENT 'WhileList IP acl',
  PRIMARY KEY (`sn`,`hw_type`),
  KEY `office_id` (`office_id`),
  CONSTRAINT `controller_names_ibfk_1` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='contoller names and data';


CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_code` tinyint(1) NOT NULL COMMENT 'from event_codes.id',
  `src_ip` int(10) NOT NULL COMMENT 'INET_ATON ( IPv4 ) source address',
  `sn` varchar(64) NOT NULL COMMENT 'from controller_names.sn',
  `hw_type` varchar(64) NOT NULL COMMENT 'device type from event_codes.hw_type',
  `card_hex` varchar(12) NOT NULL,
  `card` varchar(17) NOT NULL,
  `ts` datetime NOT NULL,
  `flag` varchar(255) NOT NULL,
  `internal_id` bigint(8) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_code_sn_hw_type_card_ts_internal_id_flag` (`event_code`,`sn`,`hw_type`,`card`,`ts`,`internal_id`,`flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='events from controllers';


CREATE TABLE `event_codes` (
  `id` int(1) NOT NULL AUTO_INCREMENT COMMENT 'returned_code',
  `hw_type` varchar(64) NOT NULL COMMENT 'HW type',
  `name` varchar(32) NOT NULL DEFAULT '',
  `card_number_required` tinyint(1) NOT NULL DEFAULT 0,
  `severity_color` varchar(12) NOT NULL DEFAULT '0' COMMENT 'color for highlighting',
  PRIMARY KEY (`id`,`hw_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='help table with events codes descriptions';

INSERT INTO `event_codes` (`id`, `hw_type`, `name`, `card_number_required`, `severity_color`) VALUES
(0,	'Z5RWEB',	'Button internal (input)',	0,	'yellow'),
(1,	'Z5RWEB',	'Button internal (output)',	0,	'yellow'),
(2,	'Z5RWEB',	'key not found for input',	0,	'orange'),
(3,	'Z5RWEB',	'key not found for output',	0,	'orange'),
(4,	'Z5RWEB',	'key found, door open, for input',	1,	'#32CD32'),
(5,	'Z5RWEB',	'key found, door open, for output',	1,	'#90EE90'),
(6,	'Z5RWEB',	'access denied for input',	1,	'red'),
(7,	'Z5RWEB',	'access denied for output',	1,	'red'),
(8,	'Z5RWEB',	'open from network for input',	0,	'#6495ED'),
(9,	'Z5RWEB',	'open from network for output',	0,	'#6495ED'),
(10,	'Z5RWEB',	'key OK, lock for input',	1,	'orange'),
(11,	'Z5RWEB',	'key OK, lock for output',	1,	'orange'),
(12,	'Z5RWEB',	'Door hacked for input',	0,	'red'),
(13,	'Z5RWEB',	'Door hacked for output',	0,	'red'),
(14,	'Z5RWEB',	'Door stay open for input',	0,	'#CCCCFF'),
(15,	'Z5RWEB',	'Door stay open for output',	0,	'#CCCCFF'),
(16,	'Z5RWEB',	'Pass OK, input',	1,	'#40E0D0'),
(17,	'Z5RWEB',	'Pass OK, output',	1,	'#a3e4d7'),
(20,	'Z5RWEB',	'Controller rebooted',	0,	'cyan'),
(21,	'Z5RWEB',	'Power: 0 - off, 1 - on;',	0,	'cyan'),
(32,	'Z5RWEB',	'Door open for input',	1,	'#9FE2BF'),
(33,	'Z5RWEB',	'Door open for output',	1,	'#9FE2BF'),
(34,	'Z5RWEB',	'Door closed for input',	0,	'#CD5C5C'),
(35,	'Z5RWEB',	'Door closed for output',	0,	'#CD5C5C'),
(37,	'Z5RWEB',	'Change WORK-MODE',	0,	'cyan'),
(38,	'Z5RWEB',	'Fire-alarm event',	0,	'red'),
(39,	'Z5RWEB',	'Defence event',	0,	'Fuchsia'),
(40,	'Z5RWEB',	'Pass timeout input',	1,	'#FF7F50'),
(41,	'Z5RWEB',	'Pass timeout output',	1,	'#FF7F50'),
(48,	'Z5RWEB',	'Gate input',	1,	'#27ae60'),
(49,	'Z5RWEB',	'Gate output',	1,	'#27ae60'),
(50,	'Z5RWEB',	'Gate busy input',	1,	'#FFBF00'),
(51,	'Z5RWEB',	'Gate busy output',	1,	'#FFBF00'),
(52,	'Z5RWEB',	'Gate allow for input',	1,	'#82e0aa'),
(53,	'Z5RWEB',	'Gate allow for output',	1,	'#82e0aa'),
(54,	'Z5RWEB',	'pass locked for input',	1,	'orange'),
(55,	'Z5RWEB',	'pass locked for output',	1,	'orange');

CREATE TABLE `last_activity_keys` (
  `key` varchar(17) NOT NULL COMMENT 'HEXX,DEC,DECCC keycard number from user_keys.key',
  `controller_hw_type` varchar(64) NOT NULL COMMENT 'HW of last used controller',
  `controller_sn` varchar(64) NOT NULL COMMENT 'SN of last used controller',
  `datetime` datetime NOT NULL,
  `status_code` int(1) NOT NULL COMMENT 'status code from controller',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='last keyz activity';


CREATE TABLE `last_state` (
  `sn` varchar(64) NOT NULL,
  `hw_type` varchar(6) NOT NULL COMMENT 'тип устройства из event_codes.hw_type',
  `last_activity` datetime DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `last_access_card_number` varchar(17) DEFAULT NULL,
  `last_access_card_ts` datetime DEFAULT '0000-00-00 00:00:00',
  `last_deny_card_number` varchar(17) DEFAULT NULL,
  `last_deny_card_ts` datetime DEFAULT '0000-00-00 00:00:00',
  `last_button_open_ts` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `last_network_open_ts` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `external_ip` int(10) DEFAULT 0,
  `internal_ip` int(10) DEFAULT 0,
  `firmware_versions` varchar(32) DEFAULT '',
  `reader_protocol` varchar(32) DEFAULT '',
  PRIMARY KEY (`sn`,`hw_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='current (last) state of SKUD';


CREATE TABLE `logins` (
  `id` mediumint(4) NOT NULL AUTO_INCREMENT,
  `user` varchar(64) NOT NULL,
  `password_sha256` varchar(128) NOT NULL DEFAULT '' COMMENT 'SHA256(salt1+Passowrd+salt2)',
  `enable` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'allow login and operations',
  `twofactor_method` enum('','totp','email','bitcoin') NOT NULL DEFAULT '',
  `twofactor_secret` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `comment` varchar(256) DEFAULT '',
  `allowed_ip_range` varchar(256) DEFAULT NULL COMMENT 'ip acls for restrict login',
  `created_ts` datetime NOT NULL DEFAULT current_timestamp(),
  `last_changed_password_ts` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'date of last password change',
  `last_used_ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `allow_open_door` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'open door from system by login/password ',
  `allow_manage_keys` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Add / Edit / Delete keys data',
  `allow_enroll_keys` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Enroll keys in hardware',
  `allow_manage_badkeys` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'add / edit bad-keys',
  `allow_manage_offices` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'add / edit offices',
  `allow_manage_logins` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'add / edit logins+access rights',
  `allow_manage_options` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'add / edit global options',
  `allow_manage_controllers` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'add / edit controller data - names, login, ip',
  `allow_manage_proxy_events` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'add / edit proxy events',
  `salt1` varchar(64) NOT NULL DEFAULT '',
  `salt2` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`user`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='user creds for remote open';

INSERT INTO `logins` (`id`, `user`, `password_sha256`, `enable`, `twofactor_method`, `twofactor_secret`, `email`, `comment`, `allowed_ip_range`, `created_ts`, `last_changed_password_ts`, `last_used_ts`, `allow_open_door`, `allow_manage_keys`, `allow_enroll_keys`, `allow_manage_badkeys`, `allow_manage_offices`, `allow_manage_logins`, `allow_manage_options`, `allow_manage_controllers`, `allow_manage_proxy_events`, `salt1`, `salt2`) VALUES
(1,	'admin',	'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',	1,	'',	'',	'',	'ADMIN',	'',	NOW(),	NOW(),	NOW(),	1,	1,	1,	1,	1,	1,	1,	1,	1,	'',	'');


CREATE TABLE `offices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'название',
  `address` varchar(255) DEFAULT NULL COMMENT 'адрес офиса',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='List of Offices';


CREATE TABLE `options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abbr` varchar(32) NOT NULL COMMENT 'internal short name',
  `value` varchar(64) DEFAULT NULL COMMENT 'option value',
  `type` enum('b','i','t') NOT NULL DEFAULT 't' COMMENT 'option type (b - boolean, i - int, t - text)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='system options';

INSERT INTO `options` (`id`, `abbr`, `value`, `type`) VALUES
(1,	'opts_show_anonym_stat',	'1',	'b'),
(2,	'opts_ua_regexp_root_redirect',	'/Firefox|Mozilla|Safari|Chrom|Gecko|Links/',	't'),
(3,	'opts_allow_autoreg_controllers',	'1',	'b'),
(4,	'opts_allow_autoreg_auto_ip_filt',	'1',	'b'),
(6,	'opts_restrict_anonim_view_ips',	'',	't'),
(7,	'opts_restrict_open_door_ips',	'',	't'),
(8,	'opts_restrict_manage_keys_ips',	'',	't'),
(9,	'opts_restrict_enroll_keys_ips',	'',	't'),
(10,	'opts_hardware_z5r_interval',	'10',	'i'),
(11,	'opts_allow_profile_edit_pswd',	'1',	'b'),
(12,	'opts_allow_profile_edit_iprange',	'1',	'b'),
(13,	'opts_allow_profile_edit_email',	'1',	'b'),
(14,	'opts_allow_profile_edit_comment',	'1',	'b'),
(15,	'opts_allow_passwd_email_recovery',	'1',	'b'),
(16,	'opts_global_sysname',	'СКУД-админка / ACS WebAdmin',	't'),
(17,	'opts_email_recovery_from',	'ACS/СКУД Password <no-reply@localhost>',	't');

CREATE TABLE `proxy_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(64) NOT NULL,
  `hw_type` varchar(64) NOT NULL,
  `enable` enum('0','1') NOT NULL DEFAULT '0',
  `event_code` int(1) NOT NULL,
  `comment` varchar(255) NOT NULL COMMENT 'description',
  `target_url` varchar(255) NOT NULL COMMENT 'http(s) url to redirect event',
  `target_method` enum('GET-PARAMS','POST-PARAMS','POST-RAW','PUT-PARAMS','PUT-RAW') NOT NULL COMMENT 'http method',
  `target_raw_body` varchar(255) NOT NULL COMMENT 'only for raw-methods',
  `target_content_type` varchar(255) NOT NULL COMMENT 'additional header for target',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_code` (`event_code`,`hw_type`),
  KEY `sn` (`sn`,`hw_type`),
  CONSTRAINT `proxy_events_ibfk_2` FOREIGN KEY (`event_code`, `hw_type`) REFERENCES `event_codes` (`id`, `hw_type`),
  CONSTRAINT `proxy_events_ibfk_6` FOREIGN KEY (`sn`, `hw_type`) REFERENCES `controller_names` (`sn`, `hw_type`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='redirect (proxy) events to ext HTTPs servers';


CREATE TABLE `queue_commands` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `sn` varchar(64) NOT NULL COMMENT 'SN of controller',
  `hw_type` varchar(64) NOT NULL,
  `command` varchar(2048) NOT NULL COMMENT 'JSON-command',
  `created` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'create date',
  `executed` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'execution date',
  `executer` tinyint(1) NOT NULL,
  `ip` int(10) DEFAULT NULL COMMENT 'INET_ATON ( Source IP )',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Planned MGMT commands';


CREATE TABLE `user_keys` (
  `n` int(1) NOT NULL AUTO_INCREMENT COMMENT 'iD',
  `key` varchar(17) NOT NULL COMMENT 'iButton-Key HEXX,EMM,MARIN [HEX](dec,dec)',
  `type` enum('SIMPLE','BLOCK','MASTER') NOT NULL COMMENT 'key_type (user, blocking, master)',
  `access` varchar(255) NOT NULL COMMENT 'default access mask',
  `user` varchar(255) NOT NULL COMMENT 'keyholder username ',
  `comment` varchar(255) DEFAULT NULL COMMENT 'comment',
  `photo_url` varchar(255) DEFAULT NULL COMMENT 'photo_url',
  `office_id` int(11) DEFAULT NULL COMMENT 'offices_id',
  `create_date` varchar(19) DEFAULT NULL COMMENT 'create date',
  PRIMARY KEY (`n`),
  KEY `key` (`key`),
  KEY `office_id` (`office_id`),
  CONSTRAINT `user_keys_ibfk_1` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='User keys - CSV from Z5R Guard Commander';


-- 2022-10-22 11:28:22

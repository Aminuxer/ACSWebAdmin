<?php

require ("config.php");

# ===========================

header('Content-Type: application/json');

$ip = $_SERVER['REMOTE_ADDR'];
if ( ($opts_restrict_anonim_view_ips != '' or $opts_restrict_open_door_ips != '' or $opts_restrict_manage_keys_ips != '') and check_ip_acl($ip, $opts_restrict_anonim_view_ips.','.$opts_restrict_open_door_ips.','.$opts_restrict_manage_keys_ips) == 0 )
{
        print '{ "success":6,
        "err": "Error: IP '.$ip.' denied in config." }';
        die();
}

$mode = isset($_GET['mode']) ? $_GET['mode'] : '';
if ( $mode == '' ) {
     print '{ "code": 1, "opt1": "Use ?mode=last-state&sn=SERIAL",
             "opt2" : "?mode=event-user&sn=SERIAL&user=USERNAME",
             "opt3" : "?mode=queue-command&sn=SERIAL&cmd=open-door&user=?&pswd=?",
             "opt4" : "?mode=queue-command&sn=SERIAL&cmd=add-key&user=NEW-USERNAME&newkey=HEXX,KEY,EMMAR",
             "opt5" : "?mode=queue-command&sn=SERIAL&cmd=del-key&user=USERNAME",
             "opt6" : "?mode=queue-command&sn=SERIAL&cmd=del-key-from-db&user=USERNAME",
             "opt7" : "?mode=queue-command&sn=SERIAL&cmd=change-key&tz=NEW-TZ&user=USERNAME  (TZ: 255 full access; 0 no access; 1-127 time-regions bitmask; )"
}';
     exit (0);
} else { $ret_json = '{ "code": 6969,
  "msg": "unknown method" }'; }

$dt = date("Y-m-d-H:i:s");
$ts = time();


// print_r($_SESSION);

$user = '';
if ( isset($_SESSION['user']) AND $_SESSION['user'] != '' ) { $user = $_SESSION['user']; $online = 1; }
elseif ( isset($_SERVER['PHP_AUTH_USER']) ) { $user = $_SERVER['PHP_AUTH_USER']; $pswd = $_SERVER['PHP_AUTH_PW']; $online = 0; }
elseif ( isset($_GET['user']) ) { $user = $_GET['user']; $pswd = $_GET['pswd']; $online = 0; }
else { print '{ "code": 68, "msg": "NO AUTH DATA, '.$user.'" }'; die;  };

if ( $online == 0 ) { $ures = check_password_db($user, $pswd);
     if ( $ures[0] != 0 ) { print '{ "code": 69, "msg": "BAD AUTH, '.$user.'"}'; die; }
}
$user_info = get_user_info ($user);

// print_r($user_info);

if ( ( isset($_SESSION['twofactor_required']) AND $_SESSION['twofactor_required'] == 1 ) OR
     ( isset($user_info['twofactor_method']) AND $user_info['twofactor_method'] != '' )
) {
        if ( !isset($_SESSION['twofactor_passed']) or $_SESSION['twofactor_passed'] != 1 )
           { print '{ "code": 69, "msg": "2FA Must be passed, '.$user.'" }'; die; }
}



if ( $mode == 'last-state' ) {
    $sn = isset($_GET['sn']) ? mysqli_real_escape_string($conn, $_GET['sn']) : 0;
    $q1 = mysqli_query($conn, "SELECT ls.sn, ls.hw_type, ls.last_activity, ls.last_access_card_ts, ls.last_deny_card_ts, ls.last_deny_card_number,
        ls.last_button_open_ts, ls.last_network_open_ts,
        uk_ok.n, uk_ok.user AS allowed_user, uk_ok.comment AS allowed_comment,
        uk_no.n, uk_no.user AS denied_user, uk_no.comment AS denied_comment
    FROM `last_state` `ls`
    LEFT JOIN user_keys uk_ok ON uk_ok.key = ls.last_access_card_number
    LEFT JOIN user_keys uk_no ON uk_no.key = ls.last_deny_card_number
    WHERE ls.sn = '$sn'");
    if ( mysqli_num_rows($q1) == 0 ) { $ret_json = '{ "code": 1, "err": "No controller with this SN number" }'; }
    else {
        $r = mysqli_fetch_assoc($q1);

        if ( $r['denied_user'] == '' ) { $r['denied_user'] = 'UNKNOWN_KEY'; }
        if ( $r['denied_comment'] == '' ) { $r['denied_comment'] = $r['last_deny_card_number']; }
        $diff_la_sec = time() - strtotime($r['last_activity']);
        if ( $diff_la_sec > 2*$opts_hardware_z5r_interval ) { $cntr_is_online = 0; } else { $cntr_is_online = 1; }

        $ret_json = '{ "code": 0,
  "sn": '.$r['sn'].',
  "controller_periodic_interval": '.$opts_hardware_z5r_interval.',
  "hw_type": "'.$r['hw_type'].'",
  "last_activity": "'.$r['last_activity'].'",
  "controller_inactivity_seconds" : '.$diff_la_sec.',
  "controller_online" : '.$cntr_is_online.',

  "last_access_user": "'.$r['allowed_user'].'",
  "last_access_comment": "'.$r['allowed_comment'].'",
  "last_access_card_ts": "'.$r['last_access_card_ts'].'",

  "last_deny_user": "'.$r['denied_user'].'",
  "last_deny_comment": "'.$r['denied_comment'].'",
  "last_deny_card_ts": "'.$r['last_deny_card_ts'].'",

  "last_button_open_ts": "'.$r['last_button_open_ts'].'",
  "last_network_open_ts": "'.$r['last_network_open_ts'].'"  }';
    };
};


if ( $mode == 'event-user' ) {
    $sn   = isset($_GET['sn']) ? mysqli_real_escape_string($conn, $_GET['sn']) : 0;
    $user = isset($_GET['user']) ? mysqli_real_escape_string($conn, $_GET['user']) : 0;
    $q1 = mysqli_query($conn, "SELECT ls.sn, uk.n, uk.user, uk.comment, uk.create_date,
   (SELECT ts FROM events e1 WHERE e1.card = uk.key AND e1.event_code = 4 AND e1.sn = ls.sn ORDER BY e1.ts DESC LIMIT 1) AS last_success_ts,
   (SELECT ts FROM events e2 WHERE e2.card = uk.key AND e2.event_code = 6 AND e2.sn = ls.sn ORDER BY e2.ts DESC LIMIT 1) AS last_denied_ts
   FROM `user_keys` uk
     JOIN `last_state` ls ON ls.sn =  '$sn'
   WHERE uk.`user` = '$user'     LIMIT 1") or print mysqli_error($conn);
    if ( mysqli_num_rows($q1) == 0 ) { $ret_json = '{ "code": 1, "err": "No controller with this SN number / incorrect username" }'; }
    else {
        $r = mysqli_fetch_assoc($q1);
        $diff_success_sec = time() - strtotime($r['last_success_ts']);
        if ( $diff_success_sec > 36000 ) { $user_in_office = 0; } else { $user_in_office = 1; }

        $ret_json = '{ "code": 0,
  "sn": '.$r['sn'].',
  "key_N_number": '.$r['n'].',
  "user": "'.$r['user'].'",
  "comment": "'.$r['comment'].'",
  "user_create_date": "'.$r['create_date'].'", 
  "user_presence_in_office_in_seconds" : '.$diff_success_sec.',
  "user_in_office" : '.$user_in_office.' }';
    };
};


if ( $mode == 'queue-command' ) {
    $sn   = isset($_GET['sn']) ? mysqli_real_escape_string($conn, $_GET['sn']) : 0;
    $cmd  = isset($_GET['cmd']) ? mysqli_real_escape_string($conn, $_GET['cmd']) : 0;
    $user = isset($_GET['user']) ? mysqli_real_escape_string($conn, $_GET['user']) : 0;

    $ret_json = '{ "code": 1, "err": "No command" }';
    $q0 = mysqli_query($conn, "SELECT sn FROM last_state WHERE sn = '$sn' LIMIT 1");
    $q0_numrows = mysqli_num_rows($q0);

    $q00 = mysqli_query($conn, "SELECT `n`, `key`, `type` FROM `user_keys` WHERE `user` = '$user' LIMIT 1") or print mysqli_error($conn);
    $q00_numrows = mysqli_num_rows($q00);
    if ($q00_numrows > 0) { $ud00 = mysqli_fetch_assoc($q00);
            $user_n = $ud00['n'];
            $user_key = mysqli_real_escape_string($conn, $ud00['key']);
            $user_type = $ud00['type']; }

    if ( $cmd == 'open-door' ) {
            $pswd = isset($_GET['pswd']) ? mysqli_real_escape_string($conn, $_GET['pswd']) : '';
            if ( check_ip_acl($ip, $opts_restrict_open_door_ips) == 0 ) { $ret_json = '{ "code": 2, "err": "Error: IP '.$ip.' not allow to open door."}'; }
            elseif ( $q0_numrows == 0 ) { $ret_json = '{ "code": 3, "err": "No controller with this SN number" }'; } 
            else {
                        if ( $user_info['allow_open_door'] == 0 ) { $ret_json = '{ "code": 5, "err": "username / controller-SN / login - not allowed for open door ?" }'; }
                        else {
                                        $json_out_cmd = '{ "id": "'.$ts.'", "operation":"open_door", "direction": 0 }';
                                        $q1 = "INSERT INTO `queue_commands` (`sn`, `hw_type`, `command`, `executer`, `ip`)
                                               VALUES ('$sn', 'Z5RWEB', '$json_out_cmd', '0', INET_ATON('$ip') ) ";
                                        $q1 = mysqli_query($conn, $q1) or print mysqli_error($conn);
                                        if ( !mysqli_error($conn) ) { $ret_code = 0; $msg = 'OK'; } else { $ret_code = mysqli_errno($conn); $msg = mysqli_error($conn); };
                                        $ret_json = '{ "code": '.$ret_code.', "msg": "'.$msg.'" }';
                        }
            };
    }

    if ( $cmd == 'add-key' ) {
            if ( check_ip_acl($ip, $opts_restrict_manage_keys_ips) == 0 ) { $ret_json = '{ "code": 2, "err": "Error: IP '.$ip.' not allow to add key."}'; }
            elseif ( $user_info['allow_manage_keys'] == 0 ) { $ret_json = '{ "code": 5, "err": "username - not allowed for add key" }'; }
            elseif ( $q0_numrows == 0 ) { $ret_json = '{ "code": 3, "err": "No controller with this SN number" }'; } 
            else {
                        $newkey = isset($_GET['newkey']) ? mysqli_real_escape_string($conn, $_GET['newkey']) : 0;
                        $user = isset($_GET['user']) ? mysqli_real_escape_string($conn, $_GET['user']) : 0;
                        $q01 = mysqli_query($conn, "SELECT `n`, `key` FROM `user_keys` WHERE `key` = '$newkey' LIMIT 1") or print mysqli_error($conn);
                        $q03 = mysqli_query($conn, "SELECT `description` FROM `bad_keys` WHERE `card` = '$newkey' LIMIT 1") or print mysqli_error($conn);
                        if ( mysqli_num_rows($q01) > 0 ) { $ret_json = '{ "code": 5, "err": "This key already binded to another user" }'; }
                        elseif ( mysqli_num_rows($q03) > 0 ) { $ret_json = '{ "code": 51, "err": "This key blacklisted and denied to add" }'; }
                        elseif ($newkey == '') { $ret_json = '{ "code": 6, "err": "New key must be specified as HEXX,EMM,MARIN" } '; }
                        else {
                                if ( $q00_numrows == 0 ) {
                                        mysqli_query($conn, "INSERT INTO `user_keys` (`key`, `type`, `access`, `user`, `comment`, `photo_url`, `create_date`)
                                                         VALUES ('$newkey', 'SIMPLE', '255', '$user', '$user', '', NOW()) ");
                                        $nk_hex = mixed_hex_marine_2_pure_hex($newkey);
                                        $ret_json = '{ "code": 69, "err": "BAD KEY; '.$newkey.'; HEX: '.$nk_hex.'" } ';
                                        if ( $nk_hex == '000000000000') { $ret_json = '{ "code": 7, "err": "BAD KEY; New key must be specified as HEXX,XXX,YYYYY" } '; }
                                        else {
                                                $flags = 0;
                                                if (strlen($newkey) == 9) { $flags += 32; };
                                                $json_out_cmd = '{ "id": '.$ts.',"operation":"add_cards","cards": [{"card": "'.$nk_hex.'","flags": '.$flags.', "tz": 255}]}';
                                                $q1 = "INSERT INTO `queue_commands` (`sn`, `hw_type`, `command`, `executer`, `ip`) VALUES ('$sn', 'Z5RWEB', '$json_out_cmd', '0', INET_ATON('$ip') ) ";
                                                $q1 = mysqli_query($conn, $q1) or print mysqli_error($conn);
                                                if ( !mysqli_error($conn) ) { $ret_code = 0; $msg = 'OK'; } else { $ret_code = mysqli_errno($conn); $msg = mysqli_error($conn); };
                                                $ret_json = '{ "code": '.$ret_code.', "msg": "'.$msg.'" }';
                                        }
                                } else { $ret_json = '{ "code": 7, "msg": "New name of key conflict with another user" }'; };
                        }
            };
    }

    if ( $cmd == 'del-key' ) {
            if ( check_ip_acl($ip, $opts_restrict_manage_keys_ips) == 0 ) { $ret_json = '{ "code": 2, "err": "Error: IP '.$ip.' not allow to delete key."}'; }
            elseif ( $user_info['allow_manage_keys'] == 0 ) { $ret_json = '{ "code": 5, "err": "username - not allowed for del key" }'; }
            elseif ( $q0_numrows == 0 ) { $ret_json = '{ "code": 3, "err": "No controller with this SN number" }'; } 
            elseif ( $q00_numrows == 0 ) { $ret_json = '{ "code": 4, "err": "User not exists" }'; } 
            else {
                     $key_hex = mixed_hex_marine_2_pure_hex($user_key);
                     $json_out_cmd = '{ "id": '.$ts.',"operation":"del_cards", "cards": [ {"card": "'.$key_hex.'"} ]}';
                     $q1 = "INSERT INTO `queue_commands` (`sn`, `hw_type`, `command`, `executer`, `ip`) VALUES ('$sn', 'Z5RWEB', '$json_out_cmd', '0', INET_ATON('$ip') ) ";
                     $q1 = mysqli_query($conn, $q1) or print mysqli_error($conn);
                     if ( !mysqli_error($conn) ) { $ret_code = 0; $msg = 'OK'; } else { $ret_code = mysqli_errno($conn); $msg = mysqli_error($conn); };
                     $ret_json = '{ "code": '.$ret_code.', "msg": "'.$msg.'" }';
            };
    }

    if ( $cmd == 'del-key-from-db' ) {
            if ( check_ip_acl($ip, $opts_restrict_manage_keys_ips) == 0 ) { $ret_json = '{ "code": 2, "err": "Error: IP '.$ip.' not allow to delete key."}'; }
            elseif ( $user_info['allow_manage_keys'] == 0 ) { $ret_json = '{ "code": 5, "err": "username - not allowed for del key from DB" }'; }
            elseif ( $q00_numrows == 0 ) { $ret_json = '{ "code": 4, "err": "User not exists" }'; }
            else {
                    $key_hex = mixed_hex_marine_2_pure_hex($user_key);
                    $snlist = mysqli_query($conn, "SELECT sn, hw_type FROM controller_names");
                    while ($snr = mysqli_fetch_array($snlist)) {
                       $cu_sn = $snr['sn'];
                       $cu_hw = $snr['hw_type'];
                       if ( $cu_hw == 'Z5RWEB') {
                                $json_out_cmd = '{ "id": '.$ts.',"operation":"del_cards", "cards": [ {"card": "'.$key_hex.'"} ]}';
                                mysqli_query($conn, "INSERT INTO `queue_commands` (`sn`, `hw_type`, `command`, `executer`, `ip`) VALUES ('$cu_sn', '$cu_hw', '$json_out_cmd', '0', INET_ATON('$ip') ) ");
                       }
                    }
                    mysqli_query($conn, "DELETE FROM `user_keys` WHERE `user` = '$user' LIMIT 1");
                    if ( mysqli_errno($conn) != 0) { $ret_json = '{ "code": '.mysqli_errno($conn).', "err": "MySQL Error '.mysqli_error($conn).'" }'; }
                    else { $ret_json = '{ "code": 0, "err": "OK" }'; };
            };
    }

    if ( $cmd == 'change-key' ) {
            if ( check_ip_acl($ip, $opts_restrict_manage_keys_ips) == 0 ) { $ret_json = '{ "code": 2, "err": "Error: IP '.$ip.' not allow to modify key."}'; }
            elseif ( $user_info['allow_manage_keys'] == 0 ) { $ret_json = '{ "code": 5, "err": "username - not allowed for change key" }'; }
            elseif ( $q0_numrows == 0 ) { $ret_json = '{ "code": 3, "err": "No controller with this SN number" }'; } 
            elseif ( $q00_numrows == 0 ) { $ret_json = '{ "code": 4, "err": "User not exists" }'; } 
            else {
                        $tz = isset($_GET['tz']) ? (int)$_GET['tz'] : 0;
                        if ( $tz < 0 or $tz > 255  ) { $ret_json = '{ "code": 6, "err": "TZ value must be in range 0..255" } '; }
                        else {
                                $nk_hex = mixed_hex_marine_2_pure_hex($user_key);
                                $ret_json = '{ "code": 69, "err": "WTF ? KEY '.$user_key.'; HEX: '.$nk_hex.'" } ';
                                if ( $nk_hex == '000000000000' ) { $ret_json = '{ "code": 7, "err": "BAD KEY; New key must be specified as HEXX,XXX,YYYYY" } '; }
                                else {
                                        $flags = 0;
                                        if ($user_type == 'BLOCK') { $flags += 8; }
                                        if (strlen($user_key) == 9) { $flags += 32; };
                                        $json_out_cmd = '{ "id": '.$ts.',"operation":"add_cards","cards": [{"card": "'.$nk_hex.'", "flags": '.$flags.', "tz": '.$tz.'}]}';
                                        $q1 = "INSERT INTO `queue_commands` (`sn`, `hw_type`, `command`, `executer`, `ip`) VALUES ('$sn', 'Z5RWEB', '$json_out_cmd', '$user_n', INET_ATON('$ip') ) ";
                                        $q1 = mysqli_query($conn, $q1) or print mysqli_error($conn);
                                        if ( !mysqli_error($conn) ) { $ret_code = 0; $msg = 'OK'; } else { $ret_code = mysqli_errno($conn); $msg = mysqli_error($conn); };
                                        mysqli_query($conn, "UPDATE `user_keys` SET `access` = '$tz' WHERE `user` = '$user' AND `key` = '$user_key' ") or print mysqli_error($conn);
                                        $ret_json = '{ "code": '.$ret_code.', "msg": "'.$msg.'", "msg2": "TZ: '.$tz.'; ACC: '.tz_to_accstr($tz, 1).'" }';
                                }
                        }
            };
    }

};


print $ret_json;

// print_r($user_info);

?>

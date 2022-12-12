<?php

require ("config.php");

# ===========================

if ( preg_match ("$opts_ua_regexp_root_redirect", $_SERVER["HTTP_USER_AGENT"]) ) {
   print sprintf($loc_user_agent_admin_redirect, htmlspecialchars($_SERVER["HTTP_USER_AGENT"]) ).'<META HTTP-EQUIV="REFRESH" CONTENT="3;URL=./admin.php">'; die;
}


header('Content-Type: application/json');

$raw_post_data = file_get_contents('php://input');
$dt = date("Y-m-d-H:i:s");
$ts = time();
$ret_code = $ret_text = $ret_json = $debug_str = $cu_operation = '';

$json = json_decode($raw_post_data, true);
   if ( json_last_error() == 0 ) {
     if ($debug >= 2) { $debug_str .= '1-JSON. '.print_r($json, true); };
     $hw_type = mysqli_real_escape_string($conn, $json['type']);
          $sn = mysqli_real_escape_string($conn, $json['sn']);
     if ( !in_array($hw_type, $z5r_reftype) ) { $ret_code = 0; $ret_text = 'BAD HW-TYPE'; }
     else {   // Start correct HW-Type message parsing

        // Auto-registration;   
        $existing = mysqli_query($conn, "SELECT * FROM `controller_names` WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1");
        $num_rex_rows = mysqli_num_rows($existing);
        $rex = mysqli_fetch_array($existing);

        if ( $num_rex_rows > 0 AND $rex['allowed_ip_range'] != '' AND check_ip_acl($remote_ip, $rex['allowed_ip_range']) == 0 ) {   # Existing controller from untrusted IP
             $ret_code = 6; $ret_text = 'Error: IP '.$remote_ip.' denied for this controller. Edit controller allowed IP subnet';
        }
        elseif ( $num_rex_rows == 0 AND $opts_allow_autoreg_controllers == 1 )  {   # Auto reg new controller
             mysqli_query($conn, "INSERT INTO `controller_names` (`sn`, `hw_type`) VALUES ('$sn', '$hw_type')");
             if ( $opts_allow_autoreg_auto_ip_filt == 1 ) {
                  mysqli_query($conn, "UPDATE `controller_names` SET `allowed_ip_range` = '$remote_ip'
                                        WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1");
             }
             $ret_code = 5; $ret_text = 'Autoregistration OK';
        } elseif ( $num_rex_rows == 0 AND $opts_allow_autoreg_controllers == 0 )  {   # Auto reg new controller DISABLED
             $ret_code = 6; $ret_text = 'Error: Autoregistration DISABLED. Add SN: '.$sn.' HW-Type: '.$hw_type.' IP:'.$remote_ip.' manually';
        } else {   # Start update data for existing controller from correct IP
            $msgs = $json['messages'];
            mysqli_query($conn, "INSERT IGNORE INTO `last_state` (`sn`, `hw_type`) VALUES ('$sn', '$hw_type')");

          $msg_arr_count = 0;
          foreach ( $msgs as $index => $msg ) {  $msg_arr_count++;

            $cu_operation = isset ($msg['operation']) ? $msg['operation'] : '';
            $cu_id = isset ($msg['id']) ? mysqli_real_escape_string($conn, $msg['id']) : '';

            if ( $cu_operation == 'events' ) { $events_array = $msg['events']; }

            if ($debug >= 2) { $debug_str .= "\n foreach step: $msg_arr_count ; CU_ID: $cu_id ; foreach-index: $index"; }
            if ($debug >= 3) { $debug_str .= var_export($msg, true); }

            // Activate controller, need for start sending events
            if ( $cu_operation == 'power_on' ) {
                  $ret_json = '{"date": "'.$dt.'","interval": '.$opts_hardware_z5r_interval.',"messages": [ {"id":'.$ts.',"operation": "set_active", "active":1, "online":0 } ]}';
            }

            // Answer to pings and send queued commands
            if ( $cu_operation == 'ping' ) {
                  $q0 = mysqli_query($conn, "SELECT command FROM `queue_commands`
                                             WHERE `sn` = '$sn' AND `hw_type` = '$hw_type'
                                                   AND ( `executed` = '0000-00-00 00:00:00' OR `executed` IS NULL)
                                             ORDER BY `created` DESC ");
                  $cmds = '';
                  while ($r = mysqli_fetch_array($q0) ) {
                     $cmds .= $r['command'].',';
                  }
                  $cmds = substr($cmds, 0, -1);
                  $ret_code = 0;
                  $ret_json = '{"date": "'.$dt.'","interval": '.$opts_hardware_z5r_interval.', "messages": ['.$cmds.'] }';
                  mysqli_query($conn, "UPDATE `last_state` SET `last_activity` = '".$dt."' WHERE `sn` = '$sn' AND `hw_type` = '$hw_type'");
                  mysqli_query($conn, "UPDATE `queue_commands`  SET `executed` = '".$dt."' WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' AND ( `executed` = '0000-00-00 00:00:00' OR `executed` IS NULL) ");
            }

            // parse events
            if ( $cu_operation == 'events' ) {
                  $evts = $events_array; // $msgs[0]['events'];
                  $evnt_cnt = 0;
                  foreach ($evts as $event) {
                        $q0 = $q1 = '';
                        $event_time = mysqli_real_escape_string($conn, $event['time']);
                        $event_code = mysqli_real_escape_string($conn, $event['event']);
                        if ($debug >= 2 ) { $debug_str .= "\n".'2-EVENT. '.print_r($event, true); };
                              $emmarine_code = mysqli_real_escape_string ($conn, pure_hex_2_mixed_hex_marine($event['card']) );
                              if ( $emmarine_code != '' ) {
                                 mysqli_query($conn, "REPLACE INTO `last_activity_keys` (`key`, `controller_hw_type`, `controller_sn`, `datetime`, `status_code`)
                                                VALUES ('$emmarine_code', '$hw_type', '$sn', '$event_time', '$event_code')");
                              }

                              if ($event_code == 4) {  // door open by key (4) or pass (16) for INPUT
                                 $debug_str .= "\n".'3. KEY '.$emmarine_code." INCOMING \n";
                                 $q0 = "UPDATE `last_state` SET `last_access_card_number` = '$emmarine_code', `last_access_card_ts` = '$event_time'
                                       WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1";
                              }

                              if ($event_code == 5 ) {  // door open by key (5) or pass (17) for OUTPUT
                                 $debug_str .= "\n".'3. KEY '.$emmarine_code." OUTGOING \n";
                                 $q0 = "UPDATE `last_state` SET `last_access_card_number` = '$emmarine_code', `last_access_card_ts` = '$event_time'
                                       WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1";
                              }

                              if ($event_code == 2 or $event_code == 3 ) {  // door key not found
                                 $debug_str .= "\n".'3. KEY '.$emmarine_code.' NOT FOUND';
                                 $q0 = "UPDATE `last_state` SET `last_deny_card_number` = '$emmarine_code', `last_deny_card_ts` = '$event_time'
                                       WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1";
                              }

                              if ($event_code == 6 or $event_code == 10 or $event_code == 7 or $event_code == 11) {   // access denied (6) or door_locked (10)
                                 $debug_str .= "\n".'3. FOR KEY '.$emmarine_code.' ACCESS DENIED with code '.$event_code;
                                 $q0 = "UPDATE `last_state` SET `last_deny_card_number` = '$emmarine_code', `last_deny_card_ts` = '$event_time'
                                       WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1";
                              }

                              if ($event_code == 0 or $event_code == 1 ) {  // door open by button
                                 $debug_str .= "\n3. OPEN BY BUTTON";
                                 $q0 = "UPDATE `last_state` SET `last_button_open_ts` = '$event_time' WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1";
                              }

                              if ($event_code == 8 ) {  // door open by network command
                                 $debug_str .= '3. OPEN BY NETWORK COMMAND';
                                 $q0 = "UPDATE `last_state` SET `last_network_open_ts` = '$event_time' WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1";
                              }

                              if ($event_code == 21 ) {  // Power_on, power resume
                                 $debug_str .= "\n3. POWER RESUME";
                              }

                              if ($event_code == 16 or $event_code == 17 ) {  // door passed
                                 $debug_str .= '3. Pass with code '.$event_code;
                              }

                        if ($debug >= 2) { $debug_str .= "\n3.0-SQL. $q0"; };
                        if ($q0 != '') { mysqli_query($conn, $q0); };

                        $q1 = "INSERT IGNORE INTO `events` (`event_code`, `sn`, `hw_type`, `src_ip`, `card_hex`, `card`, `ts`, `flag`, `internal_id`)
                                             VALUES ('$event_code', '$sn', '$hw_type', INET_ATON('$remote_ip'), '".mysqli_real_escape_string($conn, $event['card'])."', '$emmarine_code', '$event_time', '".mysqli_real_escape_string($conn, $event['flag'])."', '".$cu_id."');";
                        if ($debug >= 2) { $debug_str .= "\n3.1-SQL. $q1"; };
                        mysqli_query($conn, $q1);
                        $event_code_last_id = mysqli_insert_id($conn);

                        /* START EVENT PROXIFY */
                        $q2 = "SELECT * FROM `proxy_events` WHERE `event_code` = '$event_code' AND `enable` = '1'
                                         AND `sn` = '$sn' AND `hw_type` = '$hw_type' ";
                        $qq2 = mysqli_query($conn, $q2);
                        if ($debug >= 1 and mysqli_num_rows($qq2) > 0 ) { $debug_str .= "\n3.2-EventsProxy-SQL-with-rows. $q2"; };

                        while ( $ev = mysqli_fetch_array($qq2) ) {       #   Start events processing
                           if ($debug >= 1) { $debug_str .= "\n3.2.1-ProxyEvent-iD ".$ev['id'].", Code $event_code, SN $sn, HW $hw_type,
                               send ".$ev['target_method']." to ".$ev['target_url']; };

                           $f_url = $ev['target_url'];
                           $f_body = $ev['target_raw_body'];
                           if ( preg_match( '/LOGIN|OFFICE/', $f_url) or preg_match( '/LOGIN|OFFICE/', $f_body) ) {
                                /* if need extra-data - fetch this from DB */
                                $extra1 = mysqli_query($conn, "SELECT uk.type AS key_type, uk.access as access, uk.user AS login, uk.comment, uk.photo_url, uk.office_id, of.name AS offce_name, of.address AS office_address FROM `user_keys` uk
                                                               LEFT JOIN offices of ON of.id = uk.office_id
                                                               WHERE uk.key = '$emmarine_code' LIMIT 1");
                                $extrar1 = mysqli_fetch_assoc($extra1);
                                $f_url = str_replace('[LOGIN]', $extrar1['login'], $f_url);
                                $f_url = str_replace('[OFFICE]', $extrar1['offce_name'], $f_url);
                                $f_body = str_replace('[LOGIN]', $extrar1['login'], $f_body);
                                $f_body = str_replace('[OFFICE]', $extrar1['offce_name'], $f_body);
                           }
                           $f_url = str_replace('[SN]', $sn, $f_url);
                           $f_url = str_replace('[HWTYPE]', $hw_type, $f_url);
                           $f_url = str_replace('[EVENT_ID]', $event_code_last_id, $f_url);
                           $f_url = str_replace('[EVENT_CODE]', $event_code, $f_url);
                           $f_url = str_replace('[CARD]', $event['card'], $f_url);
                           $f_url = str_replace('[CARD_HEX]', $emmarine_code, $f_url);
                           $f_url = str_replace('[DATETIME]', rawurlencode($event_time), $f_url);
                           $f_url = str_replace('[IP]', $remote_ip, $f_url);

                           $f_body = str_replace('[SN]', $sn, $f_body);
                           $f_body = str_replace('[HWTYPE]', $hw_type, $f_body);
                           $f_body = str_replace('[EVENT_ID]', $event_code_last_id, $f_body);
                           $f_body = str_replace('[EVENT_CODE]', $event_code, $f_body);
                           $f_body = str_replace('[CARD]', $event['card'], $f_body);
                           $f_body = str_replace('[CARD_HEX]', $emmarine_code, $f_body);
                           $f_body = str_replace('[DATETIME]', $event_time, $f_body);
                           $f_body = str_replace('[IP]', $remote_ip, $f_body);
//
                           $params_string = parse_url($f_url, PHP_URL_QUERY);
                           parse_str($params_string, $params_array);

                           $http_curl = curl_init();
                           curl_setopt($http_curl, CURLOPT_URL, $f_url);
                           curl_setopt($http_curl, CURLOPT_VERBOSE, $debug);
                           curl_setopt($http_curl, CURLOPT_PORT , parse_url($f_url, PHP_URL_PORT) );
                           curl_setopt($http_curl, CURLOPT_RETURNTRANSFER, true);
                           curl_setopt($http_curl, CURLOPT_FOLLOWLOCATION, true);
                           curl_setopt($http_curl, CURLOPT_CONNECTTIMEOUT, 20);
                           curl_setopt($http_curl, CURLOPT_TIMEOUT, 40);

                           if ( parse_url ($f_url, PHP_URL_SCHEME) == 'https' ) {
                              curl_setopt($http_curl, CURLOPT_SSL_VERIFYPEER, 0);
                           }

                           if ( $ev['target_content_type'] != '' ) {
                              curl_setopt($f_url, CURLOPT_HTTPHEADER, array('Content-type:'.$ev['target_content_type']) );
                           }

                           if ($ev['target_method'] == 'POST-RAW' or $ev['target_method'] == 'POST-PARAMS') {
                              curl_setopt($http_curl, CURLOPT_POST, 1);
                              if ( $ev['target_method'] == 'POST-PARAMS' ) {
                                    curl_setopt($http_curl, CURLOPT_POSTFIELDS, $params_array);
                              }
                           }

                           if ($ev['target_method'] == 'PUT-RAW' or $ev['target_method'] == 'PUT-PARAMS') {
                              curl_setopt($http_curl, CURLOPT_CUSTOMREQUEST, "PUT");
                              curl_setopt($http_curl, CURLOPT_POSTFIELDS, http_build_query($params_array));
                           }

                           if ($ev['target_method'] == 'POST-RAW' or $ev['target_method'] == 'PUT-RAW') {
                                  curl_setopt($http_curl, CURLOPT_POSTFIELDS, $f_body);
                           }


                           $http_data = curl_exec($http_curl);
                           if ($debug > 0) {
                              if ( ! curl_errno($http_curl) ) {
                                    $http_info = curl_getinfo($http_curl);
                                    $debug_str .= '3.2.2 -curl- Took '.$http_info['total_time'].' seconds to send a request to '.$http_info['url'];
                              } else {
                                    $debug_str .= '3.2.2 -curl- error: ' . curl_error($http_curl);
                              }
                           }

                        }       #   End events processing
                        /* END EVENT PROXIFY */

                        $evnt_cnt++;
                  }
                  $ret_json = '{"date": "'.$dt.'","interval": '.$opts_hardware_z5r_interval.',"messages": [
                                 {"id":'.$ts.',"operation": "events","events_success": '.$evnt_cnt.' }
                        ]}';
            }   // End parse events

            if ( $cu_operation == '' ) {
               $ret_code = 1; $ret_text = 'OK, empty operation';
            }

          }   // End foreach 
        }    // End update data for existing controller from correct IP
     }    // End correct HW-Type message parsing
   } else { $ret_code = 4; $ret_text = 'BAD REQUEST JSON"'; };

if ($ret_json == '') { $ret_json = '{ "success":'.$ret_code.', "err": "'.$ret_text.'"}'; };
print "$ret_json\n";

if ( $debug > 0 ) {
     $str = "\n\n---==={ $remote_ip       ".date("Y-m-d H:i:s")." } \n";
     if ( isset($_SERVER['HTTP_REFERER'])    ) { $str .= "from: ".$_SERVER['HTTP_REFERER']."\n"; };
     if ( isset($_SERVER['HTTP_USER_AGENT']) ) { $str .= "UA: ".$_SERVER['HTTP_USER_AGENT']."\n"; };

     $str .= "-OP-->$cu_operation<--/OP-\n";
     if ( isset($_SERVER['PHP_AUTH_USER']) ) { $str .= "-HTTP_AUTH-->".$_SERVER['PHP_AUTH_USER']." / ".$_SERVER['PHP_AUTH_PW']."<--/HTTP-AUTH-\n"; }
     $str .= "-raw-->$raw_post_data<--/raw-\n";
     $str .= "-ret-->$ret_json<--/ret-\n";
     $str .= "-debug-->$debug_str<--/debug-";


     if ( $debug < 2 and $cu_operation == 'ping' and $ret_code == 0 ) { die; }
     $fh = fopen($debug_log, "a");
           fwrite($fh, $str);
           fclose($fh);
}

?>

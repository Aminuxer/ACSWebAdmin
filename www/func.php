<?php

session_start();
$remote_ip = $_SERVER['REMOTE_ADDR'];
$opts__int__sys__version = 'v.2022-10-04.007';
$conn = mysqli_connect($db_host, $db_user, $db_pswd, $db_name);

$z5r_reftype = array ('Z5RWEB');

$options_res = mysqli_query ($conn, "SELECT abbr, value FROM options") or die('ERROR - NO data in MySQL table options<Br/>'.mysqli_error($conn));
while ($ops_row = mysqli_fetch_array($options_res)) {
   $opsrabbr = $ops_row["abbr"];
   ${$opsrabbr} = $ops_row["value"];
}

include("localization/default.php");   /* Локализация / Localizaion */

function net_match ($ip, $network) {   # net_match ($_SERVER['REMOTE_ADDR'], '192.168.0.0/16')
   $ip_arr = explode ('/', $network);
   if (!isset($ip_arr[1]) or $ip_arr[1] == '') { $ip_arr[1] = 32; };
   $network_long = ip2long($ip_arr[0]);
   $x = ip2long($ip_arr[1]);
   $mask = long2ip($x) == $ip_arr[1] ? $x : 0xffffffff << (32 - $ip_arr[1]);
   $ip_long = ip2long($ip);
   # echo ">".$ip_arr[1]."> ".decbin($mask)."\n";
   return ($ip_long & $mask) == ($network_long & $mask); }


function check_ip_acl($ip, $ip_acl_str) { # check_ip_acl ($_SERVER['REMOTE_ADDR'], '192.168.0.0/16, 127.0.0.0/8')
   $ip_acl_str = trim($ip_acl_str);
   $ip_acl_arr = preg_split("/[\s;,|]+/", $ip_acl_str);
   foreach ( $ip_acl_arr as $key => $acl ) { if (net_match($ip, trim($acl)) == 1) { return $key+1; }   }
   return 0; }


function mixed_hex_marine_2_pure_hex($newkey) { # 1A2F,123,45678 --> pure hexx
   if ($newkey != '') {
      $nk = explode(',', $newkey);
      $nk_hex = str_pad(strtoupper($nk[0]), 6, "0", STR_PAD_LEFT). str_pad(strtoupper(dechex($nk[1])), 2, "0", STR_PAD_LEFT). str_pad(strtoupper(dechex($nk[2])), 4, "0", STR_PAD_LEFT);
   } else { $nk_hex = ''; }
   return $nk_hex; }


function pure_hex_2_mixed_hex_marine($card_number) { # pure hexx --> 1A2F,123,45678
   $emmarine_code = str_pad((substr($card_number, 2, 4)), 4, "0", STR_PAD_LEFT).','
                         .str_pad(hexdec(substr($card_number, 6, 2)), 3, "0", STR_PAD_LEFT).','
                         .str_pad(hexdec(substr($card_number, 8, 4)), 5, "0", STR_PAD_LEFT);
   return $emmarine_code; }


function generate_random_password($length=15, $arr='abcdefghijkmnoprstuvxyzABCDEFGHJKLMNPQRSTUVXYZ23456789_~!@#$%^&*')
   { $length = (int)$length; $pass = ""; srand( ((int)((double)microtime()*1000003)) );
   if ($length == 0) { $length = 15; }
   for($i = 0; $i < $length; $i++) { $index = mt_rand(0, strlen($arr) - 1); $pass .= $arr[$index]; }
   return $pass; }


function get_password_complex ($pass) { $pass = trim($pass);
    if (strlen($pass) > 0) { $diff = 0; } ELSE { return -1; }
    if (preg_match("/[a-z]{1,}/", $pass)) $diff++;
    if (preg_match("/[A-Z]{1,}/", $pass)) $diff++;
    if (preg_match("/[0-9]{1,}/", $pass)) $diff++;
    if (preg_match("/[-\~\`\!\"\'\|\№\#\$\&\^\%\@\;\%\:\?\*\/\+\_\=\.\,]{1,}/", $pass)) $diff++;
   return $diff; }


function tz_to_accstr ($tz, $hum=0) {   # 14 (bitmask) --> -234--- (timezone string-list)
         global $loc_common_phrase_always, $loc_common_phrase_never;
         $acc = substr(str_pad(decbin($tz), 7, "0", STR_PAD_LEFT), 0, 7);
         $acc = str_replace("0", "-", $acc);
         for ($i=1; $i <= 7; $i++) {
              if (substr($acc, -1*$i, 1) == "1") {  $acc = substr_replace ($acc, $i, -1*$i, 1); };
          }
          $acc = strrev ($acc);
          if ($hum == 1) {
             if ($tz == 255) { $acc = $loc_common_phrase_always; }
             if ($tz == 0) { $acc = $loc_common_phrase_never; }
          }
          return $acc;
}


function int2checkbox ($i, $name='', $disabled=0, $title='', $label='' ) {
    if ($i > 0) { $st = ' checked'; } ELSE { $st = ''; };
    if ( $disabled > 0 ) { $st .= ' disabled'; };
    if ($name != '' ) { $st .= ' name="'.$name.'"'; }
    if ($title != '' ) { $st .= ' title="'.$title.'"'; }
    if ($label != '') {
       $id = 'twid_'.$name.'_'.md5($name);
       $st .= ' id="'.$id.'"';
       $label = '<label for="'.$id.'">'.$label.'</label>';
    }
    $cu_form_element = '<INPUT type="checkbox"'.$st.'>'.$label;
   return $cu_form_element;
}


function create_controller_select ($name, $act='') { $select_output = '';   /* Селектор контроллера */
global $conn;
$res = mysqli_query($conn, "SELECT cn.sn, cn.hw_type, cn.name, o.name AS office_name
FROM controller_names cn
  LEFT JOIN offices o ON o.id = cn.office_id
ORDER BY o.name, cn.name");
if ($name != 'disabled')
         { $cu_name = 'NAME="'.$name.'" title="Выберите контроллер"'; }
    ELSE { $cu_name = 'disabled title="Выбор контроллера невозможен"'; };
$select_output .= '<SELECT TYPE="text" SIZE="1" '.$cu_name.'><OPTION value="">--';
while ($row = mysqli_fetch_array($res)) {
              if ($row["sn"] == $act) {$sell = ' SELECTED';} ELSE {$sell = ''; };
              $select_output .= '<OPTION value="'.$row["sn"].'"'.$sell.'>['.htmlspecialchars($row["hw_type"].' '.$row["sn"]).'] '.htmlspecialchars($row["office_name"]).' '.htmlspecialchars($row["name"], ENT_QUOTES);
      }
$select_output .='</SELECT>'; return $select_output; }


function create_controller_type_select ($name, $act='') { $select_output = '';   /* Селектор типа контроллера */
$act = (int) $act;
global $z5r_reftype;
if ($name != 'disabled')
         { $cu_name = 'NAME="'.$name.'" title="Выберите тип контроллера"'; }
    ELSE { $cu_name = 'disabled title="Выбор типа контроллера невозможен"'; };
$select_output .= '<SELECT TYPE="text" SIZE="1" '.$cu_name.'>';
foreach ($z5r_reftype as $row) {
              if ($row == $act) {$sell = ' SELECTED';} ELSE {$sell = ''; };
              $select_output .= '<OPTION value="'.$row.'"'.$sell.'>'.htmlspecialchars($row, ENT_QUOTES);
      }
$select_output .='</SELECT>'; return $select_output; }


function create_twofactor_type_select ($name, $act='') { $select_output = '';   /* Селектор типа 2FA */
if ($name != 'disabled')
         { $cu_name = 'NAME="'.$name.'" title="Выберите тип 2FA"'; }
    ELSE { $cu_name = 'disabled title="Выбор типа невозможен"'; };
$select_output .= '<SELECT TYPE="text" SIZE="1" '.$cu_name.'>';
$types = array ('none', 'totp', 'bitcoin', 'email');
foreach ($types as $row) {
              if ($row == $act) {$sell = ' SELECTED';} ELSE {$sell = ''; };
              $select_output .= '<OPTION value="'.$row.'"'.$sell.'>'.htmlspecialchars($row, ENT_QUOTES);
      }
$select_output .='</SELECT>'; return $select_output; }


function create_http_method_select ($name, $act='') { $select_output = '';   /* Селектор типа 2FA */
if ($name != 'disabled')
         { $cu_name = 'NAME="'.$name.'" title="Выберите тип HTTP"'; }
    ELSE { $cu_name = 'disabled title="Выбор типа невозможен"'; };
$select_output .= '<SELECT TYPE="text" SIZE="1" '.$cu_name.'>';
$types = array ('GET-PARAMS','POST-PARAMS','POST-RAW','PUT-PARAMS','PUT-RAW');
foreach ($types as $row) {
              if ($row == $act) {$sell = ' SELECTED';} ELSE {$sell = ''; };
              $select_output .= '<OPTION value="'.$row.'"'.$sell.'>'.htmlspecialchars($row, ENT_QUOTES);
      }
$select_output .='</SELECT>'; return $select_output; }


function create_office_select ($name, $act='') { $select_output = '';   /* Селектор контроллера */
$act = (int) $act;
global $conn;
$res = mysqli_query($conn, "SELECT id, name FROM offices cn ORDER BY name");
if ($name != 'disabled')
         { $cu_name = 'NAME="'.$name.'" title="Выберите офис"'; }
    ELSE { $cu_name = 'disabled title="Выбор офиса невозможен"'; };
$select_output .= '<SELECT TYPE="text" SIZE="1" '.$cu_name.'><OPTION value="">--';
while ($row = mysqli_fetch_array($res)) {
              if ($row["id"] == $act) {$sell = ' SELECTED';} ELSE {$sell = ''; };
              $select_output .= '<OPTION value="'.$row["id"].'"'.$sell.'>'.htmlspecialchars($row["name"], ENT_QUOTES);
      }
$select_output .='</SELECT>'; return $select_output; }


function create_event_code_select ($name, $act='') { $select_output = '';   /* Селектор контроллера */
$act = (int) $act;
global $conn;
$res = mysqli_query($conn, "SELECT id, hw_type, name FROM event_codes ORDER BY hw_type, id");
if ($name != 'disabled')
         { $cu_name = 'NAME="'.$name.'" title="Выберите код события"'; }
    ELSE { $cu_name = 'disabled title="Выбор невозможен"'; };
$select_output .= '<SELECT TYPE="text" SIZE="1" '.$cu_name.'><OPTION value="">--';
while ($row = mysqli_fetch_array($res)) {
              if ($row["id"] == $act) {$sell = ' SELECTED';} ELSE {$sell = ''; };
              $select_output .= '<OPTION value="'.$row["id"].'"'.$sell.'>'.htmlspecialchars($row["id"], ENT_QUOTES).' ['.htmlspecialchars($row["hw_type"], ENT_QUOTES).'] '.htmlspecialchars($row["name"], ENT_QUOTES);
      }
$select_output .='</SELECT>'; return $select_output; }


// @return true if password and nickname match
function check_password_db($login,$password) {
  global $conn;
  global $remote_ip;
  $code = 255; $status = 'UNAUTHORIZED';
  $login = addslashes($login);
  $a = mysqli_query($conn, "SELECT * FROM `logins` WHERE `user` = '$login' LIMIT 1");
  if ( mysqli_num_rows($a) == 0 ) { $code = -1; $status = 'NO user'; }
  else {
      $m = mysqli_fetch_array($a);
      if ( strcasecmp(trim($m['password_sha256']), hash('sha256', $m['salt1'].$password.$m['salt2']) ) == 0 ) {
         if ( $m['allowed_ip_range'] != '' AND check_ip_acl($remote_ip, $m['allowed_ip_range']) == 0 ) {
               $code = 2; $status = 'IP not in allowed range';
         } elseif ( $m['enable'] != 1 ) {
            $code = 3; $status = 'DISABLED';
         }
         else {
            $code = 0; $status = 'OK';
         }
      } else { $code = 1; $status = 'Bad password'; };
  }
  return array($code, $status);
}


function get_user_info ($login) {
   global $conn;
   $user_info_query = mysqli_query($conn, "SELECT * FROM `logins` WHERE `user` = '$login' LIMIT 1");
   $user_info = mysqli_fetch_assoc($user_info_query) or print mysqli_error($conn);
   return $user_info;
}


function get_statistic () {
          global $conn, $loc_entity_name_statistic, $loc_menu_element_controllers, $loc_susbys_list_events, $loc_common_phrase_activity, $loc_property_name_access, $loc_property_name_reject;
          $qsq = mysqli_query($conn, "SELECT MAX(ls.last_activity) AS last_act, MAX(ls.last_access_card_ts) AS last_ok,
                              MAX(ls.last_deny_card_ts) AS last_fail, COUNT(DISTINCT(ls.sn)) AS cnt_devices, COUNT(e.id) AS cnt_events
                                      FROM `last_state` ls LEFT JOIN events e ON e.sn = ls.sn");
          $qsr = mysqli_fetch_assoc($qsq);
          $out = '<div class="anon_stat">
          <span class="mini_header">'.$loc_entity_name_statistic.':</span>
          <table>
          <tr><td>'.$loc_menu_element_controllers.'</td><td>'.$qsr['cnt_devices'].'</td></tr>
          <tr><td>'.$loc_susbys_list_events.'</td><td>'.$qsr['cnt_events'].'</td></tr>
          <tr><td>'.$loc_common_phrase_activity.'</td><td>'.$qsr['last_act'].'</td></tr>
          <tr><td>'.$loc_property_name_access.'</td><td>'.$qsr['last_ok'].'</td></tr>
          <tr><td>'.$loc_property_name_reject.'</td><td>'.$qsr['last_fail'].'</td></tr>
          </table>
          </div>';   
          return $out;
}

?>

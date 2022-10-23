<?php

require("config.php");
require("totp.php");

require_once 'BitcoinECDSA.php';
use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;

$tab = isset($_GET['tab']) ? $_GET['tab'] : '';

$out = $edit_link = ''; $hide_menu = 0;

$sess_control_string = $remote_ip.'_'.$_SERVER['HTTP_USER_AGENT'].'_'.date("Ymd");
$sess_control_hash = hash('sha512', $sess_control_string.'__'.$sess_secret_salt);

/* Main checks - Session User, session keys, main IP filters */
if ( isset($_SESSION['user']) AND $_SESSION['user'] != '' AND $_SESSION['control_hash'] == $sess_control_hash) {   // User logged with login/password
     if ($tab == 'logout') {   // Logout can be called only by logged users
        $out .=  'LOGOUT';
        session_destroy();
        $out .= '<META HTTP-EQUIV="Refresh" CONTENT="0; URL='.$_SERVER['PHP_SELF'].'?">';
     } elseif ($tab == 'converter') {   // Converer accesible to any logged users;
               $out =  '<h3>'.$loc_options_value.':</h3>';
          $sk = isset ($_POST['sk']) ? mysqli_real_escape_string($conn, $_POST['sk']) : '';
          $tz = isset ($_POST['tz']) ? (int)($_POST['tz']) : 0;

          $out .=  '<form method="POST"> <Br/>
               iD : <input type=text name="sk" value="'.$sk.'" placeholder="XXXX,ddd,ddddd"> <Br/>
               TZ : <input type=text name="tz" value="'.$tz.'" title="0..255"> <Br/><Br/>
               <input type="submit" value="'.$loc_common_phrase_send.'">
          </form><Br/><Br/>
          <pre>';

          $out .= "Mix-2-Pure     ".mixed_hex_marine_2_pure_hex($sk)."<Br/>";
          $out .= "Pure-2-Mix   ".pure_hex_2_mixed_hex_marine($sk)."<Br/>";
          if ( $tz == 255 ) { $acc = $loc_common_phrase_always; }
          elseif ($tz == 0) { $acc = $loc_common_phrase_never; }
                       else { $acc = tz_to_accstr ($tz); }
          $out .= "TZ $tz = $loc_property_name_access $acc<Br/>";
     }
     $logged_user = mysqli_real_escape_string($conn, $_SESSION['user']);   // Need for another logic and display logged username
} else {
     /* Login FORM for unauthorised */
          $u = isset($_POST['f_user']) ? mysqli_real_escape_string($conn, $_POST['f_user']) : '';
          $p = isset($_POST['f_pswd']) ? $_POST['f_pswd'] : '';
          if ( $u != '' ) {
              $lst = check_password_db($u, $p);
              if ( $lst[0] == 0 ) {
                   $_SESSION['user'] = $u;
                   $_SESSION['control_hash'] =  $sess_control_hash;
                   if ($p == '') { $_SESSION['force_change_password'] = 1; }
                   mysqli_query ($conn, "UPDATE `logins` SET `last_used_ts` = NOW() WHERE `user` = '$u' LIMIT 1");
                   print 'OK '.$u.' LOGGED   <META HTTP-EQUIV="Refresh" CONTENT="0; URL='.$_SERVER['PHP_SELF'].'">';
                   die;
              } else {
                   $out .= "<div class=\"red\">$loc_common_phrase_error</div>";
                   if ($debug > 1) { $out .= $lst[0].' ; Desc: '.$lst[1]; };
              }
          }

          $out =  '<div class="div_login_form">
          <FORM method="POST">
               <INPUT type="text" name="f_user" placeholder="<'.$loc_common_phrase_username.'>" title="'.$loc_common_phrase_username.'"> <Br/>
               <INPUT type="password" name="f_pswd"  placeholder="<'.$loc_common_phrase_password.'>" title="'.$loc_common_phrase_password.'"> <Br/>
               <INPUT type="submit" value="'.$loc_common_phrase_login.'"><Br/>
          </FORM></div>'.$out;
     $tab = 'no_access';
     $hide_menu = 1;
     $logged_user = '';
     if ( $opts_allow_passwd_email_recovery == 1 ) { $out .= '<div class="passwd_email_recovery"><a href="password-recovery.php?user='.htmlspecialchars($u).'">'.$loc_susbys_email_pswd_recovery.'</a></div>'; }
     if ( $opts_show_anonym_stat == 1 AND check_ip_acl($remote_ip, $opts_restrict_anonim_view_ips) != 0 ) {   // Anonymous statistics IF ENABLED IN OPTIONS
          $out .= get_statistic();
     }
};


print '<!DOCTYPE HTML>
<html> <head>   <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="shortcut icon" href="z5r.ico" />
  <meta charset="utf-8">
<title>'.htmlspecialchars($opts_global_sysname).'</title>
  <link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body><div class="div_sysname">';
     if ($logged_user != '' ) { print "<a href=\"?tab=profile\">".htmlspecialchars($logged_user)."@$remote_ip</a> ";
          $user_info_query = mysqli_query($conn, "SELECT * FROM `logins` WHERE `user` = '$logged_user' LIMIT 1");
          $user_info = mysqli_fetch_assoc($user_info_query) or print mysqli_error($conn);
     }
     print '<a href="?">'.htmlspecialchars($opts_global_sysname).'</a>';
     if ($logged_user != '' ) { print " <a href=\"?tab=logout\">$loc_common_phrase_logout</a>"; }
     print '</div>';


// Additional global user permission check_ip_acl , 2FA , empty default password
if ($logged_user != '') {

     if ( $user_info['salt1'] == '' OR $user_info['salt2'] == '' ) {   # default password - force password change
          $hide_menu = 1;
          $out = "$loc_common_phrase_password : $loc_common_phrase_must_be_filled !";
          $tab = 'profile';
     }

     if ( isset($user_info['twofactor_method']) AND $user_info['twofactor_method'] != '' ) {   # Two-factor auth
          $_SESSION['twofactor_required'] = 1;
          if ( ! isset($_SESSION['twofactor_passed']) or $_SESSION['twofactor_passed'] != 1 ) {
               $out .= "$loc_common_phrase_2fa $loc_common_phrase_must_be_filled !!";
               $hide_menu = 1;
               $tab = 'twofactor_steps';
          }
     }

     if ( trim($user_info['allowed_ip_range']) != '' AND check_ip_acl($remote_ip, $user_info['allowed_ip_range']) == 0 ) {   # per-user IP ACL
          $out = "$loc_common_phrase_username : $loc_common_phrase_ip_not_allowed - $loc_common_phrase_disabled_user_profile";
          $hide_menu = 1;
          $tab = 'no_access';
     }

     if ( $opts_restrict_manage_keys_ips != '' AND check_ip_acl($remote_ip, $opts_restrict_manage_keys_ips) == 0 ) { $mgmt_keys = 0; } else { $mgmt_keys = 1; };   # Global IP-ACL KeyMgmt
}


switch ($tab) {   /* Start Switch */

     case 'controllers' :
          $out = '<table>';
          if ($opts_allow_autoreg_controllers == 1) {
               if ( $opts_allow_autoreg_auto_ip_filt == 1 ) { $color_class = 'green'; $ipft = "$loc_susbys_src_ip_bind $loc_common_phrase_on. $loc_susbys_src_ip_bind_help; "; }
                 else  { $color_class = 'red'; $ipft = "$loc_susbys_src_ip_bind $loc_common_phrase_off. $loc_susbys_src_ip_bind_help2;"; };
               $out .= '<tr> <td colspan="3" class="'.$color_class.'">'.$loc_susbys_controllers_autoreg.' '.$loc_common_phrase_on.'; '.$ipft.'</td> </tr>';
          } else { $out .= '<tr> <td colspan="3" class="green">'.$loc_susbys_controllers_autoreg.' '.$loc_common_phrase_off.' '.$loc_susbys_src_ip_bind_help3.'</td> </tr>'; }
          if ($user_info['allow_manage_controllers'] == 1) { $out .= '<tr> <td colspan="3"><a href="?tab=add_controller">'.$loc_common_phrase_add.' '.$loc_entity_name_controller.'</td> </tr>'; }

          $q1 = mysqli_query($conn, "SELECT ls.sn, ls.hw_type, ls.last_activity, ls.last_access_card_ts, ls.last_access_card_number, ls.last_deny_card_ts, ls.last_deny_card_number,
        ls.last_button_open_ts, ls.last_network_open_ts, cn.name AS controller_name, cn.hw_type,
        uk_ok.n AS allowed_n, uk_ok.user AS allowed_user, uk_ok.comment AS allowed_comment,
        uk_no.n AS denied_n, uk_no.user AS denied_user, uk_no.comment AS denied_comment, of.name AS office_name
    FROM `last_state` `ls`
    LEFT JOIN user_keys uk_ok ON uk_ok.key = ls.last_access_card_number
    LEFT JOIN user_keys uk_no ON uk_no.key = ls.last_deny_card_number
    LEFT JOIN  controller_names cn ON cn.sn = ls.sn
    LEFT JOIN offices of ON of.id = cn.office_id
    ORDER BY of.name, cn.name ASC");
               if ( mysqli_num_rows($q1) == 0 ) { $out .= $loc_susbys_controllers_no_data; }
               else {
                    $out .= '<tr> <th>'.$loc_common_phrase_sn.'</th> <th>'.$loc_common_phrase_type.'</th>  <th>'.$loc_common_phrase_activity.'</th> </tr>';
                    while ( $r = mysqli_fetch_assoc($q1) ) {

                         if ( $mgmt_keys == 1 AND $user_info['allow_manage_keys'] == 1 ) {
                              $last_allow_key = $r['last_access_card_number'];
                              $last_deny_key = $r['last_deny_card_number'];
                         } else { $last_allow_key = $last_deny_key = '****'; };

                         if ( check_ip_acl($remote_ip, $opts_restrict_open_door_ips) == 0 )
                            { $door_link = '<a href="#" title="'.$loc_susbys_open_door.' '.$loc_common_phrase_not_accessible.' '.$loc_common_phrase_ip_not_allowed.' '.$loc_common_phrase_disabled_global_options.'" style="gray">🚪</a>'; }
                            elseif ( $user_info['allow_open_door'] == 0 )
                            { $door_link = '<a href="#" title="'.$loc_susbys_open_door.' '.$loc_common_phrase_not_accessible.' '.$loc_common_phrase_disabled_user_profile.'"  style="gray">🚪</a>'; }
                            else { $door_link = '<a href="?tab=open_door&sn='.$r['sn'].'&hw_type='.$r['hw_type'].'" title="'.$loc_susbys_open_door.'">🚪</a>'; };

                         if ( $user_info['allow_manage_controllers'] == 1 ) {
                              $edit_link = '<a href="?tab=edit_controller&sn='.$r['sn'].'&hw_type='.$r['hw_type'].'" title="'.$loc_common_phrase_edit.'">🖊</a>
                                            <a href="?tab=del_controller&sn='.$r['sn'].'&hw_type='.$r['hw_type'].'" title="'.$loc_common_phrase_del.'">🗑</a>';
                                   if ( $mgmt_keys == 1 AND $user_info['allow_manage_keys'] == 1 ) { $edit_link .= '<a href="?tab=list_queue&sn='.$r['sn'].'&hw_type='.$r['hw_type'].'" title="'.$loc_susbys_list_queue.'">💻</a>'; }
                                                     else { $edit_link .= '<a title="'.$loc_susbys_list_queue.' denied - need MANAGE_KEYS permission too" class="gray"><del>💻</del></a>'; }
                              
                            }

                         if ( $r['controller_name'] == '' ) { $r['controller_name'] .= $loc_susbys_controllers_need_name; }
                         $out .= '<tr>
<td>'.$r['hw_type'].' <B>'.$r['sn'].'</B> '.htmlspecialchars($r['office_name']).'<Br/>'.htmlspecialchars($r['controller_name']).'<Br/>
  '.$edit_link.'
  <a href="?tab=list_events&sn='.$r['sn'].'&hw_type='.$r['hw_type'].'" title="List events">📜</a>
  '.$door_link.'</td>

<td>'.$r['hw_type'].'</td> <td>
<div class="controller_data_table1" > <table><tr><td>'.$loc_common_phrase_activity.'</td><td>'.$r['last_activity'].'</td></tr>
<tr><td>&nbsp;'.$loc_property_name_access.'</td><td>'.$r['last_access_card_ts'].'   '
   .$last_allow_key.'   '.htmlspecialchars($r['allowed_user']).'   '.htmlspecialchars($r['allowed_comment']).'</td></tr>
<tr><td>&nbsp;'.$loc_property_name_reject.'</td><td>'.$r['last_deny_card_ts'].'   '
   .$last_deny_key.'   '.htmlspecialchars($r['denied_user']).'   '.htmlspecialchars($r['denied_comment']).'</td></tr>
<tr><td>&nbsp;'.$loc_susbys_open_door.', '.$loc_entity_name_button.'</td><td>'.$r['last_button_open_ts'].'</td></tr>
<tr><td>&nbsp;'.$loc_susbys_open_door.', '.$loc_entity_name_network.'</td><td>'.$r['last_network_open_ts'].'</td></tr>
 </table></div></td> </tr>';
                    }
               }

          $q2 = mysqli_query($conn, "SELECT cn.sn, cn.hw_type, cn.name AS controller_name, of.name AS office_name
   FROM controller_names cn
        LEFT JOIN offices of ON of.id = cn.office_id
   WHERE cn.sn NOT IN (SELECT ls.sn FROM last_state ls)");
  if ( mysqli_num_rows($q2) > 0 ) {
        $out .= "\n".'<tr> <td colspan="3"></td> </tr>'."\n";
        while ( $r2 = mysqli_fetch_assoc($q2) ) {

                         if ( check_ip_acl($remote_ip, $opts_restrict_open_door_ips) == 0 )
                            { $door_link = '<a href="#" title="'.$loc_susbys_open_door.', '.$loc_property_name_command.' : '.$loc_common_phrase_ip_not_allowed.'" style="gray">🚪</a>'; }
                            elseif ( $user_info['allow_open_door'] == 0 )
                            { $door_link = '<a href="#" title="'.$loc_susbys_open_door.', '.$loc_property_name_command.' : '.$loc_common_phrase_disabled_user_profile.'"  style="gray">🚪</a>'; }
                            else { $door_link = '<a href="?tab=open_door&sn='.$r2['sn'].'&hw_type='.$r2['hw_type'].'" title="'.$loc_susbys_open_door.'">🚪</a>'; };

                         if ( $user_info['allow_manage_controllers'] == 1 ) {
                              $edit_link = '<a href="?tab=edit_controller&sn='.$r2['sn'].'&hw_type='.$r2['hw_type'].'" title="'.$loc_common_phrase_edit.'">🖊</a>
                                            <a href="?tab=list_queue&sn='.$r2['sn'].'&hw_type='.$r2['hw_type'].'" title="'.$loc_susbys_list_queue.'">💻</a>
                                            <a href="?tab=del_controller&sn='.$r2['sn'].'&hw_type='.$r2['hw_type'].'" title="'.$loc_common_phrase_del.' '.$loc_entity_name_controller.' from database">🗑</a>';
                         }

              $out .= '<tr class="row_nonactive"> <td>'.$r2['hw_type'].' <B>'.htmlspecialchars($r2['sn']).'</B> '.htmlspecialchars($r2['office_name']).'<Br/>'.htmlspecialchars($r2['controller_name']).'<Br/>
              '.$edit_link.' <a href="?tab=list_events&sn='.$r2['sn'].'&hw_type='.$r2['hw_type'].'" title="'.$loc_susbys_list_events.'">📜</a>'.$door_link.'</td>
             <td>'.$r2['hw_type'].'</td> <td>'.$loc_common_phrase_not_active.'</td> </tr>';
        }
  }
          $out .= '</table>';
          break;


     case 'keys' :
          if ( $user_info['allow_manage_keys'] == 1 ) {
             if ( $mgmt_keys == 0 ) { $out .= '<font class="red">'.$loc_common_phrase_add.' '.$loc_entity_name_key.' : '.$loc_common_phrase_ip_not_allowed.'</font>'; }
                               else { $out .= '<a href="?tab=add_new_key">'.$loc_common_phrase_add.' '.$loc_entity_name_key.'</a><Br/>'; };
          };
$q1 = mysqli_query($conn, "SELECT uk.*, of.name AS office_name, lak.datetime AS last_activity, lak.status_code AS last_code,
          ec.name AS codename, ec.severity_color, ofla.name AS last_office_name
FROM `user_keys` uk
     LEFT JOIN offices of ON of.id = uk.office_id 
     LEFT JOIN `last_activity_keys` lak ON lak.`key` = uk.`key`
     LEFT JOIN event_codes ec ON ec.id = lak.status_code AND ec.hw_type = lak.controller_hw_type
     LEFT JOIN controller_names lcn ON lcn.sn = lak.controller_sn AND lcn.hw_type = lak.controller_hw_type
     LEFT JOIN offices ofla ON ofla.id = lcn.office_id
ORDER BY of.name, uk.user");
               if ( mysqli_num_rows($q1) == 0 ) { $out = $loc_menu_element_keys.' '.$loc_common_phrase_not_found; }
               else {
                    $out .= '<table>
                    <tr> <th>'.$loc_common_phrase_username.'</th> <th>'.$loc_entity_name_key.'</th>  <th>'.$loc_common_phrase_type.'</th> <th>'.$loc_property_name_access.'</th> <th>'.$loc_property_name_description.'</th> <th>'.$loc_entity_name_office.'</th> <th>'.$loc_property_name_created.'</th> <th>'.$loc_property_name_last_activity.'</th> </tr>';
                    while ( $r = mysqli_fetch_assoc($q1) ) {
                         if ($mgmt_keys == 1 AND $user_info['allow_manage_keys'] == 1) {
                                  $key = $r['key'].' <a href="?tab=edit_key&user_id='.$r['n'].'" title="'.$loc_common_phrase_edit.' '.$loc_entity_name_key.'">🖊</a>
                                                     <a href="?tab=del_key&user_id='.$r['n'].'" title="'.$loc_entity_name_controller.': '.$loc_common_phrase_del.' '.$loc_entity_name_key.'">🗑</a>';
                            } else { $key = '****'; };
                         if ( $user_info['allow_enroll_keys'] == 1 ) { $key_enroll_link = '<a href="?tab=enroll_key&user_id='.$r['n'].'" title="'.$loc_entity_name_controller.': '.$loc_common_phrase_enroll.' '.$loc_entity_name_key.'">⚙</a>'; } else { $key_enroll_link = ''; };
                         $out .= '<tr> <td><B>'.htmlspecialchars($r['user']).'</B></td>
                             <td>'.$key.$key_enroll_link.'</td>
                             <td>'.$r['type'].'</td> <td>'.tz_to_accstr($r['access'], 1).'</td>
                             <td>'.htmlspecialchars($r['comment']).'</td>
                             <td>'.htmlspecialchars($r['office_name']).'</td>
                             <td>'.substr($r['create_date'], 0, 10).'</td>
                             <td> <font ></font><a title="'.htmlspecialchars($r['codename']).' ('.$r['last_code'].') '.htmlspecialchars($r['last_office_name']).' "
                                  style="background-color: '.$r['severity_color'].'">'.$r['last_activity'].'</td></tr>';
                    }
                    $out .= '</table>';
               }
               break;

     case 'offices' :
               if ($user_info['allow_manage_offices'] == 1) { $out .= '<a href="?tab=add_new_office">'.$loc_common_phrase_add.'</a>'; };
               $q1 = mysqli_query($conn, "SELECT * FROM `offices` ORDER BY name ");
               if ( mysqli_num_rows($q1) == 0 ) { $out .= $loc_common_phrase_no_datarecords; }
               else {
                    $out .= '<table>
                    <tr> <th>'.$loc_property_name_name.'</th> <th>'.$loc_property_name_address.'</th> </tr>';
                    while ( $r = mysqli_fetch_assoc($q1) ) {
                         if ($user_info['allow_manage_offices'] == 1) { $edit_link = ' <a href="?tab=edit_office&id='.$r['id'].'">🖊</a> <a href="?tab=del_office&id='.$r['id'].'">🗑</a>'; }
                         $out .= '<tr> <td><B>'.htmlspecialchars($r['name']).$edit_link.'</B></td> <td>'.htmlspecialchars($r['address']).'</td> </tr>';
                    }
                    $out .= '</table>';
               }
               break;


     case 'add_new_office' :
          if ($user_info['allow_manage_offices'] == 1) {
               $out =  "<h3>$loc_common_phrase_add $loc_entity_name_office</h3>";
               $nname = isset ($_POST['nname']) ? mysqli_real_escape_string($conn, $_POST['nname']) : '';
               $naddr = isset ($_POST['naddr']) ? mysqli_real_escape_string($conn, $_POST['naddr']) : '';
               if ( isset($_POST['nname']) and $nname != '' ) {
                    $q2 = mysqli_query($conn, "INSERT INTO offices (`name`, `address`) VALUES ('$nname', '$naddr' ) ") or print mysqli_error($conn);
                    print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=offices">';
               }
               $out .=  '<form method="POST">
                    '.$loc_property_name_name.': <input type=text name="nname"><Br/>
                    '.$loc_property_name_address.': <input type=text name="naddr"><Br/>
                    <input type="submit" value="'.$loc_common_phrase_save.'">
               </form><Br/><Br/>
               <pre>';
          } else { $out .= "$loc_common_phrase_disabled_user_profile"; }
          break;


     case 'edit_office' :
          if ($user_info['allow_manage_offices'] == 1) {
               $out =  "<h3>$loc_common_phrase_edit $loc_entity_name_office</h3>";
               $id = isset ($_GET['id']) ? (int)$_GET['id'] : 0;
               $nname = isset ($_POST['nname']) ? mysqli_real_escape_string($conn, $_POST['nname']) : '';
               $naddr = isset ($_POST['naddr']) ? mysqli_real_escape_string($conn, $_POST['naddr']) : '';

               if ( isset($_POST['nname']) and $nname != '' ) {
                         mysqli_query($conn, "UPDATE `offices` SET `name` = '$nname', `address` = '$naddr' WHERE `id` = '$id' LIMIT 1") or print mysqli_error($conn);
                    print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=offices">';
               }
               $q1 = mysqli_query($conn, "SELECT * FROM `offices` WHERE `id` = '$id' LIMIT 1");
               $r = mysqli_fetch_assoc($q1);

               $out .=  '<form method="POST"><table class="small_table">
                    <tr> <td>'.$loc_property_name_name.': </td><td> <input type=text name="nname" value="'.htmlspecialchars($r['name']).'"> <font class="red">*</font> </td</tr>
                    <tr> <td>'.$loc_property_name_address.' </td><td> <input type=text name="naddr" value="'.htmlspecialchars($r['address']).'"> </td</tr>
                    <tr> <td colspan="2"> <input type="submit" value="'.$loc_common_phrase_save.'"> </td</tr>
               </table></form><Br/><Br/>
               <pre>';
          } else { $out .= $loc_common_phrase_disabled_user_profile; };
          break;


     case 'del_office' :
          if ($user_info['allow_manage_offices'] == 1) {
               $out =  "<h3>$loc_common_phrase_del $loc_entity_name_office</h3>";
               $id = (int)$_GET['id'];
               if ( isset($_POST['nname']) and $_POST['nname'] != '' ) {
                    try {
                         $q2 = mysqli_query($conn, "DELETE FROM `offices` WHERE `id` = '$id' LIMIT 1") or print mysqli_error($conn);
                         print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=offices">';
                         die;
                    } catch (Exception $e) { $out .= "<div class=\"red\">$loc_common_phrase_error : ". $e->getMessage().'</div>'; };
               }
               $q1 = mysqli_query($conn, "SELECT * FROM `offices` WHERE `id` = '$id' LIMIT 1") or print mysqli_error($conn);
               $r = mysqli_fetch_assoc($q1);
               $out .=  '<form method="POST">
                    '.$loc_property_name_name.': <input type="hidden" name="nname" value="'.$r['id'].'">'.htmlspecialchars($r['name']).'<Br/>
                    '.$loc_property_name_address.': '.htmlspecialchars($r['address']).'<Br/>
                    <input type="submit" value="'.$loc_common_phrase_del.'">
               </form><Br/><Br/>
               <pre>';
          } else { $out .= "$loc_common_phrase_disabled_user_profile"; }
          break;


     case 'add_controller' :
          $out =  "<h3>$loc_common_phrase_add $loc_entity_name_controller</h3>";
          if ($user_info['allow_manage_controllers'] == 1) {
               $nsn = isset ($_POST['nsn']) ? mysqli_real_escape_string($conn, $_POST['nsn']) : '';
               $ntype = isset ($_POST['ntype']) ? mysqli_real_escape_string($conn, $_POST['ntype']) : '';
               $nname = isset ($_POST['nname']) ? mysqli_real_escape_string($conn, $_POST['nname']) : '';
               
               $nhwlogin = isset ($_POST['nhwlogin']) ? mysqli_real_escape_string($conn, $_POST['nhwlogin']) : '';
               $nhwpswd = isset ($_POST['nhwpswd']) ? sha1($_POST['nhwpswd']) : '';
               $niprange = isset ($_POST['niprange']) ? mysqli_real_escape_string($conn, $_POST['niprange']) : '';
               if ( isset($_POST['nname']) AND $nname != '' AND $nsn != '' AND $ntype != '') {
                    $office_id = isset ($_POST['noffice']) ? (int)$_POST['noffice'] : '';
                    if ( $office_id == '' or $office_id == 0 ) { $office_id = 'NULL'; }
                    $q2 = mysqli_query($conn, "INSERT INTO `controller_names` (`sn`, `hw_type`, `name`, `office_id`, `hardware_login`, `hardware_password_sha1`, `allowed_ip_range`)
                    VALUES ('$nsn', '$ntype', '$nname', $office_id, '$nhwlogin', '$nhwpswd', '$niprange')") or print mysqli_error($conn);
                    print $loc_property_name_executed.'<META HTTP-EQUIV="REFRESH" CONTENT="0; URL=?tab=controllers">';
               }

               $out .=  '<form method="POST"><table class="small_table">
                    <tr> <td>'.$loc_common_phrase_sn.': </td><td> <input type=text name="nsn" placeholder="SN"> <font class="red">*</font> </td</tr>
                    <tr> <td>'.$loc_common_phrase_type.': </td><td> '.create_controller_type_select('ntype').' <font class="red">*</font> </td</tr>
                    <tr> <td>'.$loc_property_name_name.': </td><td> <input type=text name="nname" placeholder="Device name"> <font class="red">*</font> </td</tr>
                    <tr> <td>'.$loc_entity_name_office.': </td><td> '.create_office_select('noffice').' </td</tr>
                    <tr> <td>'.$loc_common_phrase_username.'/'.$loc_entity_name_controller.': </td><td> <input type=text name="nhwlogin" placeholder="NOT USED NOW"> </td</tr>
                    <tr> <td>'.$loc_common_phrase_password.'/'.$loc_entity_name_controller.' </td><td> <input type=text name="nhwpswd" placeholder="NOT USED NOW"> </td</tr>
                    <tr> <td>'.$loc_property_name_ipsubnets.' </td><td> <input type=text name="niprange" placeholder="IP ex. 10.1.0.0/16 10.15.0.0/24">  </td</tr>
                    <tr> <td colspan="2"> <input type="submit" value="'.$loc_common_phrase_save.'"> </td</tr>
               </table></form>';
          } else { $out .= $loc_common_phrase_disabled_user_profile; };
          break;


     case 'edit_controller' :
          if ($user_info['allow_manage_controllers'] == 1) {
               $sn = mysqli_real_escape_string($conn, $_GET['sn']);
               $hw_type = mysqli_real_escape_string($conn, $_GET['hw_type']);
               $out =  "<h3>$loc_common_phrase_edit $loc_entity_name_controller $loc_common_phrase_hw: $hw_type $loc_common_phrase_sn: $sn</h3>";
               $nname = isset ($_POST['nname']) ? mysqli_real_escape_string($conn, $_POST['nname']) : '';
               $nhwlogin = isset ($_POST['nhwlogin']) ? mysqli_real_escape_string($conn, $_POST['nhwlogin']) : '';
               $niprange = isset ($_POST['niprange']) ? mysqli_real_escape_string($conn, $_POST['niprange']) : '';
               if ( isset($_POST['nname']) and $nname != '' ) {
                    $office_id = isset ($_POST['noffice']) ? (int)$_POST['noffice'] : '';
                    if ( $office_id == '' or $office_id == 0 ) { $office_id = 'NULL'; }
                    $ch_exists = mysqli_query($conn, "SELECT name FROM controller_names WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1");
                    try {
                       if (mysqli_num_rows($ch_exists) == 0) {
                           $q21 = "INSERT INTO `controller_names`
                                       (`sn`, `hw_type`, `name`, `office_id`, `hardware_login`, `allowed_ip_range`)
                                VALUES ('$sn', '$hw_type', '$nname', $office_id, '$nhwlogin', '$niprange')";
                       } else { $q21 = "UPDATE `controller_names`   SET `name` = '$nname', `office_id` = $office_id,
                                                `hardware_login` = '$nhwlogin', `allowed_ip_range` = '$niprange'
                                                 WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1"; };
                       $q2 = mysqli_query($conn, $q21) or print mysqli_error($conn);
                       if ( isset ($_POST['nhwpswd']) AND $_POST['nhwpswd'] != '' ) {
                            mysqli_query($conn, "UPDATE `controller_names` SET `hardware_password_sha1` = '".sha1($_POST['nhwpswd'])."'
                               WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1") or print mysqli_error($conn);
                       }
                       $pause = 0;
                    } catch (Exception $e) {
                           $out .= "<div class=\"red\">$loc_common_phrase_error: ".$e->GetMessage()."</div>";
                           $pause = 8;
                    };
                    print '<META HTTP-EQUIV="REFRESH" CONTENT="'.$pause.';URL=?tab=controllers">';
               }
               $q1 = mysqli_query($conn, "SELECT `cn`.* FROM `controller_names` `cn` WHERE `cn`.`sn` = '$sn' AND `cn`.`hw_type` = '$hw_type' LIMIT 1");
               $r = mysqli_fetch_assoc($q1);

               $q2 = mysqli_query($conn, "SELECT DISTINCT (INET_NTOA(src_ip)) as ips FROM `events` WHERE `sn` = '$sn'  AND `hw_type` = '$hw_type' "); $ips = '';
               if ( mysqli_num_rows($q2) > 0 ) { $ips = '<font class="gray">? ';
                  while ( $r2 = mysqli_fetch_assoc($q2) ) { $ips .= $r2['ips'].' '; }
                  $ips .= '</font>';
               }

               $out .=  '<form method="POST"><table class="small_table">
                    <tr> <td>'.$loc_property_name_name.': </td><td> <input type=text name="nname" value="'.$r['name'].'"> <font class="red">*</font> </td</tr>
                    <tr> <td>'.$loc_entity_name_office.': </td><td> '.create_office_select('noffice', $r['office_id']).' </td</tr>
                    <tr> <td>'.$loc_common_phrase_username.'/'.$loc_entity_name_controller.': </td><td> <input type=text name="nhwlogin" value="'.$r['hardware_login'].'"> </td</tr>
                    <tr> <td>'.$loc_common_phrase_password.'/'.$loc_entity_name_controller.' </td><td> <input type=text name="nhwpswd" placeholder="Type for rewrite... (store as SHA1)"> </td</tr>
                    <tr> <td>'.$loc_property_name_ipsubnets.' </td><td> <input type=text name="niprange" placeholder="Type IP Range for restrict" value="'.$r['allowed_ip_range'].'"> '.$ips.' </td</tr>
                    <tr> <td colspan="2"> <input type="submit" value="'.$loc_common_phrase_save.'"> </td</tr>
               </table></form><Br/><Br/>
               <pre>';
          } else { $out .= $loc_common_phrase_disabled_user_profile; };
          break;


     case 'del_controller' :
          if ($user_info['allow_manage_controllers'] == 1) {
               $sn = mysqli_real_escape_string($conn, $_GET['sn']);
               $hw_type = mysqli_real_escape_string($conn, $_GET['hw_type']);
               $out =  "<h3>$loc_common_phrase_del $loc_entity_name_controller $loc_common_phrase_hw: $hw_type $loc_common_phrase_sn: $sn</h3>";
               $nname = isset ($_POST['nname']) ? mysqli_real_escape_string($conn, $_POST['nname']) : '';
               if ( isset($_POST['nname']) and $nname != '' ) {
                         mysqli_query($conn, "DELETE FROM events WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' ") or print mysqli_error($conn);
                         mysqli_query($conn, "DELETE FROM proxy_events WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' ") or print mysqli_error($conn);
                         mysqli_query($conn, "DELETE FROM  `controller_names` WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1") or print mysqli_error($conn);
                         mysqli_query($conn, "DELETE FROM  `last_state` WHERE `sn` = '$sn' AND `hw_type` = '$hw_type' LIMIT 1") or print mysqli_error($conn);
                    print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=controllers">';
               }
               $q1 = mysqli_query($conn, "SELECT `cn`.*,
                     (SELECT COUNT(e.id) FROM events e WHERE `e`.`sn` = '$sn' AND `e`.`hw_type` = '$hw_type' ) AS count_events,
                     (SELECT COUNT(pe.id) FROM proxy_events pe WHERE `pe`.`sn` = '$sn' AND `pe`.`hw_type` = '$hw_type' ) AS count_proxy
                      FROM `controller_names` `cn` WHERE `cn`.`sn` = '$sn' AND `cn`.`hw_type` = '$hw_type' LIMIT 1");
               if ( mysqli_num_rows($q1) == 0 ) {
                    $out .= "$loc_common_phrase_error : $loc_menu_element_controllers $loc_common_phrase_not_found";
               } else {
                    $r = mysqli_fetch_assoc($q1);

                    if ($r['count_proxy'] > 0) { $proxy_color = "red"; } else { $proxy_color = ""; };
                    $out .=  '<form method="POST">
                    <input type="hidden" name="nname" value="'.htmlspecialchars($r['name'].$r['sn']).'">
                    <table class="small_table">
                         <tr> <td>'.$loc_property_name_name.': </td>
                              <td>'.htmlspecialchars($r['name']).' </td></tr>
                         <tr> <td>'.$loc_entity_name_office.': </td>
                              <td> '.create_office_select('disabled', $r['office_id']).' </td></tr>
                         <tr> <td>'.$loc_susbys_list_events.': </td>
                              <td> '.$r['count_events'].' </td></tr>
                         <tr> <td class="'.$proxy_color.'">'.$loc_entity_name_proxyevent.': </td>
                              <td class="'.$proxy_color.'"> '.$r['count_proxy'].' </td></tr>
                         <tr> <td colspan="2"> <input type="submit" value="'.$loc_common_phrase_del.'"> </td></tr>
                    </table></form><Br/><Br/>
                    <pre>';
               };
          } else { $out .= $loc_common_phrase_disabled_user_profile; };
          break;


     case 'list_events' :
          $sn = mysqli_real_escape_string($conn, $_GET['sn']);
          $hw = mysqli_real_escape_string($conn, $_GET['hw_type']);
          $q0 = mysqli_query($conn, "SELECT cn.name AS controller_name, of.name AS office_name, cn.hw_type
             FROM `controller_names` cn
             LEFT JOIN offices of ON of.id = cn.office_id
             WHERE cn.sn = '$sn' AND cn.hw_type = '$hw' LIMIT 1");
          if ( mysqli_num_rows($q0) > 0 ) {
               $r0 = mysqli_fetch_assoc($q0);
               $out =  "<h3>$loc_susbys_list_events, ".$r0['hw_type']." ".htmlspecialchars($r0['controller_name']).", ".htmlspecialchars($r0['office_name']).", SN: ".htmlspecialchars($sn)."</h3>";
               $q1 = mysqli_query($conn, "SELECT  e.id, e.event_code, e.card, e.card_hex, e.ts, e.internal_id, ec.name, ec.severity_color, uk.type, uk.access, uk.user, INET_NTOA(src_ip) AS src_ip
                         FROM `events` e
                              LEFT JOIN event_codes ec ON ec.id = e.event_code
                              LEFT JOIN user_keys uk ON uk.key = e.card
                         WHERE e.sn = '$sn' AND e.hw_type = '$hw' ORDER BY e.ts ASC, e.id ASC");
               $out .= '<table>
               <tr> <th>iD DB</th> <th>'.$loc_property_name_code.'</th>
                    <th>'.$loc_entity_name_key.'</th>
                    <th>'.$loc_property_name_time.'</th>
                    <th>'.$loc_common_phrase_username.'</th>
                    <th>'.$loc_entity_name_event.'</th>
                    <th>IP</th>
                    <th>iD int</th> </tr>';
               while ( $r = mysqli_fetch_assoc($q1) ) {
                    if ( $r['card'] == '0000,000,00000' and $r['card_hex'] == '000000000000' ) { $key = $key_hex = ''; }
                    elseif ( $mgmt_keys == 1 AND $user_info['allow_manage_keys'] == 1) { $key = $r['card']; $key_hex = $r['card_hex']; }
                    else { $key = $key_hex = '****'; };
                    $out .= "\n".'<tr style="background-color: '.$r['severity_color'].'">
                        <td>'.$r['id']."</td>   <td>".$r['event_code'].'</td>  <td>'.$key.' '.$key_hex.'</td>  <td>'.$r['ts'].'</td>
                        <td>'.htmlspecialchars($r['user']).'</td> <td>'.htmlspecialchars($r['name']).'</td>        <td>'.$r['src_ip'].'</td>       <td>'.$r['internal_id'].'</td> </tr>';
               }
               $out .= '</table>';
          } else { $out = $loc_common_phrase_not_found; }
          break;


     case 'list_queue' :
          if (  check_ip_acl($remote_ip, $opts_restrict_manage_keys_ips) == 0 ) { $out = "$loc_common_phrase_disabled_global_options :: $loc_opts_restrict_manage_keys_ips"; }
          elseif ( $user_info['allow_manage_controllers'] == 0 ) { $out = "$loc_menu_element_controllers :: $loc_common_phrase_edit :: $loc_common_phrase_disabled_user_profile"; }
          elseif ( $user_info['allow_manage_keys'] == 0 ) { $out = "$loc_menu_element_keys :: $loc_common_phrase_edit :: $loc_common_phrase_disabled_user_profile"; }
          else {
               $sn = mysqli_real_escape_string($conn, $_GET['sn']);
               $hw = mysqli_real_escape_string($conn, $_GET['hw_type']);
               $q0 = mysqli_query($conn, "SELECT cn.name AS controller_name, of.name AS office_name, cn.hw_type
               FROM `controller_names` cn
               LEFT JOIN offices of ON of.id = cn.office_id
               WHERE cn.sn = '$sn' AND cn.hw_type = '$hw' LIMIT 1");
               if ( mysqli_num_rows($q0) > 0 ) {
                         $r0 = mysqli_fetch_assoc($q0);
                         $out =  "<h3>Queue in ".$r0['hw_type']." ".htmlspecialchars($r0['controller_name']).", ".htmlspecialchars($r0['office_name']).", SN: ".htmlspecialchars($sn)."</h3>";
                         $q1 = mysqli_query($conn, "SELECT id, sn, hw_type, command, created, executed, executer, INET_NTOA(ip) AS ip FROM `queue_commands` qc WHERE qc.sn = '$sn' AND qc.hw_type = '$hw' ORDER BY qc.id ASC");
                         $out .= '<table>
                         <tr> <th>iD</th> <th>'.$loc_property_name_command.'</th>  <th>'.$loc_property_name_created.'</th> <th>'.$loc_property_name_executed.'</th> <th>'.$loc_property_name_executor.'</th> <th>IP</th> </tr>';
                         while ( $r = mysqli_fetch_assoc($q1) ) {
                              $out .= '<tr> <td>'.$r['id'].'</td> <td>'.htmlspecialchars($r['command']).'</td> <td>'.$r['created'].'</td> <td>'.$r['executed'].'</td> <td>'.$r['executer'].'</td> <td>'.$r['ip'].'</td> </tr>';
                         }
                         $out .= '</table>';
               } else { $out = $loc_common_phrase_not_found; }
          }
          break;


     case 'add_new_key' :
          if ( $mgmt_keys == 0 ) { $out = "$loc_menu_element_keys :: $loc_common_phrase_edit :: $loc_common_phrase_ip_not_allowed :: $loc_common_phrase_disabled_global_options"; }
          elseif ( $user_info['allow_manage_keys'] == 0) { $out = "$loc_menu_element_keys :: $loc_common_phrase_edit :: $loc_common_phrase_disabled_user_profile"; }
          else {
                    $out =  "<h3>$loc_common_phrase_add $loc_entity_name_key:</h3>$loc_susbys_addkeys_help1";

          $out .=  '<pre><form method="GET" action="ha-json.php"> <input type="hidden" name="mode" value="queue-command"> <input type="hidden" name="cmd" value="add-key">
 '.$loc_property_name_code.'         : <input type=text name="newkey" pattern="^[0-9A-F]{4},[0-9]{3},[0-9]{5}$"  placeholder="XXXX,ddd,ddddd"> <Br/>
 '.$loc_common_phrase_sn.' : '.create_controller_select('sn', '').' <Br/>
 '.$loc_common_phrase_username.': <input type=text name="login"> <Br/>
       <input type="submit" value="'.$loc_common_phrase_add.' '.$loc_entity_name_key.'">
          </form><Br/><Br/>
          <pre>';
          };
          break;


     case 'edit_key' :
          $user_id = (int)$_GET['user_id'];
          if (  check_ip_acl($remote_ip, $opts_restrict_manage_keys_ips) == 0 ) { $out = "$loc_menu_element_keys :: $loc_common_phrase_edit :: $loc_common_phrase_ip_not_allowed :: $loc_common_phrase_disabled_global_options"; }
          elseif ( $user_info['allow_manage_keys'] == 0 ) { $out = "$loc_menu_element_keys :: $loc_common_phrase_edit :: $loc_common_phrase_disabled_user_profile"; } 
          else {
                 if ( isset($_POST['new_username']) ) {
                      $new_username  = mysqli_real_escape_string($conn, $_POST['new_username']);
                      $new_comment   = mysqli_real_escape_string($conn, $_POST['new_comment']);
                      $new_office_id = (int)$_POST['new_office_id'];
                      if ( $new_office_id == 0 )  { $new_office_id = 'NULL'; }
                      $new_tz = (int)$_POST['new_tz'];
                      mysqli_query($conn, "UPDATE `user_keys` SET `user` = '$new_username', `comment` = '$new_comment', `office_id` = $new_office_id, `access` = '$new_tz' WHERE `n` = '$user_id' LIMIT 1") or print mysqli_error($conn);
                      if ( mysqli_errno($conn) == 0 ) { $out = "<h3>$loc_property_name_executed</h3>"; }
                      print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=keys">';
                 }

          $q1 = mysqli_query($conn, "SELECT * FROM `user_keys` WHERE `n` = '$user_id' LIMIT 1");
          $r = mysqli_fetch_assoc($q1);
          $out = '<h3>'.$loc_common_phrase_edit.' '.$loc_entity_name_key.' #'.$user_id.', '.$loc_property_name_code.' '.$r['key'].'</h3>';

          $out .=  "\n".'<pre><form method="POST">
   User        : <input type=text name="new_username" value="'.$r['user'].'">
   '.$loc_property_name_description.'     : <input type=text name="new_comment" value="'.$r['comment'].'">
   '.$loc_entity_name_office.' : '.create_office_select('new_office_id', $r['office_id']).'
   '.$loc_property_name_access.' : <input type=text name="new_tz" value="'.$r['access'].'"> '.tz_to_accstr($r['access'], 1).'
   /* 255 - '.$loc_common_phrase_always.'; 0 - '.$loc_common_phrase_never.'; 1-127 - '.$loc_susbys_addkeys_tzhelp1.'; */

     <input type="submit" value="'.$loc_common_phrase_save.' '.$loc_entity_name_key.'">
          </form><Br/><Br/>
          </pre>';
          }
          break;

     case 'enroll_key' :
          $user_id = (int)$_GET['user_id'];
          if (  check_ip_acl($remote_ip, $opts_restrict_enroll_keys_ips) == 0 ) { $out = "$loc_menu_element_keys :: $loc_common_phrase_enroll :: $loc_common_phrase_disabled_global_options"; }
          elseif (  $user_info['allow_enroll_keys'] == 0 ) { $out = "$loc_menu_element_keys :: $loc_common_phrase_enroll :: $loc_common_phrase_disabled_user_profile"; }
          else {

          $q1 = mysqli_query($conn, "SELECT * FROM `user_keys` WHERE `n` = '$user_id' LIMIT 1");
          $r = mysqli_fetch_assoc($q1);
          $out = "<h3>$loc_common_phrase_enroll $loc_entity_name_key $user_id</h3>";

          $out .=  '<pre><form method="GET" action="ha-json.php">  <input type="hidden" name="mode" value="queue-command">  <input type="hidden" name="cmd" value="change-key">
   '.$loc_entity_name_key.'  : <input type=text name="user" value="'.htmlspecialchars($r['user']).'" readonly>    /* '.htmlspecialchars($r['comment']).' */
   '.$loc_common_phrase_sn.'   : '.create_controller_select('sn', '').'   /* Controller serial number */
   '.$loc_property_name_access.'   : <input type=text name="tz" value="'.$r['access'].'">   /* 255 full access; 0 no access; 1-127 time-regions bitmask; Current access: '.tz_to_accstr($r['access'], 1).' */

     <input type="submit" value="'.$loc_common_phrase_enroll.' '.$loc_entity_name_key.'/'.$loc_property_name_access.'">
          </form><Br/><Br/>
          </pre>'.$loc_susbys_addkeys_tzhelp1.': <Br/>
<img src="rasp.png" alt="TZ" />';
          }
          break;


     case 'del_key' :
          $user_id = (int)$_GET['user_id'];
          if ( $mgmt_keys == 0 ) { $out = "$loc_menu_element_keys :: $loc_common_phrase_del :: $loc_common_phrase_disabled_global_options"; }
          elseif ( $user_info['allow_manage_keys'] == 0 ) { $out = "$loc_menu_element_keys :: $loc_common_phrase_del :: $loc_common_phrase_disabled_user_profile"; }
          else {

          $q1 = mysqli_query($conn, "SELECT * FROM `user_keys` WHERE `n` = '$user_id' LIMIT 1");
          $r = mysqli_fetch_assoc($q1);
          $out = "<h3>$loc_common_phrase_del $loc_entity_name_key $user_id @ $loc_entity_name_controller</h3>".$r['key'];

          $out .=  '<pre><form method="GET" action="ha-json.php"> <input type="hidden" name="mode" value="queue-command"> <input type="hidden" name="cmd" value="del-key">
   '.$loc_entity_name_key.'  : <input type=text name="user" value="'.htmlspecialchars($r['user']).'" readonly>  /* '.htmlspecialchars($r['comment']).' */
   '.$loc_common_phrase_sn.'   : '.create_controller_select('sn', '').'   /* Select controller serial number */

   DB   : <a href="?tab=del_key_db&user_id='.$user_id.'"> '.$loc_susbys_delkeys_help1.'</a>
   <input type="submit" value="'.$loc_entity_name_controller.': '.$loc_common_phrase_del.' '.$loc_entity_name_key.'">
          </form><Br/><Br/>
          </pre>';
          }
          break;


     case 'del_key_db' :
          $user_id = (int)$_GET['user_id'];
          if ( $mgmt_keys == 0 ) { $out = "$loc_menu_element_keys :: $loc_common_phrase_del :: $loc_common_phrase_disabled_global_options"; }
          elseif ( $user_info['allow_manage_keys'] == 0 ) { $out = "$loc_menu_element_keys :: $loc_common_phrase_del :: $loc_common_phrase_disabled_user_profile"; }
          else {

          $q1 = mysqli_query($conn, "SELECT * FROM `user_keys` WHERE `n` = '$user_id' LIMIT 1");
          $r = mysqli_fetch_assoc($q1);
          $out = "<h3>$loc_common_phrase_del $loc_entity_name_key $user_id @ $loc_menu_element_controllers</h3>\n"
                 .$r['key']." - $loc_susbys_delkeys_help1<Br/>$loc_susbys_delkeys_help2";

          $out .=  '<pre><form method="GET" action="ha-json.php"> <input type="hidden" name="mode" value="queue-command"> <input type="hidden" name="cmd" value="del-key-from-db">
   '.$loc_entity_name_key.'  : <input type=text name="user" value="'.htmlspecialchars($r['user']).'" readonly>  /* '.htmlspecialchars($r['comment']).' */

   <input type="submit" value="'.$loc_menu_element_controllers.': '.$loc_common_phrase_del.' '.$loc_entity_name_key.'">
          </form><Br/><Br/>
          </pre>';
          }
          break;

     case 'open_door' :
          $sn = mysqli_real_escape_string($conn, $_GET['sn']);
          if (  check_ip_acl($remote_ip, $opts_restrict_open_door_ips) == 0 ) { $out = "$loc_common_phrase_ip_not_allowed :: $loc_common_phrase_disabled_global_options"; } else {

          $out = "<h3>$loc_susbys_open_door</h3>
        <script type=\"text/javascript\">
window.onload = function() {
   document.getElementById('actionButton').style.display = 'block';
};

	function showBlock(element) {
	        data = '';
                el = document.getElementById(element);
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open('GET', 'ha-json.php?mode=queue-command&cmd=open-door&sn=$sn', true);
                xmlHttp.send(null);
                xmlHttp.onreadystatechange = function() {
                      if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
                      data = xmlHttp.responseText;
                      if ( data != '' ) {
                         json1 =  JSON.parse(data)
                         el.textContent = json1['msg'];
                         console.log(json1['msg'])
                      }
                }

                if ( el != null) {
                         el.style.display = 'block';
                         setTimeout(() => { el.style.display = 'none'; }, 5000);
                }
	    }
	</script>";

          $out .=  '<pre><form method="GET" action="ha-json.php"> <input type="hidden" name="mode" value="queue-command"> <input type="hidden" name="cmd" value="open-door">
          '.$loc_common_phrase_sn.' : <input type="text" name="sn" value="'.$sn.'" readonly>
          <input id="actionButton" type="button" value="'.$loc_susbys_open_door.'" onclick="showBlock(\'myShowBlock\')" style="display: none;" >
          <noscript> <input type="submit" value="'.$loc_susbys_open_door.'-NO-JS"> </noscript>
          </form></pre>
          <div class="js_window1" id="myShowBlock" style="display: none;"></div>';
          }
          break;


     case 'logins' :
               if ($user_info['allow_manage_logins'] == 1) { $out .= '<a href="?tab=add_new_login">'.$loc_common_phrase_add.'</a>';
                    $q1 = mysqli_query($conn, "SELECT * FROM `logins` ");
                    if ( mysqli_num_rows($q1) == 0 ) { $out = "$loc_menu_element_logins :: $loc_common_phrase_not_found"; }
                    else {
                         $out .= $loc_susbys_logins_help1.'<table>
                         <tr> <th>'.$loc_common_phrase_username.'</th>  <th>'.$loc_property_name_enable.'</th> <th>'.$loc_property_name_accessrights.'</th> <th>'.$loc_property_name_description.'</th> <th>'.$loc_property_name_created.'</th> <th>'.$loc_property_name_last_activity.'</th> </tr>'."\n";
                         while ( $r = mysqli_fetch_assoc($q1) ) {
                              $rights = '';
                              if ($r['allow_open_door'] == 1) { $rights .= '<a title="'.$loc_susbys_open_door.', '.$loc_common_phrase_username.'+'.$loc_common_phrase_password.'">🚪</a>'; }
                              if ($r['allow_manage_controllers'] == 1) { $rights  .= '<a title="'.$loc_menu_element_controllers.', '.$loc_common_phrase_manage.'">💻</a>'; }
                              if ($r['allow_manage_keys'] == 1) { $rights .= '<a title="'.$loc_menu_element_keys.', '.$loc_common_phrase_manage.'">🔑</a>'; }
                              if ($r['allow_enroll_keys'] == 1) { $rights .= '<a title="'.$loc_menu_element_keys.', '.$loc_common_phrase_enroll.'">🔐</a>'; }
                              if ($r['allow_manage_badkeys'] == 1) { $rights .= '<a title="'.$loc_menu_element_badkeys.', '.$loc_common_phrase_manage.'">🗝</a>'; }
                              if ($r['allow_manage_offices'] == 1) { $rights .= '<a title="'.$loc_menu_element_offices.', '.$loc_common_phrase_manage.'">🏢</a>'; }
                              if ($r['allow_manage_logins'] == 1) { $rights  .= '<a title="'.$loc_menu_element_logins.', '.$loc_common_phrase_manage.'">👨</a>'; }
                              if ($r['allow_manage_options'] == 1) { $rights  .= '<a title="'.$loc_menu_element_options.', '.$loc_common_phrase_manage.'">⚙</a>'; }
                              if ($r['allow_manage_proxy_events'] == 1) { $rights  .= '<a title="'.$loc_menu_element_proxy_events.', '.$loc_common_phrase_manage.'">X</a>'; }
                              
                              $out .= '<tr> <td><B>'.htmlspecialchars($r['user']).'</B> <a href="?tab=edit_login&login_id='.$r['id'].'">🖊</a> <a href="?tab=del_login&login_id='.$r['id'].'">🗑</a> </td>
                              <td>'.int2checkbox($r['enable'], '', 1).'</td> <td>'.$rights.'</td> <td>'.htmlspecialchars($r['comment']).'</td> <td>'.$r['created_ts'].'</td> <td>'.$r['last_used_ts'].'</td> </tr>'."\n";
                         }
                         $out .= '</table>';
                    }
               } else { $out .= $loc_common_phrase_disabled_user_profile.'<META HTTP-EQUIV="Refresh" CONTENT="0; URL=?tab=profile">'; };
               break;


     case 'add_new_login' :
          if ($user_info['allow_manage_logins'] == 1) {
               $out =  "<h3>$loc_common_phrase_add $loc_common_phrase_username</h3>";
               $nname = isset ($_POST['nname']) ? mysqli_real_escape_string($conn, $_POST['nname']) : '';
               if ( isset($_POST['nname']) and $nname != '' ) {
                    $q2 = mysqli_query($conn, "INSERT INTO logins (`user`, `password_sha256`, `created_ts`, `last_changed_password_ts`, `enable`)
                        VALUES ('$nname', '', NOW(), NOW(), '1' ) ") or print mysqli_error($conn);
                    $last_id = mysqli_insert_id($conn);
                    print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=edit_login&login_id='.$last_id.'">';
               }
               $out .=  '<form method="POST">
                    '.$loc_common_phrase_username.': <input type=text name="nname"><Br/>
                    <input type="submit" value="'.$loc_common_phrase_add.'">
               </form><Br/><Br/>
               <pre>';
          } else { $out .= "$loc_common_phrase_disabled_user_profile"; }
          break;


     case 'edit_login' :
          if ($user_info['allow_manage_logins'] == 1) {
               $id = (int)$_GET['login_id'];
               $out =  "<h3>$loc_common_phrase_edit $loc_common_phrase_username #$id</h3>";
               $nname = isset ($_POST['nname']) ? mysqli_real_escape_string($conn, $_POST['nname']) : '';
               $nemail = isset ($_POST['nemail']) ? mysqli_real_escape_string($conn, $_POST['nemail']) : '';
               $ncomm = isset ($_POST['ncomm']) ? mysqli_real_escape_string($conn, $_POST['ncomm']) : '';
               $twofac = isset ($_POST['f_twofac']) ? mysqli_real_escape_string($conn, $_POST['f_twofac']) : '';
                 if ($twofac == 'none') { $twofac = ''; }
               $allipr = isset ($_POST['f_allowed_ip_range']) ? mysqli_real_escape_string($conn, $_POST['f_allowed_ip_range']) : '';
               
               if ( isset($_POST['nname']) and $nname != '' ) {
                    $subsql = '';
                    if ($_POST['npswd'] != '' AND $_POST['npswd'] == $_POST['npswd2']) {
                                   print "$loc_common_phrase_edit $loc_common_phrase_password ...<Br/>";
                                   $salt1 = generate_random_password(64, 'abcdefghijkmnoprstuvxyzABCDEFGHJKLMNPQRSTUVXYZ23456789_');
                                   $salt2 = generate_random_password(64, 'abcdefghijkmnoprstuvxyzABCDEFGHJKLMNPQRSTUVXYZ23456789_');
                                   $new_sha256 = hash('sha256', $salt1.$_POST['npswd'].$salt2);
                                   $subsql .= ", `salt1` = '$salt1', `salt2` = '$salt2', `password_sha256` = '$new_sha256', `last_changed_password_ts` = NOW()";
                    }
                    if ($_POST['f_enable'] == 'on')           { $enable = 1; } else { $enable = 0; };
                    if ($_POST['f_allow_open_door'] == 'on')           { $aopendoor = 1; } else { $aopendoor = 0; };
                    if ($_POST['f_allow_manage_controllers'] == 'on')  { $amgmcontr = 1; } else { $amgmcontr = 0; };
                    if ($_POST['f_allow_manage_keys'] == 'on')         { $amgmtkeys = 1; } else { $amgmtkeys = 0; };
                    if ($_POST['f_allow_enroll_keys'] == 'on')         { $aenrlkeys = 1; } else { $aenrlkeys = 0; };
                    if ($_POST['f_allow_manage_badkeys'] == 'on')      { $amgmbkeys = 1; } else { $amgmbkeys = 0; };
                    if ($_POST['f_allow_manage_offices'] == 'on')      { $amgmtoffi = 1; } else { $amgmtoffi = 0; };
                    if ($_POST['f_allow_manage_logins'] == 'on')       { $amgmtlogi = 1; } else { $amgmtlogi = 0; };
                    if ($_POST['f_allow_manage_options'] == 'on')      { $amgmtopts = 1; } else { $amgmtopts = 0; };
                    if ($_POST['f_allow_manage_proxy_events'] == 'on') { $amgmtprox = 1; } else { $amgmtprox = 0; };

                    $q2 = mysqli_query($conn, "UPDATE logins SET `user` = '$nname', `email` = '$nemail', `enable` = '$enable', `comment` = '$ncomm', `allowed_ip_range` = '$allipr', `twofactor_method` = '$twofac',
                          `allow_open_door` = '$aopendoor',  `allow_manage_controllers` = '$amgmcontr', `allow_manage_keys` = '$amgmtkeys', `allow_enroll_keys` = '$aenrlkeys',
                          `allow_manage_badkeys` = '$amgmbkeys', `allow_manage_offices` = '$amgmtoffi', `allow_manage_logins` = '$amgmtlogi', `allow_manage_options` = '$amgmtopts',
                          `allow_manage_proxy_events` = '$amgmtprox'   $subsql
                    WHERE `id` = '$id' LIMIT 1") or print mysqli_error($conn);
                    print '<META HTTP-EQUIV="REFRESH" CONTENT="3;URL=?tab=logins">';
               }
               $q1 = mysqli_query($conn, "SELECT * FROM `logins` WHERE `id` = '$id' LIMIT 1");
               $r = mysqli_fetch_assoc($q1);

               $out .=  '<form method="POST">
                    '.$loc_common_phrase_username.': <input type=text name="nname" value="'.$r['user'].'"> <a title="'.$loc_property_name_created.': '.$r['created_ts'].'; '."\n".$loc_property_name_last_activity.': '.$r['last_used_ts'].';">?</a><Br/>
                    2FA: '.create_twofactor_type_select('f_twofac', $r['twofactor_method']).' <Br/>
                    '.$loc_common_phrase_email_address.':  <input type=text name="nemail" value="'.$r['email'].'"> <Br/>
                    '.$loc_property_name_description.':  <input type=text name="ncomm" value="'.$r['comment'].'"> <Br/>
                    '.int2checkbox ($r['enable'], 'f_enable', 0, '', "$loc_common_phrase_username, $loc_property_name_enable" ).'<Br/>
                    '.$loc_common_phrase_password.': <input type="password" name="npswd"> <a title="LAST CHANGED '.$r['last_changed_password_ts'].'">?</a> <Br/>
                    '.$loc_common_phrase_password.'(2): <input type="password" name="npswd2"> <Br/>
                    '.$loc_property_name_ipsubnets.': <input type=text name="f_allowed_ip_range" value="'.$r['allowed_ip_range'].'">  <Br/>
                    '.int2checkbox ($r['allow_open_door'], 'f_allow_open_door', 0, '', "$loc_property_name_access : $loc_susbys_open_door" ).' <Br/>
                    '.int2checkbox ($r['allow_manage_controllers'], 'f_allow_manage_controllers', 0, '', "$loc_property_name_access : $loc_common_phrase_manage, $loc_menu_element_controllers" ).'  <Br/>
                    '.int2checkbox ($r['allow_manage_keys'], 'f_allow_manage_keys', 0, '', "$loc_property_name_access : $loc_common_phrase_manage, $loc_menu_element_keys" ).'<Br/>
                    '.int2checkbox ($r['allow_enroll_keys'], 'f_allow_enroll_keys', 0, '', "$loc_property_name_access : $loc_common_phrase_enroll, $loc_menu_element_keys/$loc_menu_element_controllers" ).'  <Br/>
                    '.int2checkbox ($r['allow_manage_badkeys'], 'f_allow_manage_badkeys', 0, '', "$loc_property_name_access : $loc_common_phrase_manage, $loc_menu_element_badkeys" ).'   <Br/>
                    '.int2checkbox ($r['allow_manage_offices'], 'f_allow_manage_offices', 0, '', "$loc_property_name_access : $loc_common_phrase_manage, $loc_menu_element_offices" ).'   <Br/>
                    '.int2checkbox ($r['allow_manage_logins'], 'f_allow_manage_logins', 0, '', "$loc_property_name_access : $loc_common_phrase_manage, $loc_menu_element_logins" ).'  <Br/>
                    '.int2checkbox ($r['allow_manage_options'], 'f_allow_manage_options', 0, '', "$loc_property_name_access : $loc_common_phrase_manage, $loc_menu_element_options" ).'   <Br/>
                    '.int2checkbox ($r['allow_manage_proxy_events'], 'f_allow_manage_proxy_events', 0, '', "$loc_property_name_access : $loc_common_phrase_manage, $loc_menu_element_proxy_events" ).'  <Br/>
                    <input type="submit" value="'.$loc_common_phrase_save.'">
               </form><Br/><Br/>
               <pre>';
          } else { $out .= "$loc_common_phrase_disabled_user_profile"; }
          break;




     case 'del_login' :
          if ($user_info['allow_manage_logins'] == 1) {
               $out =  "<h3>$loc_common_phrase_del $loc_common_phrase_username</h3>";
               $id = (int)$_GET['login_id'];
               if ( isset($_POST['nname']) and $_POST['nname'] != '' ) {
                    $q2 = mysqli_query($conn, "DELETE FROM `logins` WHERE `id` = '$id' LIMIT 1") or print mysqli_error($conn);
                    print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=logins">';
               }
               $q1 = mysqli_query($conn, "SELECT * FROM `logins` WHERE `id` = '$id' LIMIT 1");
               $r = mysqli_fetch_assoc($q1);
               $out .=  '<form method="POST">
                    '.$loc_common_phrase_username.': <input type="hidden" name="nname" value="'.$r['id'].'">'.htmlspecialchars($r['user']).'<Br/>
                    <input type="submit" value="'.$loc_common_phrase_del.'">
               </form><Br/><Br/>
               <pre>';
          } else { $out .= "$loc_common_phrase_disabled_user_profile"; }
          break;


     case 'badkeys' :
               if ($user_info['allow_manage_badkeys'] == 1) { $out .= '<a href="?tab=add_badkey">'.$loc_common_phrase_add.'</a>'; };
               $q1 = mysqli_query($conn, "SELECT * FROM `bad_keys` ");
               if ( mysqli_num_rows($q1) == 0 ) { $out = "$loc_menu_element_badkeys $loc_common_phrase_not_found"; }
               else {
                    $out .= $loc_susbys_badkeys_help1.'<table>
                    <tr> <th>'.$loc_property_name_code.'</th> <th>'.$loc_property_name_code.' (HEX)</th> <th>'.$loc_property_name_description.'</th> <th>'.$loc_property_name_enable.'</th> </tr>';
                    while ( $r = mysqli_fetch_assoc($q1) ) {
                         if ($user_info['allow_manage_badkeys'] == 1) {
                               $edit_link = ' <a href="?tab=edit_badkey&id='.$r['card'].'">🖊</a>  <a href="?tab=del_badkey&id='.$r['card'].'">🗑</a> ';
                         }
                         $out .= '<tr> <td><B>'.$r['card'].$edit_link.'</B></td>
                            <td>'.$r['card_hex'].'</td>
                            <td>'.htmlspecialchars($r['description']).'</td>
                            <td>'.int2checkbox($r['active'], '', 1).'</td> </tr>';
                    }
                    $out .= '</table>';
               }
               break;


     case 'add_badkey' :
          if ($user_info['allow_manage_badkeys'] == 1) {
               $out =  "<h3>$loc_common_phrase_add $loc_entity_name_badkey</h3>";
               $nname = isset ($_POST['nname']) ? mysqli_real_escape_string($conn, $_POST['nname']) : '';
               $ncode = isset ($_POST['ncode']) ? mysqli_real_escape_string($conn, $_POST['ncode']) : '';
               if ( isset($_POST['ncode']) and $ncode != '' ) {
                    $hex = pure_hex_2_mixed_hex_marine($ncode);
                    $q2 = mysqli_query($conn, "INSERT INTO bad_keys (`card`, `card_hex`, `description`, `active`) VALUES ('$ncode', '$hex', '$nname', '1' ) ") or print mysqli_error($conn);
                    print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=badkeys">';
               }
               $out .=  '<form method="POST">
                    '.$loc_property_name_code.': <input type=text name="ncode"><Br/>
                    '.$loc_property_name_description.': <input type=text name="nname"><Br/>
                    <input type="submit" value="'.$loc_common_phrase_add.'">
               </form><Br/><Br/>
               <pre>';
          } else { $out .= "$loc_common_phrase_disabled_user_profile"; }
          break;


     case 'edit_badkey' :
          if ($user_info['allow_manage_badkeys'] == 1) {
               $id = isset ($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';
               $ncode = isset ($_POST['ncode']) ? mysqli_real_escape_string($conn, $_POST['ncode']) : '';
               $ndesc = isset ($_POST['ndesc']) ? mysqli_real_escape_string($conn, $_POST['ndesc']) : '';

               if ( isset($_POST['ncode']) and $ncode != '' ) {
                         if ( isset ($_POST['nenab']) AND $_POST['nenab'] == 'on') { $enable = 1; } else { $enable = 0; };
                         $hex = mixed_hex_marine_2_pure_hex($ncode);
                         mysqli_query($conn, "UPDATE `bad_keys` SET `card` = '$ncode', `card_hex` = '$hex', `description` = '$ndesc', `active` = '$enable' WHERE `card` = '$id' LIMIT 1") or print mysqli_error($conn);
                    print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=badkeys">';
                    die;
               }
               $out =  "<h3>$loc_common_phrase_edit $loc_entity_name_badkey</h3>";
               $q1 = mysqli_query($conn, "SELECT * FROM `bad_keys` WHERE `card` = '$id' LIMIT 1");
               $r = mysqli_fetch_assoc($q1);

               $out .=  '<form method="POST"><table class="small_table">
                    <tr> <td>'.$loc_property_name_code.': </td><td> <input type=text name="ncode" value="'.htmlspecialchars($r['card']).'"> <font class="red">*</font> </td</tr>
                    <tr> <td>'.$loc_property_name_code.' (HEX): </td><td> '.htmlspecialchars($r['card_hex']).'</td</tr>
                    <tr> <td>'.$loc_property_name_description.' </td><td> <input type=text name="ndesc" value="'.htmlspecialchars($r['description']).'"> </td</tr>
                    <tr> <td>'.$loc_property_name_enable.' </td><td> '.int2checkbox ($r['active'], 'nenab', 0, '', $loc_property_name_enable ).' </td</tr>
                    <tr> <td colspan="2"> <input type="submit" value="'.$loc_common_phrase_save.'"> </td</tr>
               </table></form><Br/><Br/>
               <pre>';
          } else { $out .= $loc_common_phrase_disabled_user_profile; };
          break;


     case 'del_badkey' :
          if ($user_info['allow_manage_badkeys'] == 1) {
               $out =  "<h3>$loc_common_phrase_del $loc_entity_name_badkey</h3>";
               $id = (int)$_GET['id'];
               if ( isset($_POST['nname']) and $_POST['nname'] != '' ) {
                    try {
                         $q2 = mysqli_query($conn, "DELETE FROM `bad_keys` WHERE `card` = '$id' LIMIT 1") or print mysqli_error($conn);
                         print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=badkeys">';
                         die;
                    } catch (Exception $e) { $out .= "<div class=\"red\">$loc_common_phrase_error : ". $e->getMessage().'</div>'; };
               }
               $q1 = mysqli_query($conn, "SELECT * FROM `bad_keys` WHERE `card` = '$id' LIMIT 1") or print mysqli_error($conn);
               $r = mysqli_fetch_assoc($q1);
               $out .=  '<form method="POST">
                    '.$loc_property_name_code.': <input type="hidden" name="nname" value="'.$r['card'].'">'.htmlspecialchars($r['card']).'<Br/>
                    '.$loc_property_name_code.' (HEX): <input type="hidden" name="nname" value="'.$r['card'].'">'.htmlspecialchars(mixed_hex_marine_2_pure_hex($r['card'])).'<Br/>
                    '.$loc_property_name_description.': '.htmlspecialchars($r['description']).'<Br/>
                    <input type="submit" value="'.$loc_common_phrase_del.'">
               </form><Br/><Br/>
               <pre>';
          } else { $out .= "$loc_common_phrase_disabled_user_profile"; }
          break;


     case 'options' :
               $cx = 0;
               $options_res = mysqli_query ($conn, "SELECT * FROM options ORDER BY abbr DESC");
               if (  isset($_POST["f_hid"]) and $_POST["f_hid"] !='' AND $user_info['allow_manage_options'] == 1 ) {   /* START НАЛИЧИЕ НАЖАТИЯ НА КНОПКУ */
                    $out .= "$loc_button_save_changes ... ";
                    while ($row = mysqli_fetch_array($options_res)) {   /* START ПАРСИНГ ОПЦИЙ */
                         $id = $row["id"]; $cu_value = isset($_POST["twk_$id"]) ? $_POST["twk_$id"] : '';
                         if ( $row["type"] == 'b' ) {   if ( $cu_value == 'on' ) { $cu_value = 1 ; } ELSE { $cu_value = 0 ; };   }
                         if ( $row["type"] == 'i' ) { $cu_value = (int) $cu_value ; }
                         if ( $cu_value != $row["value"] ) {
                                   $cx++;
                                   $cu_value = mysqli_real_escape_string ($conn, $cu_value);
                                   $upd_query = "UPDATE options SET `value` = '$cu_value' WHERE `id` = '".$row["id"]."' LIMIT 1";
                                   mysqli_query ( $conn, $upd_query );
                         }
                    }   /* END ПАРСИНГ ОПЦИЙ */
                    $out .= '<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=?tab=options">';
               }   /* END НАЛИЧИЕ НАЖАТИЯ НА КНОПКУ */
               else {
                    $out .= '<Br><form method="post" action="?tab=options"><table><tr><th>'.$loc_options_parameter.'</th> <th>'.$loc_options_value.' (64)</th> <th>'.$loc_options_description."</th></tr>\n";
                    while ($row = mysqli_fetch_array($options_res)) {   $cu_form_type = $row["type"];

                         $db_option_abbr = $row["abbr"];
                         $db_option_name = $db_option_abbr;
                         $cu_loc_opt_name = "loc_$db_option_abbr";
                         if ( isset (${"loc_$db_option_abbr"}) and $cu_loc_opt_name != 'loc_') { $db_option_name = ${$cu_loc_opt_name}; }

                         switch ($cu_form_type) {
                              case 'b' : {
                                             if ( $row["value"] == 1 ) { $stc = ' checked'; } ELSE { $stc = ''; };
                                             $cu_form_element = '<INPUT type="checkbox" id="twid_'.$row["id"].'" name="twk_'.$row["id"].'" title="'.$loc_options_checkbox_hint.'"'.$stc.'>';
                                             $cu_form_name = '<label for="twid_'.$row["id"].'">'.$db_option_name.'</label>';
                                   break; }
                              case 'i' : {
                                             $cu_form_element = '<INPUT type="text" name="twk_'.$row["id"].'" maxlength="5"  class="req" size="5"
                                                  value="'.htmlspecialchars(stripslashes($row["value"]), ENT_QUOTES).'" title="'.$loc_options_numberinput_hint.'">';
                                             $cu_form_name = $db_option_name;
                                   break; }
                              case 't' : {
                                             $cu_form_element = '<INPUT type="text" name="twk_'.$row["id"].'" maxlength="50" size="30" value="'.htmlspecialchars(stripslashes($row["value"]), ENT_QUOTES).'">';
                                             $cu_form_name = $db_option_name;
                                   break; }
                         };
                         if ( $user_info['allow_manage_options'] == 0 ) { $cu_form_element = '[HIDDEN]'; $cu_form_name = "$loc_property_name_reject : $loc_menu_element_options, $loc_common_phrase_manage ($loc_common_phrase_disabled_user_profile)"; };
                         $out .= "\n<tr><td>".$db_option_abbr."</td>\n    <td>".$cu_form_element."</td>\n    <td>".$cu_form_name.'</td></tr>';
                    }
                    $out .= '</table>
                    <INPUT type="hidden" name="f_hid" value="yes"> <Br/>';
                    if ( $user_info['allow_manage_options'] == 1 ) { $out .='<INPUT type="submit" value="'.$loc_button_save_changes.'" onclick="this.disabled = true; submit();"> <Br/>'; };
                    $out .= '</form>';
               };
         break;


     case 'proxy_events' :
          if ($user_info['allow_manage_proxy_events'] == 1) { $out = '<a href="?tab=add_new_proxy_event">'.$loc_common_phrase_add.'</a><Br/>'; }
          $q1 = mysqli_query($conn, "SELECT `pe`.*, ec.name AS event_name, ec.severity_color, cn.name AS controller_name
              FROM `proxy_events` `pe`
                LEFT JOIN event_codes ec ON ec.id = pe.event_code AND ec.hw_type=pe.hw_type
                LEFT JOIN  controller_names cn ON cn.sn = pe.sn AND cn.hw_type = pe.hw_type");
          if ( mysqli_num_rows($q1) == 0 ) { $out .= "$loc_menu_element_proxy_events : $loc_common_phrase_no_datarecords"; }
          else {
                    $out .= '<table>
                    <tr> <th>'.$loc_common_phrase_sn.'</th> <th>'.$loc_entity_name_event.'</th> <th>'.$loc_property_name_enable.'</th> <th>'.$loc_property_name_description.'</th> <th>'.$loc_property_name_created.'</th> <th>'.$loc_property_name_url.'</th> </tr>'."\n";
                    while ( $r = mysqli_fetch_assoc($q1) ) {
                         if ($user_info['allow_manage_proxy_events'] == 1) {
                              $edit_link = '<a href="?tab=edit_proxy_event&proxy_id='.$r['id'].'">🖊</a>
                                            <a href="?tab=del_proxy_event&proxy_id='.$r['id'].'">🗑</a>';
                              $tds = ' <td>'.htmlspecialchars($r['comment']).'</td> <td>'.$r['created'].'</td> <td>'.htmlspecialchars($r['target_url']).'</td>';
                         } else { $tds = ' <td colspan="3"> ??? need manage_proxy_events permission ??? </td>'; };
                         $out .= '<tr> <td><B>'.$r['hw_type'].' '.$r['sn'].' '.htmlspecialchars($r['controller_name']).'</B> '.$edit_link.' </td>
                         <td style="background-color: '.$r['severity_color'].'">'.$r['event_code'].' '.htmlspecialchars($r['event_name']).'</td>
                         <td>'.int2checkbox($r['enable'], '', 1).'</td> '.$tds.' </tr>'."\n";
                    }
                    $out .= '</table>';
          }
          break;


     case 'add_new_proxy_event' :
          $out =  "<h3>$loc_common_phrase_add $loc_entity_name_proxyevent</h3>";
          if ($user_info['allow_manage_proxy_events'] == 1) {
               $nsn = isset ($_POST['nsn']) ? mysqli_real_escape_string($conn, $_POST['nsn']) : '';
               $ntype = isset ($_POST['ntype']) ? mysqli_real_escape_string($conn, $_POST['ntype']) : '';
               $nname = isset ($_POST['nname']) ? mysqli_real_escape_string($conn, $_POST['nname']) : '';
               $ncode = isset ($_POST['ncode']) ? mysqli_real_escape_string($conn, $_POST['ncode']) : '';

               if ( isset($_POST['nsn']) AND $nsn != '' AND $ncode != '' AND $ntype != '') {
                    $nenable = isset ($_POST['nenable']) ? $_POST['nenable'] : '';
                    if ( $nenable == 'on' ) { $nenable = '1'; } else { $nenable = '0'; }
                    $nurl = isset ($_POST['nurl']) ? mysqli_real_escape_string($conn, $_POST['nurl']) : '';
                    $nmethod = isset ($_POST['nmethod']) ? mysqli_real_escape_string($conn, $_POST['nmethod']) : '';
                    $nbody = isset ($_POST['nbody']) ? mysqli_real_escape_string($conn, $_POST['nbody']) : '';
                    $ncontenttype = isset ($_POST['ncontenttype']) ? mysqli_real_escape_string($conn, $_POST['ncontenttype']) : '';
                    $q2 = mysqli_query($conn, "INSERT INTO `proxy_events` (`sn`, `hw_type`, `enable`, `event_code`, `comment`, `target_url`, `target_method`, `target_raw_body`, `target_content_type`, `created`)
                    VALUES ('$nsn', '$ntype', '$nenable', $ncode, '$nname', '$nurl', '$nmethod', '$nbody', '$ncontenttype', NOW())") or print mysqli_error($conn);
                    print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=proxy_events">';
                    die();
               }

               $out .=  '<form method="POST"><table class="small_table">
                    <tr> <td>'.$loc_common_phrase_sn.': </td><td> '.create_controller_select('nsn').' <font class="red">*</font> </td</tr>
                    <tr> <td>'.$loc_common_phrase_type.': </td><td> '.create_controller_type_select('ntype').' <font class="red">*</font> </td</tr>
                    <tr> <td>'.$loc_property_name_code.': </td><td> '.create_event_code_select('ncode').' <font class="red">*</font> </td</tr>
                    <tr> <td>'.$loc_property_name_description.': </td><td> <input type=text name="nname" placeholder="info..."> </td</tr>
                    <tr> <td>'.$loc_property_name_enable.': </td><td> '.int2checkbox(0, 'nenable', 0, '', 'Вкл').' </td</tr>

                    <tr> <td>'.$loc_property_name_url.': </td><td> <input type=text name="nurl" placeholder="site.net/my.php?device=[SN]&event=[EVENT_CODE]...&"> [SN] [HWTYPE] [EVENT_ID] [EVENT_CODE] [CARD] [CARD_HEX] [DATETIME] [LOGIN] [OFFICE] [IP]</td></tr>
                    <tr> <td>http-method </td><td> '.create_http_method_select('nmethod').' </td</tr>
                    <tr> <td>raw-body </td><td> <input type=text name="nbody" placeholder="">  </td</tr>
                    <tr> <td> content-type </td><td> <input type=text name="ncontenttype" placeholder="header value..."> </td</tr>
                    <tr> <td colspan="2"> <input type="submit" value="'.$loc_common_phrase_add.'"> </td</tr>
               </table></form>';
          } else { $out .= $loc_common_phrase_disabled_user_profile; };
          break;


     case 'edit_proxy_event' :
          $out =  "<h3>$loc_common_phrase_edit $loc_entity_name_proxyevent</h3>";
          $proxy_id = (int)$_GET['proxy_id'];
          if ($user_info['allow_manage_proxy_events'] == 1) {
               $nsn = isset ($_POST['nsn']) ? mysqli_real_escape_string($conn, $_POST['nsn']) : '';
               $ntype = isset ($_POST['ntype']) ? mysqli_real_escape_string($conn, $_POST['ntype']) : '';
               $nname = isset ($_POST['nname']) ? mysqli_real_escape_string($conn, $_POST['nname']) : '';
               $ncode = isset ($_POST['ncode']) ? mysqli_real_escape_string($conn, $_POST['ncode']) : '';

               if ( isset($_POST['nsn']) AND $nsn != '' AND $ncode != '' AND $ntype != '') {
                    $nenable = isset ($_POST['nenable']) ? $_POST['nenable'] : '';
                    if ( $nenable == 'on' ) { $nenable = '1'; } else { $nenable = '0'; };
                    $nurl = isset ($_POST['nurl']) ? mysqli_real_escape_string($conn, $_POST['nurl']) : '';
                    $nmethod = isset ($_POST['nmethod']) ? mysqli_real_escape_string($conn, $_POST['nmethod']) : '';
                    $nbody = isset ($_POST['nbody']) ? mysqli_real_escape_string($conn, $_POST['nbody']) : '';
                    $ncontenttype = isset ($_POST['ncontenttype']) ? mysqli_real_escape_string($conn, $_POST['ncontenttype']) : '';
                    $q2 = mysqli_query($conn, "UPDATE `proxy_events` SET `sn` = '$nsn', `hw_type` = '$ntype', `enable` = '$nenable',
                              `event_code` = '$ncode', `comment` = '$nname', `target_url` = '$nurl', `target_method` = '$nmethod',
                              `target_raw_body` = '$nbody', `target_content_type` = '$ncontenttype' WHERE id = '$proxy_id' LIMIT 1") or print mysqli_error($conn);
                    print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=proxy_events">';
                    die();
               }
               $q0 = mysqli_query($conn, "SELECT * FROM proxy_events WHERE id = '$proxy_id' LIMIT 1");
               $r = mysqli_fetch_assoc($q0);
               $out .=  '<form method="POST"><table class="small_table">
                    <tr> <td>'.$loc_common_phrase_sn.': </td><td> '.create_controller_select('nsn', $r['sn']).' <font class="red">*</font> </td</tr>
                    <tr> <td>'.$loc_common_phrase_type.': </td><td> '.create_controller_type_select('ntype', $r['hw_type']).' <font class="red">*</font> </td</tr>
                    <tr> <td>'.$loc_property_name_code.': </td><td> '.create_event_code_select('ncode', $r['event_code']).' <font class="red">*</font> </td</tr>
                    <tr> <td>'.$loc_property_name_description.': </td><td> <input type=text name="nname" placeholder="info..." value="'.$r['comment'].'"> </td</tr>
                    <tr> <td>'.$loc_property_name_enable.': </td><td> '.int2checkbox($r['enable'], 'nenable', 0, '', 'Вкл').' </td</tr>

                    <tr> <td>'.$loc_property_name_url.': </td><td> <input type=text name="nurl" placeholder="site.net/my.php?device=[SN]&event=[EVENT_CODE]...&" value="'.$r['target_url'].'"> [SN] [HWTYPE] [EVENT_ID] [EVENT_CODE] [CARD] [CARD_HEX] [DATETIME] [LOGIN] [OFFICE] [IP]</td></tr>
                    <tr> <td>http-method </td><td> '.create_http_method_select('nmethod', $r['target_method']).' </td</tr>
                    <tr> <td>raw-body </td><td> <input type=text name="nbody" placeholder="" value="'.$r['target_raw_body'].'">  </td</tr>
                    <tr> <td> content-type </td><td> <input type=text name="ncontenttype" placeholder="header value..." value="'.$r['target_content_type'].'"> </td</tr>
                    <tr> <td colspan="2"> <input type="submit" value="'.$loc_common_phrase_save.'"> </td</tr>
               </table></form>';
          } else { $out .= $loc_common_phrase_disabled_user_profile; };
          break;


     case 'del_proxy_event' :
          $out =  "<h3>$loc_common_phrase_del $loc_entity_name_proxyevent</h3>";
          $proxy_id = (int)$_GET['proxy_id'];
          if ($user_info['allow_manage_proxy_events'] == 1) {
               $nsn = isset ($_POST['nsn']) ? mysqli_real_escape_string($conn, $_POST['nsn']) : '';
               if ( isset($_POST['nsn']) AND $nsn != '' ) {
                    $q2 = mysqli_query($conn, "DELETE FROM `proxy_events` WHERE id = '$proxy_id' LIMIT 1") or print mysqli_error($conn);
                    print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab=proxy_events">';
                    die();
               }
               $q0 = mysqli_query($conn, "SELECT * FROM proxy_events WHERE id = '$proxy_id' LIMIT 1");
               $r = mysqli_fetch_assoc($q0);
               $out .=  '<form method="POST"><table class="small_table"> <input type="hidden" name="nsn" value="CONFIRM_DELETE">
                    <tr> <td>'.$loc_common_phrase_sn.': </td><td> '.create_controller_select('disabled', $r['sn']).'</td</tr>
                    <tr> <td>'.$loc_common_phrase_type.': </td><td> '.create_controller_type_select('disabled', $r['hw_type']).'</td</tr>
                    <tr> <td>'.$loc_property_name_code.': </td><td> '.create_event_code_select('disabled', $r['event_code']).'</td</tr>
                    <tr> <td>'.$loc_property_name_description.': </td><td> <input type=text disabled value="'.$r['comment'].'"> </td</tr>
                    <tr> <td>'.$loc_property_name_enable.': </td><td> '.int2checkbox($r['enable'], 'nenable', 1, '', 'Вкл').' </td</tr>

                    <tr> <td>'.$loc_property_name_url.': </td><td> <input type=text name="nurl" readonly value="'.$r['target_url'].'"></td></tr>
                    <tr> <td>http-method </td><td> '.create_http_method_select('disabled', $r['target_method']).' </td</tr>
                    <tr> <td>raw-body </td><td> <input type=text name="nbody" disabled value="'.$r['target_raw_body'].'">  </td</tr>
                    <tr> <td> content-type </td><td> <input type=text name="ncontenttype" disabled value="'.$r['target_content_type'].'"> </td</tr>
                    <tr> <td colspan="2"> <input type="submit" value="'.$loc_common_phrase_del.'"> </td</tr>
               </table></form>';
          } else { $out .= $loc_common_phrase_disabled_user_profile; };
          break;



     case 'profile' :
                    $rights = '';
                         if ($user_info['allow_open_door'] == 1) { $rights .= '<a title="'.$loc_property_name_access.' : '.$loc_susbys_open_door.'">🚪</a>'; }
                         if ($user_info['allow_manage_controllers'] == 1) { $rights  .= '<a title="'.$loc_property_name_access.' : '.$loc_common_phrase_manage.', '.$loc_menu_element_controllers.'">💻</a>'; }
                         if ($user_info['allow_manage_keys'] == 1) { $rights .= '<a title="'.$loc_property_name_access.' : '.$loc_common_phrase_manage.', '. $loc_menu_element_keys.'" >🔑</a>'; }
                         if ($user_info['allow_enroll_keys'] == 1) { $rights .= '<a title="'.$loc_property_name_access.' : '.$loc_common_phrase_enroll.', '. $loc_menu_element_keys.'/'.$loc_menu_element_controllers.'">🔐</a>'; }
                         if ($user_info['allow_manage_badkeys'] == 1) { $rights .= '<a title="'.$loc_property_name_access.' : '.$loc_common_phrase_manage.', '. $loc_menu_element_badkeys.'">🗝</a>'; }
                         if ($user_info['allow_manage_offices'] == 1) { $rights .= '<a title="'.$loc_property_name_access.' : '.$loc_common_phrase_manage.', '. $loc_menu_element_offices.'">🏢</a>'; }
                         if ($user_info['allow_manage_logins'] == 1) { $rights  .= '<a title="'.$loc_property_name_access.' : '.$loc_common_phrase_manage.', '. $loc_menu_element_logins.'">👨</a>'; }
                         if ($user_info['allow_manage_options'] == 1) { $rights  .= '<a title="'.$loc_property_name_access.' : '.$loc_common_phrase_manage.', '. $loc_menu_element_options.'">⚙</a>'; }
                         if ($user_info['allow_manage_proxy_events'] == 1) { $rights  .= '<a title="'.$loc_property_name_access.' : '.$loc_common_phrase_manage.', '. $loc_menu_element_proxy_events.'">X</a>'; }
                         if ( $opts_allow_profile_edit_pswd == 1 ) { $pswd_lock = ''; } else { $pswd_lock = ' readonly disabled'; };
                         if ( $opts_allow_profile_edit_iprange == 1 ) { $ipr_lock = ''; } else { $ipr_lock = ' readonly disabled'; };
                         if ( $opts_allow_profile_edit_email == 1 ) { $mail_lock = ''; } else { $mail_lock = ' readonly disabled'; };
                         if ( $opts_allow_profile_edit_comment == 1 ) { $comm_lock = ''; } else { $comm_lock = ' readonly disabled'; };
                         if ( isset ($_POST['f_email']) ) {
                              $q = '';
                              print "$loc_button_save_changes ... <Br/>";
                              if ( $opts_allow_profile_edit_pswd == 1 and $_POST['f_new_pswd1'] != '' AND $_POST['f_new_pswd1'] == $_POST['f_new_pswd2'] ) {
                                   print "$loc_common_phrase_edit $loc_common_phrase_password ...<Br/>";
                                   $salt1 = generate_random_password(64, 'abcdefghijkmnoprstuvxyzABCDEFGHJKLMNPQRSTUVXYZ23456789_');
                                   $salt2 = generate_random_password(64, 'abcdefghijkmnoprstuvxyzABCDEFGHJKLMNPQRSTUVXYZ23456789_');
                                   $new_sha256 = hash('sha256', $salt1.$_POST['f_new_pswd1'].$salt2);
                                   $q .= "`salt1` = '$salt1', `salt2` = '$salt2', `password_sha256` = '$new_sha256', `last_changed_password_ts` = NOW(),";
                              }
                              if ( $opts_allow_profile_edit_iprange == 1 AND $_POST['f_iprange'] != $user_info['allowed_ip_range'] ) {
                                   print "$loc_common_phrase_edit $loc_susbys_src_ip_bind ...";
                                   $q .= "`allowed_ip_range` = '".mysqli_real_escape_string($conn, $_POST['f_iprange'])."',";
                              }
                              if ( $opts_allow_profile_edit_email == 1 AND $_POST['f_email'] != $user_info['email'] ) {
                                   print "$loc_common_phrase_edit $loc_common_phrase_email_address ...";
                                   $q .= "`email` = '".mysqli_real_escape_string($conn, $_POST['f_email'])."',";
                              }
                              if ( $opts_allow_profile_edit_comment == 1 AND $_POST['f_comment'] != $user_info['comment'] ) {
                                   $q .= "`comment` = '".mysqli_real_escape_string($conn, $_POST['f_comment'])."',";
                              }
                              if ( $q != '' ) {
                                        $q = substr($q, 0, -1);
                                        $q = "UPDATE `logins` SET $q WHERE `id` = '".$user_info['id']."' AND `user` = '".$user_info['user']."' LIMIT 1";
                                        // print ($q);
                                        mysqli_query($conn, $q) or print mysqli_error($conn);
                              }
                              print '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?tab='.$tab.'">';
                         }
                    $out .= '<h3>'.$loc_menu_element_profile.'</h3>
                    <form method="POST">
                    <table>
                       <tr> <td>'.$loc_common_phrase_username.'</td> <td>'.$user_info['user'].'</td> </tr>
                       <tr> <td>'.$loc_common_phrase_email_address.'</td> <td> <input type="text" name="f_email" value="'.htmlspecialchars($user_info['email']).'"'.$mail_lock.'></td> </tr>
                       <tr> <td>'.$loc_property_name_description.'</td> <td> <input type="text" name="f_comment" value="'.htmlspecialchars($user_info['comment']).'"'.$comm_lock.'> </td> </tr>
                       <tr> <td>'.$loc_common_phrase_password.'</td> <td> <input type="password" name="f_new_pswd1"'.$pswd_lock.'> <input type="password" name="f_new_pswd2"'.$pswd_lock.'> </td> </tr>
                       <tr> <td>2FA</td> <td>'.$user_info['twofactor_method'].' <a href="?tab=twofactor">'.$loc_common_phrase_edit.'</a> </td> </tr>
                       <tr> <td>'.$loc_property_name_ipsubnets.'</td> <td> <input type="text" name="f_iprange" value="'.htmlspecialchars($user_info['allowed_ip_range']).'"'.$ipr_lock.'> </td> </tr>
                       <tr> <td>'.$loc_property_name_created.'</td> <td>'.$user_info['created_ts'].'</td> </tr>
                       <tr> <td>'.$loc_property_name_last_activity.'</td> <td>'.$user_info['last_used_ts'].'</td> </tr>
                       <tr> <td>'.$loc_property_name_accessrights.':</td> <td>'.$rights.'</td> </tr>
                       <tr> <td>'.$loc_property_name_last_activity.'/'.$loc_common_phrase_password.'</td> <td>'.$user_info['last_changed_password_ts'].'</td> </tr>
                       
                    </table> <input type="submit" value="'.$loc_button_save_changes.'"></form>';
               break;


     case 'twofactor' :
          $out .= "<h3>$loc_common_phrase_2fa</h3>";
          $add_form_elements = $add_form_elements2 = '';
          $step1_ref_hash = sha1($_SESSION['control_hash']);
          $step = isset ($_POST['step']) ? (int)$_POST['step'] : 0;
          $step1_hash = isset ($_POST['step1_hash']) ? $_POST['step1_hash'] : '';
          $new_2fa_method = isset($_POST['f_twofac']) ? $_POST['f_twofac'] : '';
          if ( $step1_hash == $step1_ref_hash ) {
               if ( ! isset($_SESSION['twofa_shared']) ) {
                         $_SESSION['twofa_shared'] = generate_random_password(32, 'abcdefghijkmnoprstuvxyzABCDEFGHJKLMNPQRSTUVXYZ');
               }

               if ($new_2fa_method == 'none') {   # ## DISABLE
                    $step++;
                    if ( isset($_POST['f_confirm']) ) {
                         if ( $_POST['f_confirm'] == 'on' AND check_password_db($logged_user, $_POST['f_password'])[0] == 0) {
                                   $out .= "$loc_common_phrase_2fa $loc_common_phrase_off";
                                   mysqli_query($conn, "UPDATE logins SET `twofactor_method` = ''
                                   WHERE `user` = '$logged_user' and `id` = '".$user_info['id']."' LIMIT 1");
                                   $out .= '<META HTTP-EQUIV="REFRESH" CONTENT="2;URL=?">';
                         } else { $out .= "$loc_common_phrase_error: $loc_common_2fa_confirm ? $loc_common_phrase_password ?"; };
                    }
                    $add_form_elements = $loc_common_phrase_2fa.' ... DISABLE ...<Br/>confirm: <input type="checkbox" name="f_confirm"><Br/>
                    '.$loc_common_phrase_password.': <input type="password" name="f_password"><Br/>';

               } elseif ($new_2fa_method == 'totp') {# ## TOTP
                    $step++;
                    $ref_totp = (new Totp())->GenerateToken(base32_decode($_SESSION['twofa_shared']));
                    if ( isset($_POST['f_answer']) and $_POST['f_answer'] != '' ) {
                         if ( $_POST['f_answer'] == $ref_totp AND check_password_db($logged_user, $_POST['f_password'])[0] == 0 ) {
                              $out .= "$loc_common_phrase_on";
                              mysqli_query($conn, "UPDATE logins SET `twofactor_method` = 'totp', `twofactor_secret` = '".$_SESSION['twofa_shared']."'
                                 WHERE `user` = '$logged_user' and `id` = '".$user_info['id']."' LIMIT 1");

                              $out .= '<META HTTP-EQUIV="REFRESH" CONTENT="2;URL=?">';
                         } else { $out .= "$loc_common_phrase_error : $loc_property_name_code? $loc_property_name_time ? $loc_common_phrase_password ?"; };
                    }
                    $add_form_elements = $loc_common_phrase_2fa.' ...TOTP... <table><tr><td>'.$loc_susbys_2fa_shared_secret.':</td><td> <input type="text" value="'.$_SESSION['twofa_shared'].'" size="32" readonly></td></tr>
                    <tr><td>TOTP-'.$loc_property_name_code.':</td><td> <input type ="text" name="f_answer" maxlength="6" pattern="^[0-9]{6}$"  placeholder="dddddd" type="number"> </td></tr>
                    <tr><td>'.$loc_common_phrase_password.':</td><td> <input type="password" name="f_password"></td></tr> </table>';
                    $add_form_elements2 = '<img class="qrcode_img" src="./phpqrcode/indexqr.php?data=otpauth://totp/'.$logged_user.'@'.$_SERVER['HTTP_HOST'].'?secret='.$_SESSION['twofa_shared'].'%26issuer='.htmlspecialchars($opts_global_sysname).'" alt="QR-'.$loc_susbys_2fa_shared_secret.'">';

               }  elseif ($new_2fa_method == 'email') {   # ## EMAIL
                    $add_form_elements .= "$loc_common_phrase_2fa ...EMAIL... <Br/>";
                    if ( $user_info['email'] == '' or !filter_var($user_info['email'], FILTER_VALIDATE_EMAIL) ) {
                         $out .= "$loc_common_phrase_error : $loc_common_phrase_email_address - $loc_common_phrase_not_found";
                    } else {
                         $step++;
                         if ( isset($_POST['f_email']) and $step >= 1 and $_POST['f_email'] == $user_info['email'] ) {
                              $out .= "$loc_common_phrase_email_address, $loc_common_phrase_send ...";
                              $mail_headers = "Content-Type: text/html; charset=UTF-8\r\nFrom: $opts_email_recovery_from\r\n";
                              $body = "<html><head>$opts_global_sysname - $loc_property_name_code, $loc_property_name_enable</head>
                                <body><Br/><Br/><h3>$loc_property_name_enable $loc_common_phrase_2fa</h3><i>$loc_property_name_code</i>: <B>".$_SESSION['twofa_shared']."</B><Br/><Br/></body></html>";
                              if ( ! mail ($user_info['email'], "$opts_global_sysname - $loc_property_name_code, $loc_property_name_enable", $body, $mail_headers) ) { $out .= "$loc_common_phrase_error: $loc_common_phrase_email_address"; };
                         }

                         if ( isset($_POST['f_answer']) and $_POST['f_answer'] != '' and $step >= 2 ) {
                              if ( $_POST['f_answer'] == $_SESSION['twofa_shared'] AND check_password_db($logged_user, $_POST['f_password'])[0] == 0 ) {
                                   $out .= "$loc_common_phrase_on";
                                   mysqli_query($conn, "UPDATE logins SET `twofactor_method` = 'email'
                                   WHERE `user` = '$logged_user' and `id` = '".$user_info['id']."' LIMIT 1");
                                   $out .= '<META HTTP-EQUIV="REFRESH" CONTENT="2;URL=?">';
                              } else { $out .= "$loc_common_phrase_error : $loc_property_name_code ? $loc_common_phrase_password ?"; };
                         }
                         if ( $step == 1 ) { $add_form_elements .= $loc_common_phrase_email_address.': <input type="text" name="f_email" value="'.$user_info['email'].'" size="32" readonly><Br/>'.$loc_susbys_confirm_mail_send.'<Br/>'; }
                         elseif ( $step == 2 ) { $add_form_elements .= 'Email-'.$loc_property_name_code.': <input type ="text" name="f_answer"> <Br/>
                                                  '.$loc_common_phrase_password.': <input type="password" name="f_password"><Br/>'; }
                    }

               } elseif ($new_2fa_method == 'bitcoin') {
                    $step++;
                    if ( isset($_POST['f_sign']) and $_POST['f_sign'] != '' ) {
                         $bitcoinECDSA = new BitcoinECDSA();
                         $bitcoinECDSA->generateRandomPrivateKey();
                         $wallet = isset ($_POST['f_wallet']) ? mysqli_real_escape_string($conn, $_POST['f_wallet']) : '';
                         if ( check_password_db($logged_user, $_POST['f_password'])[0] != 0 ) {
                             $out .= "$loc_common_phrase_error: $loc_common_phrase_password";
                         }
                         elseif ( ! $bitcoinECDSA->validateAddress($wallet)) {
                             $out .= "$loc_common_phrase_error: Not crypto wallet";
                         } else {
                              if ( $bitcoinECDSA->checkSignatureForMessage($wallet, $_POST['f_sign'], $_SESSION['twofa_shared']) ) {
                                   $out .= "$loc_common_phrase_on";
                                   mysqli_query($conn, "UPDATE logins SET `twofactor_method` = 'bitcoin', `twofactor_secret` = '".$wallet."'
                                   WHERE `user` = '$logged_user' and `id` = '".$user_info['id']."' LIMIT 1");
                                   $out .= '<META HTTP-EQUIV="REFRESH" CONTENT="2;URL=?">';
                              } else { $out .= "$loc_common_phrase_error: Bad sign"; };
                         };
                    }
                    $add_form_elements = $loc_common_phrase_2fa.' ... BTC ... <table><tr><td>'.$loc_susbys_2fa_test_string.':</td><td> <input type="text" value="'.$_SESSION['twofa_shared'].'" readonly size="32"></td></tr>
                    <tr><td>'.$loc_susbys_2fa_wallet.':</td><td> <input type="text" name="f_wallet" size="32"></td></tr>
                    <tr><td>'.$loc_susbys_2fa_sign.':</td><td> <input type ="text" name="f_sign" size="32"></td></tr>
                    <tr><td>'.$loc_common_phrase_password.':</td><td> <input type="password" name="f_password"></td></tr>
                    </table>';
                    $add_form_elements2 = '<img class="qrcode_img" src="./phpqrcode/indexqr.php?data='.$_SESSION['twofa_shared'].'" alt="QR-'.$loc_susbys_2fa_test_string.'"><div class="green">'.$loc_susbys_2fa_help1.'</div>';
               } else { $out .= "$loc_common_phrase_error: $loc_susbys_2fa_method $loc_common_phrase_2fa"; }
          }

          if ($step == 0) { $method_sel = create_twofactor_type_select('f_twofac', $user_info['twofactor_method']); }
          else { $method_sel = '<input type="hidden" name="f_twofac" value="'.$new_2fa_method.'">'; };
          $out .= '<div class="twofa_form controller_data_table1"><form method="POST">'.$method_sel."\n".
               '<input type="hidden" name="step" value="'.$step.'" size="2">
                <input type="hidden" name="step1_hash" value="'.$step1_ref_hash.'">'.$add_form_elements.'
                <input type="submit" value="'.$loc_common_phrase_send.'"></form></div>'.$add_form_elements2;
               break;


     case 'twofactor_steps' :
          $out .= "<Br/>$loc_susbys_2fa_method $loc_common_phrase_2fa: ".$user_info['twofactor_method'].'<Br/>';
          if ( $user_info['twofactor_method'] == 'totp' ) {
               $ref_totp = (new Totp())->GenerateToken(base32_decode($user_info['twofactor_secret']));
               if ( isset($_POST['f_answer']) AND $_POST['f_answer'] == $ref_totp ) {
                    $_SESSION['twofactor_passed'] = 1;
                    $out .= '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?">';
               }
               $twofa_fields = 'TOTP-'.$loc_property_name_code.': <input type="text" name="f_answer" pattern="^[0-9]{6}$" maxlength="6" placeholder="dddddd" type="number">';
          } elseif ( $user_info['twofactor_method'] == 'email' ) {
               if ( ! isset($_SESSION['twofa_shared_4send']) ) {
                            $_SESSION['twofa_shared_4send'] = generate_random_password(32, 'abcdefghijkmnoprstuvxyzABCDEFGHJKLMNPQRSTUVXYZ23456789');
               }
               $out .= $loc_susbys_mail_sending;
               $mail_headers = "Content-Type: text/html; charset=UTF-8\r\nFrom: $opts_email_recovery_from\r\n";
               $body = "<html><head>$opts_global_sysname - $loc_property_name_code</head>
               <body><Br/><Br/><h3>Запрос кода 2FA</h3><i>$loc_property_name_code</i>: <B>".$_SESSION['twofa_shared_4send']."</B><Br/><Br/></body></html>";
               if ( ! mail ($user_info['email'], "$opts_global_sysname - $loc_property_name_code", $body, $mail_headers) ) { $out .= 'Mail not send'; };
               if ( isset($_POST['f_answer']) AND $_POST['f_answer'] == $_SESSION['twofa_shared_4send'] ) {
                    $_SESSION['twofactor_passed'] = 1;
                    $out .= '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?">';
               }
               $twofa_fields = 'EMAIL-'.$loc_property_name_code.': <input type="text" name="f_answer">';
          } elseif ( $user_info['twofactor_method'] == 'bitcoin' ) {
               if ( ! isset($_SESSION['twofa_shared_4send']) ) {
                            $_SESSION['twofa_shared_4send'] = generate_random_password(32, 'abcdefghijkmnoprstuvxyzABCDEFGHJKLMNPQRSTUVXYZ23456789');
               }
               $bitcoinECDSA = new BitcoinECDSA();
               $bitcoinECDSA->generateRandomPrivateKey();
               if ( isset($_POST['f_sign']) AND $bitcoinECDSA->checkSignatureForMessage($user_info['twofactor_secret'], $_POST['f_sign'], $_SESSION['twofa_shared_4send']) ) {
                    $_SESSION['twofactor_passed'] = 1;
                    $out .= '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=?">';
               }
               $twofa_fields = $loc_susbys_2fa_wallet.': <B>'.substr($user_info['twofactor_secret'], 0, 5).' ... '.substr($user_info['twofactor_secret'], -5).'</B><Br/>
               '.$loc_susbys_2fa_test_string.': <input type="text" value="'.$_SESSION['twofa_shared_4send'].'" size="32" readonly><Br/>
               '.$loc_susbys_2fa_sign.': <input type="text" name="f_sign" size="32"><Br/><div class="green">'.$loc_susbys_2fa_help2.'</div>';
          }
          $out .= '<div class="twofa_form"><form method="POST">'.$twofa_fields.'
          <input type="submit" value="'.$loc_common_phrase_send.'"></form></div>';
               break;

}   /* End Switch */


if ($tab !='login' and $tab !='logout' AND isset($_SESSION['user']) AND $_SESSION['user'] != '' AND isset($user_info['user']) AND $user_info['user'] != '') {   # // TOP-MENU
     
     $menu_elements = array ('controllers', 'keys', 'offices', 'logins', 'badkeys', 'proxy_events', 'options', 'converter');
     print "\n".'<div class="div_top_menu">';
     foreach ($menu_elements as $menu_element) {
          $el_style = '';
          if ( $tab == $menu_element ) { $el_style = 'act'; }
          else {
               if ( isset($user_info['allow_manage_'.$menu_element]) AND $user_info['allow_manage_'.$menu_element] == 0 ) { $el_style = 'gray'; }
          };
          if ( $hide_menu == 0 ) {   /* or $menu_element == 'converter' */
               $loc_menu_element_name = ${'loc_menu_element_'.$menu_element};  // VAR from localization
               print '<span class="'.$el_style.'">  <a href="?tab='.$menu_element.'">'.$loc_menu_element_name."</a></span>\n";
          };
     }
     print "</div>\n";
}

if ( $tab == '' ) {     # // MAIN DEFAULT TAB
     $out .= "\n<div class=\"greeting_div\"><h3>".htmlspecialchars($opts_global_sysname)."</h3>
       <span>$loc_susbys_greeting</span><Br/>
       <span>$loc_common_phrase_username: $logged_user</span><Br/>
       <span>IP: $remote_ip</span><Br/>
       <span>2FA: ".htmlspecialchars($user_info['twofactor_method'])."</span><Br/>
       <span>$opts__int__sys__version</span>
     </div>\n".get_statistic();
};

print '<div class="main_div1">'.$out.'</div>';


print '</body></html>';

?>

<?php

require("config.php");

$step = isset($_GET['step']) ? (int)$_GET['step'] : 0;

print '<!DOCTYPE HTML>
<html> <head>   <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="shortcut icon" href="z5r.ico" />
  <meta charset="utf-8">
<title>'.$opts_global_sysname.' :: '.$loc_susbys_email_pswd_recovery.'</title>
  <link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body><div class="div_sysname"><a href="admin.php">'.$opts_global_sysname.'</a></div>';

if ( $opts_allow_passwd_email_recovery != 1 ) {
    print "<div class=\"red\">$loc_susbys_email_pswd_recovery $loc_common_phrase_not_accessible;<Br/>$loc_common_phrase_disabled_global_options;</div>";
    die;
}

print "<div class=\"main_div1\">
   <h1>$loc_susbys_email_pswd_recovery</h1>";

$srv_entropy = $_SERVER['GATEWAY_INTERFACE'].$_SERVER['SERVER_ADDR'].$_SERVER['SERVER_NAME'].$_SERVER['SERVER_SOFTWARE'].$_SERVER['SCRIPT_FILENAME'];
$step_0_ref_hash = hash('sha512', 'EMAIL_0_RECOVERY_'.$remote_ip.$sess_secret_salt.date("Ymd").'__0_RECOVERY'.$srv_entropy);

if ( $step == 0 ) {
      $user = isset($_GET['user']) ? $_GET['user'] : '';
      print "<form method=\"POST\" action=\"?step=1\" id=\"password_recovery_form_1\">
            <table>
              <tr> <th>$loc_common_phrase_username</th> <td> <input type=\"text\" name=\"f_user\" value=\"".htmlspecialchars($user)."\"> </td> </tr>
              <tr> <th>$loc_common_phrase_email_address</th>   <td> <input type=\"text\" name=\"f_mail\"> </td> </tr>";
              if ( $opts_email_recovery_use_captcha == 1 ) {
                 print "<tr> <td> <img src=\"kcaptcha/?".session_name()."=".session_id()."\" alt=\"Captcha\"> </td>
                             <td> <input type=\"text\" name=\"f_captcha\"> </tr>"; };
              print "<tr> <td colspan=\"2\"> <input type=\"submit\" value=\"$loc_common_phrase_send\"> </td> </tr>
            </table>
            <input type=\"hidden\" name=\"f_hash0\" value=\"$step_0_ref_hash\">
        </form>";
}
elseif ( $step == 1 ) {
      $user =  isset($_POST['f_user']) ? $_POST['f_user'] : '';
      $mail =  isset($_POST['f_mail']) ? $_POST['f_mail'] : '';
      $hash0 =  isset($_POST['f_hash0']) ? $_POST['f_hash0'] : '';
      $back_link = "<a href=\"?step=0&user=".htmlspecialchars($user)."\">$loc_susbys_email_pswd_recovery</a>"; 
      if ( $user == '' or $mail == '' ) {
           print "<span class=\"red\">$loc_common_phrase_username / $loc_common_phrase_email_address $loc_common_phrase_must_be_filled</span>$back_link"; }
      elseif ( $opts_email_recovery_use_captcha == 1 && isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] != $_POST['f_captcha'] ) {
		echo "$loc_common_phrase_error :: captcha";
	  }
      elseif ( $hash0 != $step_0_ref_hash ) { print "$loc_common_phrase_error : HASH-0 $loc_common_phrase_not_active; $loc_susbys_email_pswd_recovery_bad_hash"; }
      else {
              if ($opts_email_recovery_use_captcha == 1) { $_SESSION['captcha_keystring'] = generate_random_password(32); };
              $q1 = mysqli_query($conn, "SELECT user, email, enable, allowed_ip_range,
                    SHA1( CONCAT (password_sha256, last_changed_password_ts, created_ts, salt2, allowed_ip_range, comment)) AS password_sha
                    FROM logins WHERE `user` = '".mysqli_real_escape_string($conn, $user)."' AND `email` = '".mysqli_real_escape_string($conn, $mail)."' AND `enable` = '1' LIMIT 1");
              if ( mysqli_num_rows($q1) == 0 ) { print "<span class=\"red\">$loc_common_phrase_username / $loc_common_phrase_email_address $loc_common_phrase_not_found</span>$back_link"; }
              else {
                $r1 = mysqli_fetch_assoc($q1);
                if ( $r1['enable'] == 0 ) { print "$loc_common_phrase_username ".htmlspecialchars($user)." : $loc_common_phrase_activity = $loc_common_phrase_off; $loc_common_phrase_disabled_user_profile<Br/>$back_link"; }
                elseif ( $r1['allowed_ip_range'] != '' AND check_ip_acl($remote_ip, $r1['allowed_ip_range']) == 0 ) {
                         print "$loc_common_phrase_username ".htmlspecialchars($user)." : $loc_common_phrase_ip_not_allowed - $loc_common_phrase_disabled_user_profile <Br/>$back_link";
                } else {
                  $step_1_ref_hash = hash('sha512', 'EMAIL_1_STEP_'.$remote_ip.$sess_secret_salt.$r1['user'].$r1['email'].$r1['password_sha'].$hash0.date("Ymd").'__1_STEP');
                  $url = "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?step=2&user='.$user.'&mail='.$mail.'&hash0='.$step_0_ref_hash.'&hash1='.$step_1_ref_hash;
                  print "<h2>Step $step</h2> Sending email to ".$r1['email']." ...";
                  $head = "$opts_global_sysname $loc_susbys_email_pswd_recovery";
                  $body = "<html><head></head> <body>   <h1>$opts_global_sysname - $loc_susbys_email_pswd_recovery</h1>
                   <font color=\"red\">$loc_susbys_email_pswd_recovery_mail_body1<Br/>
                                       $loc_susbys_email_pswd_recovery_mail_body2</font><Br/><Br/>
                   <a href=\"$url\">$loc_common_phrase_password @ $opts_global_sysname</a>
                   </body></html>";
                  $mail_headers = "Content-Type: text/html; charset=UTF-8\r\nFrom: $opts_email_recovery_from\r\n";
                  if ( ! mail ($r1['email'], $head, $body, $mail_headers) ) {
                    $error = error_get_last()["message"];
                    print_r($error);
                  }
                  print $loc_susbys_email_pswd_recovery_ok_inform;
                  # print $body;   # // Uncomment Too DANGER IN PRODUCTION !!
                }
              };
      }
}
elseif ( $step == 2 ) {
      $user =  isset($_GET['user']) ? mysqli_real_escape_string($conn, $_GET['user']) : '';
      $mail =  isset($_GET['mail']) ? mysqli_real_escape_string($conn, $_GET['mail']) : '';
      $hash0 = isset($_GET['hash0']) ? $_GET['hash0'] : '';
      $hash1 = isset($_GET['hash1']) ? $_GET['hash1'] : '';
      if ( $step_0_ref_hash != $hash0 ) { print "$loc_common_phrase_error : HASH-0 $loc_common_phrase_not_active; $loc_susbys_email_pswd_recovery_bad_hash"; }
      else {
        $q2 = mysqli_query($conn, "SELECT user, email, allowed_ip_range,
                           SHA1( CONCAT (password_sha256, last_changed_password_ts, created_ts, salt2, allowed_ip_range, comment)) AS password_sha
                           FROM logins WHERE `user` = '".mysqli_real_escape_string($conn, $user)."' AND `email` = '".mysqli_real_escape_string($conn, $mail)."' AND `enable` = '1' LIMIT 1");
        $r2 = mysqli_fetch_assoc($q2);
        if ( $r2['allowed_ip_range'] != '' AND check_ip_acl($remote_ip, $r2['allowed_ip_range']) == 0 ) {
                         print "$loc_common_phrase_username ".htmlspecialchars($user)." : $loc_common_phrase_ip_not_allowed - $loc_common_phrase_disabled_user_profile <Br/>$back_link";
        } else {
          $hash1_ref = hash('sha512', 'EMAIL_1_STEP_'.$remote_ip.$sess_secret_salt.$r2['user'].$r2['email'].$r2['password_sha'].$hash0.date("Ymd").'__1_STEP');
          if ( $hash1_ref != $hash1 ) { print "$loc_common_phrase_error : HASH-1 $loc_common_phrase_not_active; $loc_susbys_email_pswd_recovery_bad_hash"; } else {
            print "<h3>$loc_susbys_email_pswd_recovery : $loc_common_phrase_password</h3>";
            $new_pwd = generate_random_password(16);
            $salt1 = generate_random_password(64, 'abcdefghijkmnoprstuvxyzABCDEFGHJKLMNPQRSTUVXYZ23456789_');
            $salt2 = generate_random_password(64, 'abcdefghijkmnoprstuvxyzABCDEFGHJKLMNPQRSTUVXYZ23456789_');
            $new_sha256 = hash('sha256', $salt1.$new_pwd.$salt2);
            mysqli_query($conn, "UPDATE `logins` SET `salt1` = '$salt1', `salt2` = '$salt2', `password_sha256` = '$new_sha256', `last_changed_password_ts` = NOW() WHERE `user` = '$user' AND `email` = '$mail' LIMIT 1") or print mysqli_error($conn);
            print "$loc_common_phrase_password, $loc_property_name_created: <B>$new_pwd</B>
            <Br/>Store this in safe place <a href=\"admin.php?user=$user\">$loc_common_phrase_login</a>";
          }
        }
      }
}

print "</div>";


print '</body></html>';

?>


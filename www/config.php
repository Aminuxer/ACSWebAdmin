<?php

   /* MySQL DB Connect options */

$db_host = 'localhost';
$db_name = 'z5r';
$db_user = 'z5r';
$db_pswd = 'z5r';

/* Change this value to any random string after installation */
$sess_secret_salt = 'this string must be changed to anywhere';

$debug = 0;                    // 0 - no debug; 1 - short log; 2 - detail log; 3 - very detailed log ;
$debug_log = '/tmp/z5r.txt';   // log path

/* Localization. Default = ru, available = en / de / fr / es / cn / ar / in / kr / jp   */
/* or other file like ??.php from localization dir */
$localization = 'en';

# ============================
require("func.php");

?>

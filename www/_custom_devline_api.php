<?php
 # ACSWebAdmin/z5r --> DevLine Linia

 $skud = isset($_GET['skud']) ? $_GET['skud'] : '';
 $ev_code = isset($_GET['code']) ? (int)$_GET['code'] : 0;
 $evid    = isset($_GET['evid']) ? $_GET['evid'] : '';
 $card    = isset($_GET['card']) ? $_GET['card'] : '';
 $user    = isset($_GET['user']) ? $_GET['user'] : '';
 $time    = isset($_GET['time']) ? $_GET['time'] : '';

 $srv1 = 'http://z5r_skud_integration:password@devline-linia-server.local:9786/events';

function send_event_to_linia ($f_url, $datetime, $skud_name, $camera_num, $event_name, $event_text) {
  $http_curl = curl_init();
  curl_setopt($http_curl, CURLOPT_URL, $f_url);
  curl_setopt($http_curl, CURLOPT_VERBOSE, 0);
  curl_setopt($http_curl, CURLOPT_PORT , parse_url($f_url, PHP_URL_PORT) );
  curl_setopt($http_curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($http_curl, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($http_curl, CURLOPT_CONNECTTIMEOUT, 20);
  curl_setopt($http_curl, CURLOPT_TIMEOUT, 40);
  curl_setopt($http_curl, CURLOPT_POST, 1);
  curl_setopt($http_curl, CURLOPT_HTTPHEADER, array('Content-type: application/json') );

  if ( parse_url ($f_url, PHP_URL_SCHEME) == 'https' ) {
      curl_setopt($http_curl, CURLOPT_SSL_VERIFYPEER, 0);
  };

  $f_body = '{ "time" : "'.$datetime.'",  "source" : "'.$skud_name.'", "name" : "'.$event_name.'",
             "device": '.$camera_num.',         "data" : "'.$event_text.'" }';
  curl_setopt($http_curl, CURLOPT_POSTFIELDS, $f_body);

  $http_data = curl_exec($http_curl);
  if ( ! curl_errno($http_curl) ) {
            $http_info = curl_getinfo($http_curl);
            print '3.2.2 -curl- Took '.$http_info['total_time'].' seconds'."<Br/>\n";
  } else {
            print '3.2.2 -curl- error: ' . curl_error($http_curl)."<Br/>\n";
  }
  return $http_data;
}

$text = '';
$card = 'XXXX,ddd,'.substr($card, -5);

if ( $ev_code == 1 ) { $ev_name = 'DOOR_BUTTON'; $text = "iD: $evid"; }
if ( $ev_code == 4 ) { $ev_name = 'DOOR_CARD '.$card; $text = "U: $user, iD: $evid"; }
if ( $ev_code == 8 ) { $ev_name = 'DOOR_NET'; $text = "IP: $user"; }

if ( $skud == 'dev1' ) {
   send_event_to_linia ($srv1, $time, 'Z5R-SKUD_'.$skud, 0, "$ev_name", "$text");  # int door cam 0
   send_event_to_linia ($srv1, $time, 'Z5R-SKUD_'.$skud, 4, "$ev_name", "$text");  # ext door cam 4
}

if ( $skud == 'dev2' ) {
   send_event_to_linia ($srv1, $time, 'Z5R-SKUD_'.$skud, 22, "$ev_name", "$text");  # vhod cam 22
}

if ( $skud == 'dev3' ) {
   send_event_to_linia ($srv1, $time, 'Z5R-SKUD_'.$skud, 15, "$ev_name", "$text");  # cam-15
   send_event_to_linia ($srv1, $time, 'Z5R-SKUD_'.$skud,  7, "$ev_name", "$text");  # cam-7
}

     $fh = fopen("/tmp/z5r-skud.txt", "a");
           fwrite($fh, "$time $skud $ev_name $text\n");
           fclose($fh);

?>

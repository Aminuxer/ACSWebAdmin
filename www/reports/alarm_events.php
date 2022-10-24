<?php

  if ( ! isset($_GET['date1']) ) {
    $out .= '<form method="GET">
                <input type="hidden" name="tab" value="reports">
                <input type="hidden" name="report" value="'.$report.'">
       <input type="date" name="date1" min="1970-01-01" value="'.date("Y-m-d").'">
       <input type="date" name="date2" min="1970-01-01" value="'.date("Y-m-d").'">
       '.create_office_select('office').'
       <input type="submit">
    </form>';
  } else {
    $date1 = mysqli_real_escape_string($conn, $_GET['date1']);
    $date2 = mysqli_real_escape_string($conn, $_GET['date2']);
    $office = (int)$_GET['office'];

    $out .= "$date1 .. $date2";

    if ( $office != 0 ) { $office_filter = " AND cn.office_id = '$office' "; } else { $office_filter = ''; };
  $q = mysqli_query($conn, "SELECT
   ev.event_code, INET_NTOA(src_ip) AS device_ip, cn.name AS controller_name,
   of.name AS office_name, uk.user, uk.comment, ec.name AS event_name, ec.severity_color
FROM `events` ev
 LEFT JOIN controller_names cn ON cn.sn = ev.sn AND cn.hw_type = ev.hw_type
 LEFT JOIN user_keys uk ON uk.key = ev.card
 LEFT JOIN offices of ON of.id = cn.office_id
 LEFT JOIN event_codes ec ON ec.id = ev.event_code
WHERE
  ev.hw_type = 'Z5RWEB' AND ev.event_code IN (2,3, 6, 7, 10, 11, 12, 13, 38)
  and ts between '$date1' and '$date2'
  $office_filter
ORDER BY of.name, uk.user");
  $out .= '<table> <tr> <th>'.$loc_property_name_code.'</th> <th>IP</th> <th>'.$loc_entity_name_controller.'</th>
                       <th>'.$loc_entity_name_office.'</th><th>'.$loc_entity_name_username.'</th><th>'.$loc_property_name_description.'</th><th>'.$loc_entity_name_event.'</th></tr>';

  while ( $r = mysqli_fetch_array($q) ) {
    $out .= '<tr style="background-color: '.$r['severity_color'].'">
                  <td>'.$r['event_code'].'</td>
                  <td>'.$r['device_ip'].'</td>
                  <td>'.htmlspecialchars($r['controller_name']).'</td>
                  <td>'.htmlspecialchars($r['office_name']).'</td>
                  <td>'.htmlspecialchars($r['user']).'</td>
                  <td>'.htmlspecialchars($r['comment']).'</td>
                  <td>'.htmlspecialchars($r['event_name']).'</td> </tr>';
  }
}

?>

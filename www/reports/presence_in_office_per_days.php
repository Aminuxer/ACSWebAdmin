<?php

  if ( ! isset($_GET['date1']) ) {
    $out .= '<form method="GET">
                <input type="hidden" name="tab" value="reports">
                <input type="hidden" name="report" value="'.$report.'">
       <input type="date" name="date1" min="1970-01-01" value="'.date("Y-m-d").'">
       <input type="date" name="date2" min="1970-01-01" value="'.date("Y-m-d").'"> <Br/>
       <input type="checkbox" name="show_csv" id="show_csv_lbl"> <label for="show_csv_lbl">CSV</label> <Br/>
       '.$loc_entity_name_office.' '.create_office_select('office').' <Br/>
       <input type="submit">
    </form>';
  } else {
    $date1 = mysqli_real_escape_string($conn, $_GET['date1']);
    $date2 = mysqli_real_escape_string($conn, $_GET['date2']);
    $office = (int)$_GET['office'];
    $csv = isset ($_GET['show_csv']) ? $_GET['show_csv'] : 0;

    $out .= htmlspecialchars("$date1 .. $date2");

    if ( $office != 0 ) { $office_filter = " AND cn.office_id = '$office' "; } else { $office_filter = ''; };
    $q = mysqli_query($conn, "SELECT
   of.name AS office, uk.user, CONCAT(YEAR(ev.ts), '-', MONTH(ev.ts)) AS mts, DAY(ev.ts) AS dts
FROM `events` ev
 LEFT JOIN controller_names cn ON cn.sn = ev.sn AND cn.hw_type = ev.hw_type
 LEFT JOIN user_keys uk ON uk.key = ev.card
 LEFT JOIN offices of ON of.id = cn.office_id
WHERE
  ev.hw_type = 'Z5RWEB' AND ev.event_code IN (4, 16, 48)
  AND (ts between '$date1 00:00:00' AND '$date2 23:59:59')
  $office_filter
GROUP BY office, uk.user, mts, dts
ORDER BY office, uk.user, mts ASC, dts ASC");

    if ( $csv == 'on' ) { $out = "<textarea cols=\"160\" rows=\"40\">\n\"$humanity_name\",\"$date1\",\"$date2\"
\"$loc_entity_name_office\", \"$loc_entity_name_username\",";
    } else {
       $out .= '<table>';
    };

    $months = array();
    $users = array();
    while ( $r = mysqli_fetch_array($q) ) {
      $cu_mts = $r['mts'];
      $cu_dts = $r['dts'];
      $cu_user = $r['user'];  $cu_office = $r['office'];
      $months[$cu_mts][$cu_dts]['users'][$cu_user] = 1;
      if ( ! in_array (array('u' => $cu_user, 'o' => $cu_office), $users) ) { array_push($users, array('u' => $cu_user, 'o' => $cu_office)); };
    }

    ksort($months);
    foreach ( $months as $mind => $subarr ) { ksort($months[$mind]); }

    $mm = $dd = '';
    foreach ( $months as $m => $d ) {
           if ( $csv != 'on' ) { $mm .= "<th colspan=\"".count($months[$m])."\">$m</th>"; };
           foreach ( $d as $didx => $cu_day ) {
             if ( $csv == 'on' ) { $out .= "\"$m-$didx\","; } else { $dd .= "<th>$didx</th>"; };
           }
    };

    if ( $csv != 'on' ) { $out .= '<tr> <th rowspan="2">'.$loc_entity_name_office.'</th> <th rowspan="2">'.$loc_entity_name_username.'</th> '.$mm.'
             <tr> '.$dd.' </tr>'; };

    foreach ( $users as $user ) {
       $cu_user = $user['u'];
       $cu_office = $user['o'];
       $pres_tds = '';
       foreach ( $months as $m => $d ) {
          foreach ( $d as $didx => $cu_day ) {
              $pres = $cu_day['users'];
              if ( isset ($pres[$cu_user]) and $pres[$cu_user] == 1 ) { $udata = '+'; } else { $udata = ''; };
              if ( $csv == 'on' ) { $pres_tds .= "\"$udata\","; } else { $pres_tds .= "<td>$udata</td>"; };
          }
       };
       if ( $csv == 'on' ) {
         $out .= "\n\"".htmlspecialchars($cu_office).'","'.htmlspecialchars($cu_user).'",'.$pres_tds;
       } else {
          $out .= '<tr> <td>'.htmlspecialchars($cu_office).'</td> <td>'.htmlspecialchars($cu_user).'</td>'.$pres_tds.'</tr>';
       };
    }

    if ( $csv == 'on' ) { $out .= '</textarea>'; } else { $out .= '</table>'; };
  }

?>

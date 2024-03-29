<?php

  if ( ! isset($_GET['date1']) ) {
    $out .= '<form method="GET">
                <input type="hidden" name="tab" value="reports">
                <input type="hidden" name="report" value="'.$report.'">
       <input type="date" name="date1" min="1970-01-01" value="'.date("Y-m-d").'">
       <input type="date" name="date2" min="1970-01-01" value="'.date("Y-m-d").'"> <Br/>
       <input type="checkbox" name="show_csv" id="show_csv_lbl"> <label for="show_csv_lbl">CSV</label> <Br/>
       '.$loc_entity_name_office.' '.create_office_select('office').'<Br/>
       <input type="submit">
    </form>';
  } else {
    $date1 = mysqli_real_escape_string($conn, $_GET['date1']);
    $date2 = mysqli_real_escape_string($conn, $_GET['date2']);
    $office = (int)$_GET['office'];
    $csv = isset ($_GET['show_csv']) ? $_GET['show_csv'] : 0;

    if ( $office != 0 ) { $office_filter = " AND uk.office_id = '$office' "; } else { $office_filter = ''; };
    $q = mysqli_query($conn, "SELECT
       uk.user, uk.comment, of.name AS office_name, of.address AS office_address
FROM user_keys uk
  LEFT JOIN offices of ON of.id = uk.office_id
  LEFT JOIN last_activity_keys lak ON lak.key = uk.key
WHERE
    (uk.key  NOT IN (SELECT DISTINCT (ev.card) FROM events ev WHERE ev.ts > '$date1 00:00:00' AND ev.ts < '$date2 23:59:59') )
    $office_filter
-- GROUP BY uk.user
ORDER BY of.name, uk.user");

    if ( $csv == 'on' ) {
      $out = "<textarea cols=\"160\" rows=\"40\">\n\"$humanity_name\",\"$date1\",\"$date2\"
\"$loc_entity_name_office\",\"$loc_entity_name_office $loc_property_name_address\", \"$loc_entity_name_username\", \"$loc_property_name_description\"\n";  }
    else {
      $out .= htmlspecialchars("$date1 .. $date2");
      $out .= '<table> <tr> <th>'.$loc_entity_name_office.'</th> <th>'.$loc_entity_name_username.'</th> <th>'.$loc_property_name_description.'</th> </tr>';
    };

    while ( $r = mysqli_fetch_array($q) ) {
      if ( $csv == 'on' ) {
        $out .= '"'.htmlspecialchars($r['office_name']).'", "'.htmlspecialchars($r['office_address']).'","'.htmlspecialchars($r['user']).'","'.htmlspecialchars($r['comment'])."\"\n";
      } else {
        $out .= '<tr> <td>'.htmlspecialchars($r['office_name']).' '.htmlspecialchars($r['office_address']).'</td>
                      <td>'.htmlspecialchars($r['user']).'</td> <td>'.htmlspecialchars($r['comment']).'</td> </tr>';
      };
    }
    if ( $csv == 'on' ) { $out .= '</textarea>'; } else { $out .= '</table>'; };
  }

?>

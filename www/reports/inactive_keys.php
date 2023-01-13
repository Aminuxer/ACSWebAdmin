<?php

  if ( ! isset($_GET['date1']) ) {
    $out .= '<form method="GET">
                <input type="hidden" name="tab" value="reports">
                <input type="hidden" name="report" value="'.$report.'">
       <input type="date" name="date1" min="1970-01-01" value="'.date("Y-m-d").'"> <Br/>
       <input type="checkbox" name="show_csv" id="show_csv_lbl"> <label for="show_csv_lbl">CSV</label> <Br/>
       <input type="submit">
    </form>';
  } else {
    $date1 = mysqli_real_escape_string($conn, $_GET['date1']);
    $csv = isset ($_GET['show_csv']) ? $_GET['show_csv'] : 0;


    $q = mysqli_query($conn, "SELECT
   uk.user, uk.comment, of.name AS office_name, of.address AS office_address ,
   lak.datetime AS last_activity, lak.status_code, ec.name AS event_name, ec.severity_color,
   cn.name AS last_device_name, ofcn.name AS last_office
FROM user_keys uk
  LEFT JOIN offices of ON of.id = uk.office_id
  LEFT JOIN last_activity_keys lak ON lak.key = uk.key
  LEFT JOIN event_codes ec ON ec.id = lak.status_code AND ec.hw_type = lak.controller_hw_type
  LEFT JOIN controller_names cn ON cn.sn = lak.controller_sn AND cn.hw_type = lak.controller_hw_type
  LEFT JOIN offices ofcn ON ofcn.id = cn.office_id
WHERE
    (uk.key  NOT IN (SELECT DISTINCT (ev.card) FROM events ev WHERE ev.ts > '$date1 00:00:00')
             AND ( lak.datetime < '$date1 23:59:59' OR lak.datetime IS NULL) )
    ORDER BY of.name, uk.user");

    if ( $csv == 'on' ) { $out = "<textarea cols=\"160\" rows=\"40\">\n\"$humanity_name\",\".. $date1\"
\"$loc_entity_name_office ($loc_entity_name_key)\", \"$loc_entity_name_office $loc_property_name_address\", \"$loc_entity_name_username\", \"$loc_property_name_description\", \"$loc_property_name_last_activity\", \"\",  \"$loc_property_name_code\", \"$loc_entity_name_event\", \"$loc_entity_name_controller,$loc_entity_name_office\", \"$loc_entity_name_controller,$loc_property_name_name\"\n";
    } else {
      $out .= htmlspecialchars(" .. $date1");
      $out .= '<table> <tr> <th>'.$loc_entity_name_office.'</th> <th>'.$loc_entity_name_username.'</th> <th>'.$loc_property_name_description.'</th>
                            <th>'.$loc_property_name_last_activity.'</th> <th>'.$loc_property_name_code.', '.$loc_entity_name_event.'</th> </tr>';
    };

     while ( $r = mysqli_fetch_array($q) ) {
      if ( $csv == 'on' ) {
        $out .= '"'.htmlspecialchars($r['office_name']).'", "'.htmlspecialchars($r['office_address']).'","'.htmlspecialchars($r['user']).'","'.htmlspecialchars($r['comment']).'","'.$r['last_activity'].'","'.htmlspecialchars($r['severity_color']).'","'.$r['status_code'].'","'.htmlspecialchars($r['event_name']).'","'.htmlspecialchars($r['last_office']).'","'.htmlspecialchars($r['last_device_name'])."\"\n";
      } else {
      $out .= '<tr> <td>'.htmlspecialchars($r['office_name']).' '.htmlspecialchars($r['office_address']).'</td>
                    <td>'.htmlspecialchars($r['user']).'</td> <td>'.htmlspecialchars($r['comment']).'</td>
                    <td>'.htmlspecialchars($r['last_activity']).'</td>
                    <td style="background-color:'.$r['severity_color'].'">'.$r['status_code'].' '.htmlspecialchars($r['event_name'])
                     .'&nbsp;&nbsp;&nbsp;'.htmlspecialchars($r['last_office']).' '.htmlspecialchars($r['last_device_name']).'</td> </tr>';
      };
     }
     if ( $csv == 'on' ) { $out .= '</textarea>'; } else { $out .= '</table>'; };
  }
?>

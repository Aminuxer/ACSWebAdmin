<?php

 if ( ! isset($logged_user) or $logged_user == '' ) {
    print '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=..">'; die;
 }

 $reports_array = array(
    "presence_in_office",
    "presence_in_office_per_days",
    "outage_in_office",
    "inactive_keys",
    "alarm_events"
    );

 $report = isset($_GET['report']) ? htmlspecialchars($_GET['report']) : '';

 if ( $report == '' ) {
    $out = "<h3>$loc_menu_element_reports</h3>";
    foreach ( $reports_array as $name ) {
       $humanity_name = ${'loc_reports_'.$name};
       $out .= '<a href="?tab=reports&report='.$name.'">'.$humanity_name.'</a><Br/>';
    }
 } elseif ( ! in_array($report, $reports_array) )
 { $out .= "$loc_menu_element_reports :: $loc_common_phrase_not_found"; }
 else {
     $humanity_name = ${'loc_reports_'.$report};
     $out .= "<h3>$humanity_name</h3>";
     include("$report.php");
 };

?>

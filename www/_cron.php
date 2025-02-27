<?php

# 55  6  *  *  *   wget -O /dev/null -o /dev/null "https://web-path-to-thus-skud-server/z5r/_cron.php"

require("config.php");

# Delete old events
mysqli_query($conn, "DELETE FROM events WHERE event_code IN (16, 17, 21) AND ts < DATE_SUB(CURDATE(), INTERVAL 120 MONTH)");
mysqli_query($conn, "DELETE FROM events WHERE ts < DATE_SUB(CURDATE(), INTERVAL 10 YEAR)");

?>
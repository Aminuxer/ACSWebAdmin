<?php

require("config.php");

mysqli_query($conn, "DELETE FROM events WHERE event_code IN (16, 17, 21)");

?>
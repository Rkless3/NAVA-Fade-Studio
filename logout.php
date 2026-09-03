<?php

session_start();


/* Remove all customer session data */

$_SESSION = [];


/* Destroy session */

session_destroy();


/* Redirect to homepage */

header("Location: index.php");

exit();

?>
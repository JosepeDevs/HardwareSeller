<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
//esto NO hay que protegerlo
session_unset();
session_destroy();
header("Location: ../index.php");
exit;
?>
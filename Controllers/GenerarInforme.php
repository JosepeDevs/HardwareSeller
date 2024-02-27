<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

include_once("OperacionesSession.php");
$rolOK = AuthYRolAdmin();
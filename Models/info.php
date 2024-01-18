<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "Articulo dice: no está user en session";
    header("Location: index.php");
}
phpinfo();
?>

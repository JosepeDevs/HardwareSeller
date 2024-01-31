<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticulosLISTARMensajes dice: no está user en session";
    header("Location: index.php");
}

function borradoLogico($codigo){
    include_once("../Models/Articulo.php");
    $articulo = new Articulo();
    $operacionConfirmada = $articulo->borradoLogico($codigo);
    return $operacionConfirmada;
}
?>

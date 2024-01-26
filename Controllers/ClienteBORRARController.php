<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ClienteBorrarController dice: no está user en session";
    header("Location: index.php");
}

function borradoLogico($dni){
    include_once("/../Models/Cliente.php");
    $cliente = new Cliente();
    $operacionConfirmada = $cliente->borradoLogico($dni);
    return $operacionConfirmada;
}
?>

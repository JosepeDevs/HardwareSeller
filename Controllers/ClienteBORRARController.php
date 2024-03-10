<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "ClienteBorrarController dice: no está user en session";
    header("Location: ../index.php");
    exit;
}

function borradoLogicoCliente($dni){
    include_once("../Models/Cliente.php");
    $cliente = new Cliente();
    $operacionConfirmada = $cliente->borradoLogicoCliente($dni);
    return $operacionConfirmada;
}
?>

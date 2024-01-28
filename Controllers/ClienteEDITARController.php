<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ClienteEDITARController dice: no está user en session";
    header("Location: /../Views/index.php");
}

function getClienteByDni($dni){
    include_once("/../Models/Cliente.php");
    $cliente = Cliente::getClienteByDni($dni);
    return $cliente;
}

function getArrayAtributos(){
    include_once("/../Models/Cliente.php");
    $arrayArticulos = getArrayAtributos();
    return $arrayArticulos;
}
?>
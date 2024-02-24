<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ClienteEDITARController dice: no está user en session";
    header("Location: ../index.php");
}

function getClienteByDni($dni){
    include_once("../Models/Cliente.php");
    $cliente = Cliente::getClienteByDni($dni);
    return $cliente;
}

function getClienteByEmail($email){
    include_once("../Models/Cliente.php");
    $cliente = Cliente::getClienteByEmail($email);
    return $cliente;
}



function getArrayAtributosCliente(){
    include_once("../Models/Cliente.php");
    $arrayArticulos = Cliente::getArrayAtributosCliente();
    return $arrayArticulos;
}
?>
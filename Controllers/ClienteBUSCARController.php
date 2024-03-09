<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

//NO proteger esto o no dejará hacer compras sin registrarse

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
<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "ContenidoPedidoEDITAR dice: no está user en session";
    header("Location: index.php");
    exit;
}

function GetContenidoPedidoByBusquedaNumPedido($numPedido){
    include_once("../Models/ContenidoPedido.php");
    $ContenidoPedido = ContenidoPedido::GetContenidoPedidoByNumPedido($numPedido);
    return $ContenidoPedido;
}

function getArrayAtributosContenidoPedido(){
    include_once("../Models/ContenidoPedido.php");
    $arrayContenidoPedido = ContenidoPedido::getArrayAtributosContenidoPedido();
    return $arrayContenidoPedido;
}
?>

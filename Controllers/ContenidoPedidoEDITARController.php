<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedidoEDITAR dice: no está user en session";
    header("Location: index.php");
}

function GetContenidoPedidoByBusquedaNumPedido($numPedido){
    include_once("../Models/ContenidoPedido.php");
    $ContenidoPedido = ContenidoPedido::GetContenidoPedidoByBusquedaNumPedido($numPedido);
    return $ContenidoPedido;
}

function getArrayAtributosContenidoPedido(){
    include_once("../Models/ContenidoPedido.php");
    $arrayContenidoPedido = ContenidoPedido::getArrayAtributosContenidoPedido();
    return $arrayContenidoPedido;
}
?>

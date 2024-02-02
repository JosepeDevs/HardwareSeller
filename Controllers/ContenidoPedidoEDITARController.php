<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedidoEDITAR dice: no está user en session";
    header("Location: index.php");
}

function GetContenidoPedidosByBusquedaNumPedido($numPedido){
    include_once("../Models/ContenidoPedido.php");
    $ContenidoPedido = ContenidoPedido::GetContenidoPedidosByBusquedaNumPedido($numPedido);
    return $ContenidoPedido;
}

function getArrayAtributosContenidoPedido(){
    include_once("../Models/ContenidoPedido.php");
    $arrayContenidoPedidos = ContenidoPedido::getArrayAtributosContenidoPedido();
    return $arrayContenidoPedidos;
}
?>

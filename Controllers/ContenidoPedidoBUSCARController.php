<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedidoBUSCAR dice: no está user en session";
    header("Location: /index.php");
}


function getContenidoPedidoByNumPedido($numPedido){
    include_once("../Models/ContenidoPedido.php");
    $ContenidoPedido = ContenidoPedido::GetContenidoPedidoBynumPedido($numPedido);
    if($ContenidoPedido == false){
        $_SESSION['numPedidoNotFound'] = true;
        return false;
    } else{
        return $ContenidoPedido;
    }
}

function getArrayAtributosContenidoPedido(){
    include_once("../Models/ContenidoPedido.php");
    $arrayContenidoPedido = ContenidoPedido::getArrayAtributosContenidoPedido();
    return $arrayContenidoPedido;
}

function GetContenidoPedidoByBusquedacodArticulo($codArticulo){
    include_once("../Models/ContenidoPedido.php");
    $arrayContenidoPedido = ContenidoPedido::GetContenidoPedidoByBusquedacodArticulo($codArticulo);
    return $arrayContenidoPedido;
}
?>
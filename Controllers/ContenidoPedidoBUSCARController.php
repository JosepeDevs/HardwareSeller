<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedidoBUSCAR dice: no está user en session";
    header("Location: /index.php");
}


function getContenidoPedidoBynumPedido($numPedido){
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
    $arrayContenidoPedidos = ContenidoPedido::getArrayAtributosContenidoPedido();
    return $arrayContenidoPedidos;
}

function GetContenidoPedidosByBusquedacodArticulo($codArticulo){
    include_once("../Models/ContenidoPedido.php");
    $arrayContenidoPedidos = ContenidoPedido::GetContenidoPedidosByBusquedacodArticulo($codArticulo);
    return $arrayContenidoPedidos;
}
?>
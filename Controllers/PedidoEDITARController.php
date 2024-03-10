<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "PedidoEDITAR dice: no está user en session";
    header("Location: index.php");
    exit;
}

function getPedidoByIdPedido($numPedido){
    include_once("../Models/Pedido.php");
    $Pedido = Pedido::GetPedidoByIdPedido($numPedido);
    return $Pedido;
}

function getArrayAtributosPedido(){
    include_once("../Models/Pedido.php");
    $arrayPedido = Pedido::getArrayAtributosPedido();
    return $arrayPedido;
}
?>

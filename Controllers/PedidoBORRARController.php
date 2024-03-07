<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "PedidoLISTARMensajes dice: no está user en session";
    header("Location: index.php");
    exit;
}

function borradoLogicoPedido($idPedido){
    include_once("../Models/Pedido.php");
    $operacionConfirmada = Pedido::borradoLogicoPedido($idPedido);
    return $operacionConfirmada;
}

function SePuedeCancelarPedido($estado){
    include_once("../Models/Pedido.php");
    $estadoCancelable = Pedido::SePuedeCancelarPedido($estado);
    return $estadoCancelable;
}




?>

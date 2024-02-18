<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedidoLISTARMensajes dice: no está user en session";
    header("Location: index.php");
}

function borradoLogicoContenidoPedido($numPedido){
    include_once("../Models/ContenidoPedido.php");
    $ContenidoPedido = new ContenidoPedido();
    $operacionConfirmada = $ContenidoPedido->borradoLogicoContenidoPedido($numPedido);
    return $operacionConfirmada;
}
?>

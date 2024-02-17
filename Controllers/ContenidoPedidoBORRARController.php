<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedidoLISTARMensajes dice: no está user en session";
    header("Location: index.php");
}

function borradoLogico($numPedido){
    include_once("../Models/ContenidoPedido.php");
    $ContenidoPedido = new ContenidoPedido();
    $operacionConfirmada = $ContenidoPedido->borradoLogico($numPedido);
    return $operacionConfirmada;
}
?>

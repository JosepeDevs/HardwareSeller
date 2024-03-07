<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "PedidoBUSCAR dice: no está user en session";
    header("Location: /index.php");
    exit;

}


function getArrayAtributosPedido(){
    include_once("../Models/Pedido.php");
    $arrayPedido = Pedido::getArrayAtributosPedido();
    return $arrayPedido;
}

function getPedidoByIdPedido($idPedido, $dni=null){
    include_once("../Models/Pedido.php");
    $Pedido = Pedido::getPedidoByIdPedido($idPedido, $dni);
    if($Pedido == false){
       // $_SESSION['numPedidoNotFound'] = true;
        return false;
    } else{
        return $Pedido;
    }
}
function getPedidosByEstado($estado, $dni=null){
    include_once("../Models/Pedido.php");
    $Pedido = Pedido::getPedidosByEstado($estado, $dni);
    if($Pedido == false){
        return false;
    } else{
        return $Pedido;
    }
}

function GetPedidosByRangoFecha($fechaInicio, $fechaFin, $dni=null){
    include_once("../Models/Pedido.php");
    $arrayPedido = Pedido::GetPedidosByRangoFecha($fechaInicio, $fechaFin, $dni);
    return $arrayPedido;
}
function getPedidosByCodUsuario($codUsuario, $dni=null){
    include_once("../Models/Pedido.php");
    $arrayPedido = Pedido::getPedidosByCodUsuario($codUsuario, $dni);
    return $arrayPedido;
}
?>
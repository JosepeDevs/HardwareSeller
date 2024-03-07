<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "CategoriaVALIDAR dice: no está user en session";
   header("Location: /index.php");
   exit;
}
/**
 * $opcion puede ser "ASC" or "DESC", en función de eso devolverá los datos ordenados ASC o DESC
 */
function getArrayPedidosOrdenadosByAtributo($orden,$nombreAtributo, $dni =null){
    include_once("../Models/Pedido.php");
    if($orden == "ASC"){
        $arrayPedidos= Pedido::getASCSortedPedidosByAtributo($nombreAtributo, $dni);
        return $arrayPedidos;
    } else if($orden == "DESC"){
        $arrayPedidos= Pedido::getDESCSortedPedidosByAtributo($nombreAtributo, $dni);
        return $arrayPedidos;
    }else{
        $arrayPedidos= Pedido::getAllPedidos($dni);
        return $arrayPedidos;
    }
}

?>
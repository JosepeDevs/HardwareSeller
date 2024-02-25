<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "CategoriaVALIDAR dice: no está user en session";
   header("Location: /index.php");
}
/**
 * $opcion puede ser "ASC" or "DESC", en función de eso devolverá los datos ordenados ASC o DESC
 */
function getArrayContenidoPedidoOrdenadosByAtributo($orden,$nombreAtributo, $dni=null){
    include_once("../Models/ContenidoPedido.php");
    if($orden == "ASC"){
        $arrayContenidoPedido= ContenidoPedido::getASCSortedContenidoPedidoByAtributo($nombreAtributo, $dni);
        return $arrayContenidoPedido;
    } else if($orden == "DESC"){
        $arrayContenidoPedido= ContenidoPedido::getDESCSortedContenidoPedidoByAtributo($nombreAtributo, $dni);
        return $arrayContenidoPedido;
    }else{
        $arrayContenidoPedido= ContenidoPedido::getAllContenidoPedido($dni);
        return $arrayContenidoPedido;
    }
}

?>
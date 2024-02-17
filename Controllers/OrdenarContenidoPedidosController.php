<?php

/**
 * $opcion puede ser "ASC" or "DESC", en función de eso devolverá los datos ordenados ASC o DESC
 */
function getArrayContenidoPedidosOrdenadosByAtributo($orden,$nombreAtributo){
    include_once("../Models/ContenidoPedido.php");
    if($orden == "ASC"){
        $arrayContenidoPedidos= ContenidoPedido::getASCSortedContenidoPedidosByAtributo($nombreAtributo);
        return $arrayContenidoPedidos;
    } else if($orden == "DESC"){
        $arrayContenidoPedidos= ContenidoPedido::getDESCSortedContenidoPedidosByAtributo($nombreAtributo);
        return $arrayContenidoPedidos;
    }else{
        $arrayContenidoPedidos= ContenidoPedido::getAllContenidoPedidos();
        return $arrayContenidoPedidos;
    }
}

?>
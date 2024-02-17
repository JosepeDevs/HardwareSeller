<?php

/**
 * $opcion puede ser "ASC" or "DESC", en función de eso devolverá los datos ordenados ASC o DESC
 */
function getArrayContenidoPedidoOrdenadosByAtributo($orden,$nombreAtributo){
    include_once("../Models/ContenidoPedido.php");
    if($orden == "ASC"){
        $arrayContenidoPedido= ContenidoPedido::getASCSortedContenidoPedidoByAtributo($nombreAtributo);
        return $arrayContenidoPedido;
    } else if($orden == "DESC"){
        $arrayContenidoPedido= ContenidoPedido::getDESCSortedContenidoPedidoByAtributo($nombreAtributo);
        return $arrayContenidoPedido;
    }else{
        $arrayContenidoPedido= ContenidoPedido::getAllContenidoPedido();
        return $arrayContenidoPedido;
    }
}

?>
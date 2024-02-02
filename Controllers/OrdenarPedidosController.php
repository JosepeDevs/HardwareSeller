<?php

/**
 * $opcion puede ser "ASC" or "DESC", en función de eso devolverá los datos ordenados ASC o DESC
 */
function getArrayPedidosOrdenadosByAtributo($orden,$nombreAtributo){
    include_once("../Models/Pedido.php");
    if($orden == "ASC"){
        $arrayPedidos= Pedido::getASCSortedPedidosByAtributo($nombreAtributo);
        return $arrayPedidos;
    } else if($orden == "DESC"){
        $arrayPedidos= Pedido::getDESCSortedPedidosByAtributo($nombreAtributo);
        return $arrayPedidos;
    }else{
        $arrayPedidos= Pedido::getAllPedidos();
        return $arrayPedidos;
    }
}

?>
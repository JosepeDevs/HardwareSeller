<?php

/**
 * $opcion puede ser "ASC" or "DESC", en función de eso devolverá los datos ordenados ASC o DESC
 */
function getArrayPedidosOrdenadosByAtributo($orden,$nombreAtributo, $dni =null){
    include_once("../Models/Pedido.php");
    if($orden == "ASC"){
        $arrayPedidos= Pedido::getASCSortedPedidosByAtributo($nombreAtributo, $dni = null);
        return $arrayPedidos;
    } else if($orden == "DESC"){
        $arrayPedidos= Pedido::getDESCSortedPedidosByAtributo($nombreAtributo, $dni = null);
        return $arrayPedidos;
    }else{
        $arrayPedidos= Pedido::getAllPedidos($dni=null);
        return $arrayPedidos;
    }
}

?>
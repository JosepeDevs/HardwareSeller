<?php

/**
 * $opcion puede ser "ASC" or "DESC", en función de eso devolverá los datos ordenados ASC o DESC
 */
function getArrayArticulosOrdenados($orden){
    include_once("../Models/Articulo.php");
    $orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
    if($orden == "ASC"){
        $arrayArticulos= Articulo::getASCSortedArticulos();
        return $arrayArticulos;
    } else if($orden == "DESC"){
        $arrayArticulos= Articulo::getDESCSortedArticulos();
        return $arrayArticulos;
    }else{
        $arrayArticulos= Articulo::getAllArticulos();
        return $arrayArticulos;
    }
}

?>
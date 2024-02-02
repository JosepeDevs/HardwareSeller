<?php

/**
 * $opcion puede ser "ASC" or "DESC", en función de eso devolverá los datos ordenados ASC o DESC
 */
function getArrayCategoriasOrdenados($orden){
    include_once("../Models/Categoria.php");
    $orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
    if($orden == "ASC"){
        $arrayCategorias= Categoria::getASCSortedCategorias();
        return $arrayCategorias;
    } else if($orden == "DESC"){
        $arrayCategorias= Categoria::getDESCSortedCategorias();
        return $arrayCategorias;
    }else{
        $arrayCategorias= Categoria::getAllCategorias();
        return $arrayCategorias;
    }
}

?>
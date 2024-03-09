<?php

//no puedo protegerlo, si lo protejo no se pueden filtrar por categoría los artículos

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

function getArrayArticulosOrdenadosByAtributo($orden,$nombreAtributo){
    include_once("../Models/Articulo.php");
    if($orden == "ASC"){
        $arrayArticulos= Articulo::getASCSortedArticulosByAtributo($nombreAtributo);
        return $arrayArticulos;
    } else if($orden == "DESC"){
        $arrayArticulos= Articulo::getDESCSortedArticulosByAtributo($nombreAtributo);
        return $arrayArticulos;
    }else{
        $arrayArticulos= Articulo::getAllArticulos();
        return $arrayArticulos;
    }
}

function getArrayArticulosFiltradosByCodigoCategoria($arrayArticulos, $codigoCategoria){
    include_once("../Models/Articulo.php");
    $arrayArticulosFiltrados = array();
    foreach ($arrayArticulos as $articulo) {
        if($articulo->getCategoria() == $codigoCategoria){
            $arrayArticulosFiltrados[] = $articulo;
        }
    }
    if(count(  $arrayArticulosFiltrados )==0){
        if($codigoCategoria !== null){ //si no han intentado filtrar no subir a session el fallo, solo queremos subir esto si intentan filtrar y no encuentra nada
            $_SESSION['NoSePudoFiltrar'] = true;
        }
        return $arrayArticulos;
    } else{
        return $arrayArticulosFiltrados;
    }

}
?>
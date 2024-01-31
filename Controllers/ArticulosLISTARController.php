<?php

function getArrayAtributos(){
    include_once("../Models/Articulo.php");
    $arrayArticulos = getArrayAtributos();
    return $arrayArticulos;
}

function  getArrayPaginado($arrayObjetos, $filasAMostrar, $paginaActual){
    //return de qué indice a qué indice debe imprimir en el $arrayArticulos que nos llega aquí
     //PAGINACIÓN
     $arrayAImpimir=[];
     $filasTotales = count($arrayObjetos);

     if(is_numeric($paginaActual)){
        $ultimoRegistroMostrado = $paginaActual * $filasAMostrar;
        $finalRegistro = min($ultimoRegistroMostrado + $filasAMostrar, $filasTotales);
         for($i=$ultimoRegistroMostrado ; $i < $finalRegistro; $i++){
             $codigo = $arrayObjetos[$i]->getCodigo();
             $nombre = $arrayObjetos[$i]->getNombre();
             $descripcion = $arrayObjetos[$i]->getDescripcion();
             $categoria = $arrayObjetos[$i]->getCategoria();
             $precio = $arrayObjetos[$i]->getPrecio();
             $imagen = $arrayObjetos[$i]->getImagen();
             $articulo = new Articulo($codigo, $nombre, $descripcion, $categoria, $precio, $imagen);
             $arrayAImpimir[]=$articulo;
         }
         return $arrayAImpimir;
     }
     if($paginaActual == "X"){
         $arrayAImpimir = $arrayObjetos;
         return $arrayAImpimir;
     }
     return null;
}

?>
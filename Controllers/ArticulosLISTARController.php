<?php
//NECESARIO PARA CATALOGO NO PROTEGER
function getArrayAtributosArticulo(){
    include_once("../Models/Articulo.php");
    $arrayArticulos = Articulo::getArrayAtributosArticulo();
    return $arrayArticulos;
}

/**
 * @return array|bool devuelve array con artículos si tiene éxito, devuelve false si no tiene éxito
 */
function  getArrayPaginadoArticulos($arrayObjetos, $articulosAMostrar, $paginaActual){
    //return de qué indice a qué indice debe imprimir en el $arrayArticulos que nos llega aquí
     //PAGINACIÓN
     $arrayAImpimir=[];
     $filasTotales = count($arrayObjetos);

     if(is_numeric($paginaActual)){
        $ultimoRegistroMostrado = $paginaActual * $articulosAMostrar;
    }

    if(is_numeric($paginaActual)){
        $ultimoRegistroMostrado = $paginaActual * $articulosAMostrar;
        $finalRegistro = min($ultimoRegistroMostrado + $articulosAMostrar, $filasTotales);
        $arrayAtributos = Articulo::getArrayAtributosArticulo();
        for($i=$ultimoRegistroMostrado ; $i < $finalRegistro; $i++){
            foreach ($arrayAtributos as $index => $atributo) {
                $nombreAtributo = $atributo;
                $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                $valor = $arrayObjetos[$i]->$getter();//lo llamamos para obtener el valor
                $arrayValores[] = $valor;//metemos cada valor en un array, como se llaman en orden luego puedo meterlos en el constructor con ese mismo orden.
            }
            $articulo = new Articulo(...$arrayValores); //resulta que si llamo el operador de propagación mete los contenidos del array como parametros del método constructor
            $arrayAImpimir[] = $articulo;
            $arrayValores=[];//reiniciamos este array
        }
        return $arrayAImpimir;
    }
    if($paginaActual == "X"){
        $arrayAImpimir=$arrayObjetos;
        return $arrayAImpimir;
    }
    return false;
}


?>
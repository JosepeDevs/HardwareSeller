<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");

checkAdminOEmpleado();

function getArrayAtributosCategoria(){
    include_once("../Models/Categoria.php");
    $arrayCategorias = Categoria::getArrayAtributosCategoria();
    return $arrayCategorias;
}

/**
 * @return array|bool devuelve array con clientes si tiene éxito, devuelve false si no tiene éxito
 */
function  getArrayPaginadoCategorias($arrayObjetos, $filasAMostrar, $paginaActual){
    //return de qué indice a qué indice debe imprimir en el $arrayCategorias que nos llega aquí
     //PAGINACIÓN
     $arrayAImpimir=[];
     $filasTotales = count($arrayObjetos);

     if(is_numeric($paginaActual)){
        $ultimoRegistroMostrado = $paginaActual * $filasAMostrar;
    }

    if(is_numeric($paginaActual)){
        $ultimoRegistroMostrado = $paginaActual * $filasAMostrar;
        $finalRegistro = min($ultimoRegistroMostrado + $filasAMostrar, $filasTotales);
        $arrayAtributos = Categoria::getArrayAtributosCategoria();
        for($i=$ultimoRegistroMostrado ; $i < $finalRegistro; $i++){
            foreach ($arrayAtributos as $index => $atributo) {
                $nombreAtributo = $atributo;
                $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                $valor = $arrayObjetos[$i]->$getter();//lo llamamos para obtener el valor
                $arrayValores[] = $valor;//metemos cada valor en un array, como se llaman en orden luego puedo meterlos en el constructor con ese mismo orden.
            }
            $Categoria = new Categoria(...$arrayValores); //resulta que si llamo el operador de propagación mete los contenidos del array como parametros del método constructor
            $arrayAImpimir[] = $Categoria;
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
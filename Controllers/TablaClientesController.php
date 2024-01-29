<?php
/**
 * $opcion puede ser "ASC" or "DESC", en función de eso devolverá los datos ordenados ASC o DESC
 */
function getArrayClientesOrdenados($orden){
    //PREPARA/OBTEN DATOS DE LOS OBJETOS
    include_once("../Models/Cliente.php");
    $orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
    if($orden == "ASC"){
        $arrayClientes = Cliente::getASCSortedClients();
        return $arrayClientes;
    } else if($orden == "DESC"){
        $arrayClientes = Cliente::getDESCSortedClients();
        return $arrayClientes;
    }else{
        //si es null o cualquier otro caso
        $arrayClientes= Cliente::getAllClients();
        return $arrayClientes;
    }
}
function getArrayAtributosCliente(){
    include_once("../Models/Cliente.php");
    $arrayAtributosCliente = Cliente::getArrayAtributosCliente();
    return $arrayAtributosCliente;
}

function  getArrayPaginado($arrayClientes, $filasAMostrar, $paginaActual){
    //return de qué indice a qué indice debe imprimir en el $arrayArticulos que nos llega aquí
     //PAGINACIÓN
     $arrayAImpimir=[];
     $filasTotales = count($arrayClientes);

            if(is_numeric($paginaActual)){
                $ultimoRegistroMostrado = $paginaActual * $filasAMostrar;
            }

            if(is_numeric($paginaActual)){
                $ultimoRegistroMostrado = $paginaActual * $filasAMostrar;
                $finalRegistro = min($ultimoRegistroMostrado + $filasAMostrar, $filasTotales);
                $arrayAtributos = getArrayAtributosCliente();
                for($i=$ultimoRegistroMostrado ; $i < $finalRegistro; $i++){
                    foreach ($arrayAtributos as $index => $atributo) {
                        $nombreAtributo = $atributo;
                        $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                        if($atributo == "psswrd"){
                            $valor=null; //no queremos pasar la contraseña, porque no se mostrará
                        }else{
                            $valor = $arrayClientes[$i]->$getter();//lo llamamos para obtener el valor
                        }
                        $arrayValores[] = $valor;//metemos cada valor en un array, como se llaman en orden luego puedo meterlos en el constructor con ese mismo orden.
                    }
                    $cliente = new Cliente(...$arrayValores); //resulta que si llamo el operador de propagación mete los contenidos del array como parametros del método constructor
                    $arrayAImpimir[] = $cliente;
                    $arrayValores=[];//reiniciamos este array
                }
                return $arrayAImpimir;
            }
            if($paginaActual == "X"){
                $arrayAImpimir=$arrayClientes;
                return $arrayAImpimir;
            }
            return null;
}

?>
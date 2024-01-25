<?php
/**
 * $opcion puede ser "ASC" or "DESC", en función de eso devolverá los datos ordenados ASC o DESC
 */
function getArrayClientesOrdenados($orden){
    //PREPARA/OBTEN DATOS DE LOS OBJETOS
    include_once("/../Models/Cliente.php");
    $orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
    if($orden == "ASC"){
        $arrayClientes = Cliente::getASCSortedClientes();
        return $arrayClientes;
    } else if($orden == "DESC"){
        $arrayClientes = Cliente::getDESCSortedClientes();
        return $arrayClientes;
    }else{
        //si es null o cualquier otro caso
        $arrayClientes= Cliente::getAllClientes();
        return $arrayAClientes;
    }
}
function getArrayAtributos(){
    include_once("/../Models/Cliente.php");
    $arrayArticulos = getArrayAtributos();
    return $arrayArticulos;
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
                $j = 0; // Variable para indexar $arrayAImpimir (era para intentar solucionar no mostrar datos del admin logeado)
                for($i=$ultimoRegistroMostrado ; $i < $finalRegistro; $i++){
                    $nombre = $arrayClientes[$i]->getNombre();
                    $direccion = $arrayClientes[$i]->getDireccion();//.".".$j.".".$finalRegistro;
                    $localidad = $arrayClientes[$i]->getLocalidad();
                    $provincia = $arrayClientes[$i]->getProvincia();
                    $telefono = $arrayClientes[$i]->getTelefono();
                    $email = $arrayClientes[$i]->getEmail();
                    $dni = $arrayClientes[$i]->getDni();
                    $rol = $arrayClientes[$i]->getRol();
                    $psswrd=null;
                    $cliente = new Cliente($dni,$nombre,$direccion,$localidad,$provincia,$telefono,$email,$psswrd,$rol);
                    $arrayAImpimir[] = $cliente;
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
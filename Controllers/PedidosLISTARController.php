<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "CategoriaVALIDAR dice: no está user en session";
   header("Location: /index.php");
   exit;
}
function getArrayAtributosPedido(){
    include_once("../Models/Pedido.php");
    $arrayPedidos = Pedido::getArrayAtributosPedido();
    return $arrayPedidos;
}

/**
 * @return array|bool devuelve array con clientes si tiene éxito, devuelve false si no tiene éxito
 */
function  getArrayPaginadoPedidos($arrayObjetos, $filasAMostrar, $paginaActual){
    //return de qué indice a qué indice debe imprimir en el $arrayPedidos que nos llega aquí
     //PAGINACIÓN
     $arrayAImpimir=[];
     $filasTotales = count($arrayObjetos);

     if(is_numeric($paginaActual)){
        $ultimoRegistroMostrado = $paginaActual * $filasAMostrar;
    }

    if(is_numeric($paginaActual)){
        $ultimoRegistroMostrado = $paginaActual * $filasAMostrar;
        $finalRegistro = min($ultimoRegistroMostrado + $filasAMostrar, $filasTotales);
        $arrayAtributos = Pedido::getArrayAtributosPedido();
        for($i=$ultimoRegistroMostrado ; $i < $finalRegistro; $i++){
            foreach ($arrayAtributos as $index => $atributo) {
                $nombreAtributo = $atributo;
                $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                $valor = $arrayObjetos[$i]->$getter();//lo llamamos para obtener el valor
                $arrayValores[] = $valor;//metemos cada valor en un array, como se llaman en orden luego puedo meterlos en el constructor con ese mismo orden.
            }
            $Pedido = new Pedido(...$arrayValores); //resulta que si llamo el operador de propagación mete los contenidos del array como parametros del método constructor
            $arrayAImpimir[] = $Pedido;
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
<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
//si protejo esto no puedo buscar items sin registrarme

/**
 * Funcion que se llama para comprobar si ha habido algún error o para mostrar un mensaje de operación realizada correctamente. Obtiene el resultado consultando SESSION.
 * @return array|bool Devuelve array de strings si detecta algún mensaje de confirmación/error, si no, devuelve false.
 */
Function getArrayMensajesArticulos(){
    if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $mensajes=[];

    if(isset($_SESSION['OperationFailed']) && $_SESSION['OperationFailed'] == true){
        $_SESSION['OperationFailed']=false;
        $mensajes[] = "Operación no completada, es posible que hubiera un pproblema con la conenxión a la base de datos.";
    }
    if(isset($_SESSION['CodigoNotFound']) && $_SESSION['CodigoNotFound'] == true){
        $_SESSION['CodigoNotFound']=false;
        $mensajes[] = "No se encontró el código consultado.";
    }
    if(isset($_SESSION['NombreNotFound']) && $_SESSION['NombreNotFound'] == true){
        $_SESSION['NombreNotFound']=false;
        $mensajes[] = "No se encontró ningún artículo que contenga su consulta en el nombre.";
    }

    if( count($mensajes) == 0){
        return false;//si no encontró ningún mensaje a mostrar devolverá false;
    } else{
        return $mensajes;
    }}
?>
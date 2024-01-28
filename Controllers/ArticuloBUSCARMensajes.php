<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticulosLISTARMensajes dice: no está user en session";
    header("Location: index.php");
}

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

    if( count($mensajes) == 0){
        return false;//si no encontró ningún mensaje a mostrar devolverá false;
    } else{
        return $mensajes;
    }}
?>
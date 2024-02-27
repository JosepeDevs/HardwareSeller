<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "PedidosLISTARMensajes dice: no está user en session";
    header("Location: index.php");
}
/**
 * Funcion que se llama para comprobar si ha habido algún error o para mostrar un mensaje de operación realizada correctamente. Obtiene el resultado consultando SESSION.
 * @return array|bool Guarda todos los mensajes que encuentre en un array que devulve, si no hay coincidencias devuelve false.
 */
Function getArrayMensajesPedidos(){
    if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $mensajes=[];

    if(isset($_SESSION['ProblemaArticuloMasVendido']) && $_SESSION['ProblemaArticuloMasVendido'] == true){
        $mensajes[] =  "Hubo un problema al generar obtener el artículo más vendido .";
        unset($_SESSION['ProblemaArticuloMasVendido']);
    }
   
    if(isset($_SESSION['BadArticulos']) && $_SESSION['BadArticulos'] == true){
        $mensajes[] =  "No se pudo generar el informe de artículos.";
        unset($_SESSION['BadArticulos']);
    }
    if(isset($_SESSION['InformeGenerado']) && $_SESSION['InformeGenerado'] == true){
        $mensajes[] =  "Informe generado correctemente, descarga debería empezar en breve.";
        unset($_SESSION['InformeGenerado']);
    }
   


    if( count($mensajes) == 0){
        return false;//si no encontró ningún mensaje a mostrar devolverá false;
    } else{
        return $mensajes;
    }
}
?>
<?php

//NO PROTEGER, MUESTRA MENSAJES DE RECUPERAR PASSWORD Y ESO NO ESTÁ PROTEGIDO

/**
 * Funcion que se llama para comprobar si ha habido algún error o para mostrar un mensaje de operación realizada correctamente. Obtiene el resultado consultando SESSION.
 * @$mensajes[]= string|bool Devuelve texto si detecta algún mensaje de confirmación/error, si no, devuelve false.
 */
Function getArrayMensajes(){
    if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    ////print_r($_SESSION);;
    $mensajes=[];

    if(isset($_SESSION['ClienteNoExiste']) && $_SESSION['ClienteNoExiste'] == true){
        unset($_SESSION['ClienteNoExiste']);
        $mensajes[] = "El email y dni indicados no tuvieron resultados, pruebe de nuevo por favor.";
    }

    if(isset($_SESSION['PsswrdSeQuedaIgual']) && $_SESSION['PsswrdSeQuedaIgual'] == true){
        unset($_SESSION['PsswrdSeQuedaIgual']);
        $mensajes[] = "Hubo algún problema al actualizar su contraseña ¿quizás fuera muy larga?";
    }

    if( count($mensajes) == 0){
        return false;//si no encontró ningún mensaje a mostrar devolverá false;
    } else{
        return $mensajes;
    }
}
?>
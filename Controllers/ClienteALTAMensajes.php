<?php

//NO PROTEGER (se accede desde clienteneuvo que no debe tener protección activada)


/**
 * Funcion que se llama para comprobar si ha habido algún error o para mostrar un mensaje de operación realizada correctamente. Obtiene el resultado consultando SESSION.
 * @return array|bool Guarda todos los mensajes que encuentre en un array que devulve, si no hay coincidencias devuelve false.
 */
Function getArrayMensajesNuevo(){
    if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $mensajes=[];

    if(isset($_SESSION['GoodInsertCliente']) && $_SESSION['GoodInsertCliente'] == true){
        $mensajes[] =  "Cliente añadido correctamente.";
        unset($_SESSION['GoodInsertCliente']);
    };
    if(isset($_SESSION['BadSemiInsertCliente']) && $_SESSION['BadSemiInsertCliente'] == true){
        $mensajes[] =  "Ocurrio algun problema al procesar sus  datos y no fue posible pasar a método de pago, por favor, revíselo.";
        unset($_SESSION['BadSemiInsertCliente']);
    };

    if(isset($_SESSION['EmailAlreadyExists']) && $_SESSION['EmailAlreadyExists'] == true){
        $mensajes[] =  "Lo sentimos, el  correo indicado no está disponible o no se acepta, por favor, seleccione otro.";
        unset($_SESSION['EmailAlreadyExists']);
    }

    if(isset($_SESSION['BadInsertCliente']) && $_SESSION['BadInsertCliente'] == true){
        $mensajes[] =  "hubo un fallo al insertar sus datos, quizás el DNI ya estuviera en uso.";
        unset($_SESSION['BadInsertCliente']);
    }

    if(isset($_SESSION['LongNombre']) && $_SESSION['LongNombre'] == true){
        $mensajes[] =  "Introdujo un nombre demasiado largo. Abrévielo por favor.";
        unset($_SESSION['LongNombre']);
    }

    if(isset($_SESSION['LongDireccion']) && $_SESSION['LongDireccion'] == true){
        $mensajes[] =  "Introdujo una dirección  demasiado larga. Abréviela por favor.";
        unset($_SESSION['LongDireccion']);
    }

    if(isset($_SESSION['LongLocalidad']) && $_SESSION['LongLocalidad'] == true){
        $mensajes[] =  "Introdujo una localidad demasiado largo. Abrévielo por favor.";
        unset($_SESSION['LongLocalidad']);
    }

    if(isset($_SESSION['LongProvincia']) && $_SESSION['LongProvincia'] == true){
        $mensajes[] =  "Introdujo una pronvicia demasiado largo. Abrévielo por favor.";
        unset($_SESSION['LongProvincia']);
    }

    if(isset($_SESSION['TelefonoMal']) && $_SESSION['TelefonoMal'] == true){
        $mensajes[] =  "El teléfono introducido no tenía un formato adecuado, por favor, pruebe con formatos como  444-555-123, 246.555.888,";
        unset($_SESSION['TelefonoMal']);
    }

    if(isset($_SESSION['EmailBadFormat']) && $_SESSION['EmailBadFormat'] == true){
        $mensajes[] =  "El formato del correo no es adecuado, se espera del tipo: algo@otroalgo.otroalgo";
        unset($_SESSION['EmailBadFormat']);
    }

    if(isset($_SESSION['DniBadFormat']) && $_SESSION['DniBadFormat'] == true){
        $mensajes[] =  "El formato del DNI no es correcto";
        unset($_SESSION['DniBadFormat']);
    }

    if( count($mensajes) == 0){
        return false;//si no encontró ningún mensaje a mostrar devolverá false;
    } else{
        return $mensajes;
    }
}
?>
<?php

//esto no puedo protegerlo porque se usa en index, donde aun no hay session.

/**
 * Funcion que se llama para comprobar si ha habido algún error o para mostrar un mensaje de operación realizada correctamente. Obtiene el resultado consultando SESSION.
 * @return @$mensajes[] = array de Strings|bool Devuelve texto si detecta algún mensaje de confirmación/error, si no, devuelve false.
 */
Function getArrayMensajesIndex(){

    if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $mensajes=[];

    if(isset($_SESSION['CuentaDesactivada']) && ($_SESSION['CuentaDesactivada'] == true)) {
        unset($_SESSION['CuentaDesactivada']);
        $mensajes[] = "Ha intentado acceder a una cuenta desactivada. Puede recuperar su contraseña para activarla";
    }
    if(isset($_SESSION['NoBorrarDniAjeno']) && ($_SESSION['NoBorrarDniAjeno'] == true)) {
        unset($_SESSION['NoBorrarDniAjeno']);
        $mensajes[] = "No puede intentar borrar un usuario con un dni diferente al suyo propio.";
    }
    if(isset($_SESSION['BadRol']) && ($_SESSION['BadRol'] == true)) {
        unset($_SESSION['BadRol']);
        $mensajes[] = "El rol no se pudo asignar correctamente.";
    }
    if(isset($_SESSION['UserNoSession']) && ($_SESSION['UserNoSession'] == true)) {
        unset($_SESSION['UserNoSession']);
        $mensajes[] = "El usuario no se encontró en session.";
    }

    if(isset($_SESSION['ExitoBorrandoCliente']) && ($_SESSION['ExitoBorrandoCliente'] == true)) {
        unset($_SESSION['ExitoBorrandoCliente'] );
        $mensajes[] = "La cuenta se borró correctamente. ";
    }

    if(isset($_SESSION['BadInsertCliente']) && ($_SESSION['BadInsertCliente'] == true)) {
        unset($_SESSION['BadInsertCliente']);
        $mensajes[] = "hubo un fallo al insertar sus datos, quizás el DNI ya estuviera en uso.";
    }
    if(isset($_SESSION['GoodInsertCliente']) && ($_SESSION['GoodInsertCliente'] == true)) {
        unset($_SESSION['GoodInsertCliente']);
        $mensajes[] = "Cliente añadido correctamente.";
    }

    if(isset($_SESSION['BadPsswrd']) && ($_SESSION['BadPsswrd'] == true)) {
        unset($_SESSION['BadPsswrd']);
        $mensajes[] = "La contraseña indicada no es correcta.";
    }

    if(isset($_SESSION['FailedAuth']) && ($_SESSION['FailedAuth'] == true)) {
        unset($_SESSION['FailedAuth'] );
        $mensajes[] = "No tenía y según nos consta, sigue sin tener permiso para acceder a ese recurso.";
    }

    if(isset($_SESSION['PsswrdActualizada']) && ($_SESSION['PsswrdActualizada'] == true)) {
        unset($_SESSION['PsswrdActualizada']);
        $mensajes[] = "La contraseña se actualizó correctamente.";
    }

    if(isset($_SESSION['NoExiste']) && ($_SESSION['NoExiste'] == true)) {
        unset($_SESSION['NoExiste']);
        $mensajes[] = "No tenemos esos datos en nuestra base de datos o algún dato introducido está mal.";
    }

    if(isset($_SESSION['OperationFailed']) && ($_SESSION['OperationFailed'] == true)) {
        unset($_SESSION['OperationFailed']);
        $mensajes[] = " operación falló de una forma inesperada, quizás la conexión a la base de datos no fue correcta";
    }

    if( count($mensajes) == 0){
        return false;//si no encontró ningún mensaje a mostrar devolverá false;
    } else{
        return $mensajes;
    }

}
?>
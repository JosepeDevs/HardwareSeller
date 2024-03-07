<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "EditarClienteMensajes dice: no está user en session";
    header("Location: index.php");
    exit;
}

/**
 * Funcion que se llama para comprobar si ha habido algún error o para mostrar un mensaje de operación realizada correctamente. Obtiene el resultado consultando SESSION.
 * @$mensajes[] =  string|bool Devuelve texto si detecta algún mensaje de confirmación/error, si no, devuelve false.
 */
Function getArrayMensajes(){
    if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $mensajes=[];

    if(isset($_SESSION['BadRol']) && ($_SESSION['BadRol'] == true)) {
        $mensajes[] =  "El rol no se pudo asignar correctamente." ;
        unset($_SESSION['BadRol']);
    }

    if(isset($_SESSION['LongNombre']) && ($_SESSION['LongNombre'] == true)) {
        unset($_SESSION['LongNombre']);
        $mensajes[] =  "El nombre que introdujo es muy largo, por favor, abrévielo, ponga un mote o cámbiese de nombre.";
    }

    if(isset($_SESSION['LongDireccion']) && ($_SESSION['LongDireccion'] == true)) {
        unset($_SESSION['LongDireccion']);
        $mensajes[] =  "La dirección que introdujo es muy larga, por favor, abreviela o cámbiese de domicilio. ";
    }

    if(isset($_SESSION['LongLocalidad']) && ($_SESSION['LongLocalidad'] == true)) {
        unset($_SESSION['LongLocalidad']);
        $mensajes[] =  "La localidad que introdujo es muy larga, por favor, abréviela o cámbiese de ciudad. ";
    }

    if(isset($_SESSION['LongProvincia']) && ($_SESSION['LongProvincia'] == true)) {
        unset($_SESSION['LongProvincia']);
        $mensajes[] =  "La provincia que introdujo es muy larga, por favor, abréviela o cámbiese de provincia. ";
    }

    if(isset($_SESSION['TelefonoMal']) && ($_SESSION['TelefonoMal'] == true)) {
        unset($_SESSION['TelefonoMal']);
        $mensajes[] =  "El teléfono introducido no tiene un formato correcto, pruebe con los siguientes formatos: 444-555-123, 246.555.888, 123555456...";
    }

    if(isset($_SESSION['EmailAlreadyExists']) && ($_SESSION['EmailAlreadyExists'] == true)) {
        unset($_SESSION['EmailAlreadyExists']);
        $mensajes[] =  "Lo sentimos, el  correo indicado no está disponible o no se acepta, por favor, seleccione otro.";
    }

    if(isset($_SESSION['EmailBadFormat']) && ($_SESSION['EmailBadFormat'] == true)) {
        unset($_SESSION['EmailBadFormat']);
        $mensajes[] =  "El formato del correo no es adecuado, se espera del tipo: algo@otroalgo.otroalgo";
    }

    if(isset($_SESSION['DniBadFormat']) && ($_SESSION['DniBadFormat'] == true)) {
        unset($_SESSION['DniBadFormat']);
        $mensajes[] =  "El formato del DNI no es correcto: Necesita 8 numeros y la letra correcta";
    }

    if(isset($_SESSION['BadUpdateCliente']) && ($_SESSION['BadUpdateCliente'] == true)) {
        unset($_SESSION['BadUpdateCliente']);
        $mensajes[] =  "El sistema tuvo un problema actualizando los datos del cliente, por favor, pruebe de nuevo.";
    }

    if(isset($_SESSION['ExitoBorrandoCliente']) && ($_SESSION['ExitoBorrandoCliente'] == true)) {
        unset($_SESSION['ExitoBorrandoCliente']);
        $mensajes[] =  "La cuenta se borró correctamente.";

    } elseif(isset($_SESSION['ExitoBorrandoCliente']) && ($_SESSION['ExitoBorrandoCliente'] == false)) {
        unset($_SESSION['ExitoBorrandoCliente']);
        $mensajes[] =  "La cuenta no ha sido borrada.";
    }

    if(isset($_SESSION['OperationFailed']) && ($_SESSION['OperationFailed'] == true)) {
        unset($_SESSION['OperationFailed']);
        $mensajes[] =  " operación falló de una forma inesperada, quizás la conexión a la base de datos no fue correcta";
    }

    if(isset($_SESSION['BorradoClienteCancelado']) && ($_SESSION['BorradoClienteCancelado'] == true)) {
        unset($_SESSION['BorradoClienteCancelado']);
        $mensajes[] =  "Borrado cancelado.";
    }
    if(isset($_SESSION['GoodUpdateCliente']) && ($_SESSION['GoodUpdateCliente'] == true)) {
        unset($_SESSION['GoodUpdateCliente']);
        $mensajes[] =  "Modificación realizada con éxito.";
    }

    if(isset($_SESSION['LongActivo']) && ($_SESSION['LongActivo'] == true)) {
        unset($_SESSION['LongActivo'] );
        $mensajes[]= " Indicó un estado no valido (solo puede ser 0 (desactivado) o 1 (activado))";
    }

    if( count($mensajes) == 0){
        return false;//si no encontró ningún mensaje a mostrar devolverá false;
    } else{
        return $mensajes;
    }
}


        /* DEPRECATED probablemente borrados en futuros commits


        if(isset($_SESSION['FailedAuth']) && ($_SESSION['FailedAuth'] == true)) {
        unset($_SESSION['FailedAuth']);
        $mensajes[] =  "La contraseña, el usuario o el rol no existen";
    }
    if(isset($_SESSION['GoodUpdateCliente']) && ($_SESSION['GoodUpdateCliente'] == true)) {
        $mensajes[] =  "Datos del cliente actualizados correctamente.";
        unset($_SESSION['GoodUpdateCliente']);
    }

    //ya no se permite la edición del DNI
        if(isset($_SESSION['DniNotFound']) && ($_SESSION['DniNotFound'] == true)) {
        unset($_SESSION['DniNotFound']);
        $mensajes[] =  "El DNI no se encontró.";
    }
    */
    /*de verdad hace falta esto?
    if(isset($_SESSION['GoodInsertCliente']) && ($_SESSION['GoodInsertCliente'] == true)) {
        unset($_SESSION['GoodInsertCliente']);
        $mensajes[] =  "Nuevo cliente añadido correctamente.";
    }

    if(isset($_SESSION['BadInsertCliente']) && ($_SESSION['BadInsertCliente'] == true)) {
        unset($_SESSION['BadInsertCliente']);
        $mensajes[] =  "hubo un fallo al insertar sus datos, quizás el DNI ya estuviera en uso.";
    }

    if(isset($_SESSION['BadPsswrd']) && ($_SESSION['BadPsswrd'] == true)) {
        unset($_SESSION['BadPsswrd']);
        $mensajes[] =  "La contraseña indicada no es correcta.";
    }

    if(isset($_SESSION['NoExiste']) && ($_SESSION['NoExiste'] == true)) {
        unset($_SESSION['NoExiste']);
        $mensajes[] =  "No tenemos esos datos en nuestra base de datos.";
    }

        if(isset($_SESSION['PsswrdActualizada']) && ($_SESSION['PsswrdActualizada'] == true)) {
        unset($_SESSION['PsswrdActualizada']);
        $mensajes[] =  "La contraseña se actualizó correctamente. ";
    }
    */
?>
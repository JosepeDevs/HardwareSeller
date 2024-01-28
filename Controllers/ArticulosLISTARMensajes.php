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
 * @$mensajes[]= string|bool Devuelve texto si detecta algún mensaje de confirmación/error, si no, devuelve false.
 */
Function getArrayMensajesArticulos(){
    if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $mensajes=[];

    if(isset($_SESSION['GoodUpdateArticulo']) && ($_SESSION['GoodUpdateArticulo'] == true)) {
        unset($_SESSION['GoodUpdateArticulo']);
        $mensajes[]= "Datos del Articulo actualizados correctamente.";
    }

    if(isset($_SESSION['BadCodigo']) && ($_SESSION['BadCodigo'] == true)) {
        unset($_SESSION['BadCodigo']);
        $mensajes[]= "El formato del código no es adecuado 3 letras y 5 números se esperaba.";
    }

    if(isset($_SESSION['ImageBadFormat']) && ($_SESSION['ImageBadFormat'] == true)) {
        unset($_SESSION['ImageBadFormat']);
        $mensajes[]= "Hubo un problema con el formato de la imagen o su ruta";
    }

    if(isset($_SESSION['CodigoAlreadyExists']) && ($_SESSION['CodigoAlreadyExists'] == true)) {
        unset($_SESSION['CodigoAlreadyExists']);
        $mensajes[]= "El código no se guardó porque ya existía previamente en nuestra base de datos. Pruebe otro por favor.";
    }

    if(isset($_SESSION['BadUpdateArticulo']) && ($_SESSION['BadUpdateArticulo'] == true)) {
        unset($_SESSION['BadUpdateArticulo']);
        $mensajes[]= "Hubo un fallo actualizando los datos del Articulo, por favor, pruebe de nuevo. La imagen subida no ha persistido en nuestros servidores.";
    }

    if(isset($_SESSION['LongNombre']) && ($_SESSION['LongNombre'] == true)) {
        unset($_SESSION['LongNombre']);
        $mensajes[]= "El nombre que introdujo es muy largo, por favor, abrévielo.";
    }

    if(isset($_SESSION['LongDescripcion']) && ($_SESSION['LongDescripcion'] == true)) {
        unset($_SESSION['LongDescripcion']);
        $mensajes[]= "La descripción que introdujo es muy larga, por favor, abréviela.";
    }

    if(isset($_SESSION['LongCategoria']) && ($_SESSION['LongCategoria'] == true)) {
        unset($_SESSION['LongCategoria']);
        $mensajes[]= "La Categooría que introdujo es muy larga, por favor, abréviela.";
    }

    if(isset($_SESSION['LongPrecio']) && ($_SESSION['LongPrecio'] == true)) {
        unset($_SESSION['LongPrecio']);
        $mensajes[]= "El precio que introdujo es muy grande, por favor, revíselo y si es correcto el precio contacte con el adminsitrador de la base de datos.";
    }

    if(isset($_SESSION['ExitoBorrandoArticulo']) && ($_SESSION['ExitoBorrandoArticulo'] == true)) {
        unset($_SESSION['ExitoBorrandoArticulo']);
        $mensajes[]= "El artículo se borró correctamente.";

    } elseif(isset($_SESSION['ExitoBorrandoArticulo']) && ($_SESSION['ExitoBorrandoArticulo'] == false)) {
        unset($_SESSION['ExitoBorrandoArticulo'] );
        $mensajes[]= "El borrado del artículo NO tuvo lugar.";
    }

    if(isset($_SESSION['BorradoArticuloCancelado']) && ($_SESSION['BorradoArticuloCancelado'] == true)) {
        unset($_SESSION['BorradoArticuloCancelado']);
        $mensajes[]= "Borrado cancelado.";
    }

    if(isset($_SESSION['GoodInsertArticulo']) && ($_SESSION['GoodInsertArticulo'] == true)) {
        unset($_SESSION['GoodInsertArticulo']);
        $mensajes[]= "Nuevo Articulo añadido correctamente.";
    }

    if(isset($_SESSION['BadInsertArticulo']) && ($_SESSION['BadInsertArticulo'] == true)) {
        unset($_SESSION['BadInsertArticulo']);
        $mensajes[]= "hubo un fallo al insertar los datos del artículo, quizás debido a la conexión con la base de datos.";
    }

    if(isset($_SESSION['OperationFailed']) && ($_SESSION['OperationFailed'] == true)) {
        unset($_SESSION['OperationFailed'] );
        $mensajes[]= " operación falló de una forma inesperada, quizás la conexión a la base de datos no fue correcta.";
    }

    if(isset($_SESSION['BadOperation']) && ($_SESSION['BadOperation'] == true)) {
        unset($_SESSION['BadOperation'] );
        $mensajes[]= " Operación de borrado de artículo falló de una forma inesperada, quizás la conexión a la base de datos no fue correcta.";
    }

    if(isset($_SESSION['MoveFailed']) && ($_SESSION['MoveFailed'] == true)) {
        unset($_SESSION['MoveFailed'] );
        $mensajes[]= "Ocurrió algún error y no pudimos guardar la imagen enviada en nuestros servidores. Por favor pruebe de nuevo y si el problema persiste contacte el dpto. IT.";
    }

    if(isset($_SESSION['MoveDone']) && ($_SESSION['MoveDone'] == false)) {
        $_SESSION['MoveDone'] = false;
        unset($_SESSION['MoveDone'] );
        $mensajes[]= "La imagen está almacenada correctamente en nuestros servidores.";
    }
    if(isset($_SESSION['BadImage']) && ($_SESSION['BadImage'] == false)) {
        $_SESSION['BadImage'] =false;
        unset($_SESSION['BadImage'] );
        $mensajes[]= "La imagen no era adecuada.";
    }
    if(isset($_SESSION['ErrorGetArticulos']) && ($_SESSION['ErrorGetArticulos'] == false)) {
        unset($_SESSION['ErrorGetArticulos'] );
        $mensajes[]= "No se pudieron recuperar articulos de la base de datos.";
    }

    if( count($mensajes) == 0){
        return false;//si no encontró ningún mensaje a mostrar devolverá false;
    } else{
        return $mensajes;
    }
}
?>
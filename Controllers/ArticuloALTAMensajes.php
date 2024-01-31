<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticulosLISTARMensajes dice: no está user en session";
    header("Location: index.php");
}
/**
 * Funcion que se llama para comprobar si ha habido algún error o para mostrar un mensaje de operación realizada correctamente. Obtiene el resultado consultando SESSION.
 * @return array|bool Guarda todos los mensajes que encuentre en un array que devulve, si no hay coincidencias devuelve false.
 */
Function getArrayMensajesAltaArticulos(){
    if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $mensajes=[];

    if(isset($_SESSION['GoodInsertArticulo']) && $_SESSION['GoodInsertArticulo'] == true){
        $mensajes[] =  "Articulo añadido correctamente.";
        unset($_SESSION['GoodInsertArticulo']);
    };

    if(isset($_SESSION['CodigoAlreadyExists']) && $_SESSION['CodigoAlreadyExists'] == true){
        $mensajes[] =  "Lo sentimos, el  código indicado ya está en uso, por favor, seleccione otro.";
        unset($_SESSION['CodigoAlreadyExists']);
    }

    if(isset($_SESSION['FileAlreadyExists']) && $_SESSION['FileAlreadyExists'] == true){
        $mensajes[] =  "Lo sentimos, ya existe un archivo en nuestros servidores con ese nombre, renombre su archivo y pruebe de nuevo.";
        unset($_SESSION['FileAlreadyExists']);
    }

    if(isset($_SESSION['BadCodigo']) && $_SESSION['BadCodigo'] == true){
        $mensajes[] =  "El código introducido no tenía un formato adecuado, por favor, pruebe con 3 letras y 5 números en ese orden.";
        unset($_SESSION['BadCodigo']);
    }

    if(isset($_SESSION['BadOperation']) && $_SESSION['BadOperation'] == true){
        $mensajes[] =  "Hubo algún problema con la conexión a la base de datos.";
        unset($_SESSION['BadOperation']);
    }

    if(isset($_SESSION['FileBadFormat']) && $_SESSION['FileBadFormat'] == true){
        $mensajes[] =  "El formato o extensión del archivo subido no es adecuado (jpg, jpeg, gif o png) .";
        unset($_SESSION['FileBadFormat']);
    }

    if(isset($_SESSION['FileBadFormat']) && $_SESSION['FileBadFormat'] == true){
        $mensajes[] =  "El formato o extensión del archivo subido no es adecuado (jpg, jpeg, gif o png) .";
        unset($_SESSION['FileBadFormat']);
    }
    if(isset($_SESSION['ImagenPesada']) && $_SESSION['ImagenPesada'] == true){
        $mensajes[] =  "El peso de la imagen que intetó subir excedió el permitido o hubo algún fallo relacionado con su peso (max 200kb) .";
        unset($_SESSION['ImagenPesada']);
    }
    if(isset($_SESSION['ImagenGrande']) && $_SESSION['ImagenGrande'] == true){
        $mensajes[] =  "El ancho o el alto de la imagen enviada excedió el permitido 200px ambos o hubo algún problema midiendo el ancho y alto de la imagen.";
        unset($_SESSION['ImagenGrande']);
    }

    if(isset($_SESSION['SinNumero']) && $_SESSION['SinNumero'] == true){
        $mensajes[] =  "El código que escribió le faltaba la parte numérica.";
        unset($_SESSION['SinNumero']);
    }

    if(isset($_SESSION['NumeroGrande']) && $_SESSION['NumeroGrande'] == true){
        $mensajes[] =  "La parte numérica que indicó en el código debe ser como máximo 99999 (5 digitos)";
        unset($_SESSION['NumeroGrande']);
    }

    if(isset($_SESSION['BadInsertArticulo']) && $_SESSION['BadInsertArticulo'] == true){
        $mensajes[] =  "Fallo al insertar sus datos, la operación no tuvo lugar. Puede ser un fallo de la base de datos. La imagen subida no ha persistido en nuestros servidores.";
        unset($_SESSION['BadInsertArticulo']);
    }

    if(isset($_SESSION['LongNombre']) && $_SESSION['LongNombre'] == true){
        $mensajes[] =  "Introdujo un nombre demasiado largo. Abrévielo por favor.";
        unset($_SESSION['LongNombre']);
    }

    if(isset($_SESSION['LongDescripcion']) && $_SESSION['LongDescripcion'] == true){
        $mensajes[] =  "Introdujo una descripción demasiado larga. Abréviela por favor.";
        unset($_SESSION['LongDescripcion']);
    }

    if(isset($_SESSION['LongCategoria']) && $_SESSION['LongCategoria'] == true){
        $mensajes[] =  "Introdujo una categoría demasiado larga. Abréviela por favor.";
        unset($_SESSION['LongCategoria']);
    }

    if(isset($_SESSION['LongPrecio']) && $_SESSION['LongPrecio'] == true){
        $mensajes[] =  "Introdujo un precio demasiado grande. Revíselo y si es correcto, nos interesa poner eso a la venta, así que pongase urgentemente en contacto con el  administrador de la base de datos para actualizar la opción tan pronto como sea posible..";
        unset($_SESSION['LongPrecio']);
    }

    if(isset($_SESSION['LongImagen']) && $_SESSION['LongImagen'] == true){
        $mensajes[] =  "El total de carácteres necesarios para la ruta de la imagen es demasido largo. por favor, abrevie el nombre de la imágen.";
        unset($_SESSION['LongImagen']);
    }

    if(isset($_SESSION['MoveFailed']) && $_SESSION['MoveFailed'] == true){
        $mensajes[] =  "Hubo un problema moviendo la imagen a nuestros servidores. Sentimos las molestias.";
        unset($_SESSION['MoveFailed']);
    }

    if(isset($_SESSION['MoveDone']) && $_SESSION['MoveDone'] == true){
        $mensajes[] =  "La imagen se almacenó correctamente en nuestros servidores.";
        unset($_SESSION['MoveDone']);
    }

    if( count($mensajes) == 0){
        return false;//si no encontró ningún mensaje a mostrar devolverá false;
    } else{
        return $mensajes;
    }
}
?>
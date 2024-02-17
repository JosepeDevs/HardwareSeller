<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedidosLISTARMensajes dice: no está user en session";
    header("Location: index.php");
}
/**
 * Funcion que se llama para comprobar si ha habido algún error o para mostrar un mensaje de operación realizada correctamente. Obtiene el resultado consultando SESSION.
 * @return array|bool Guarda todos los mensajes que encuentre en un array que devulve, si no hay coincidencias devuelve false.
 */
Function getArrayMensajesContenidoPedido(){
    if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $mensajes=[];
    if(isset($_SESSION['LongcodArticulo']) && $_SESSION['LongcodArticulo'] == true){
        $mensajes[] =  "Introdujo un codArticulo demasiado largo. Abrévielo por favor.";
        unset($_SESSION['LongcodArticulo']);
    }

    if(isset($_SESSION['LongDescripcion']) && $_SESSION['LongDescripcion'] == true){
        $mensajes[] =  "Introdujo una descripción demasiado larga. Abréviela por favor.";
        unset($_SESSION['LongDescripcion']);
    }

    if(isset($_SESSION['LongContenidoPedido']) && $_SESSION['LongContenidoPedido'] == true){
        $mensajes[] =  "Introdujo una categoría demasiado larga. Abréviela por favor.";
        unset($_SESSION['LongContenidoPedido']);
    }

    if(isset($_SESSION['LongPrecio']) && $_SESSION['LongPrecio'] == true){
        $mensajes[] =  "Introdujo un precio demasiado grande. Revíselo y si es correcto, nos interesa poner eso a la venta, así que pongase urgentemente en contacto con el  administrador de la base de datos para actualizar la opción tan pronto como sea posible..";
        unset($_SESSION['LongPrecio']);
    }

    if(isset($_SESSION['LongImagen']) && $_SESSION['LongImagen'] == true){
        $mensajes[] =  "El total de carácteres necesarios para la ruta de la imagen es demasido largo. por favor, abrevie el codArticulo de la imágen.";
        unset($_SESSION['LongImagen']);
    }

    if(isset($_SESSION['MoveFailed']) && $_SESSION['MoveFailed'] == true){
        $mensajes[] =  "Hubo un problema moviendo la imagen a nuestros servidores. Sentimos las molestias.";
        unset($_SESSION['MoveFailed']);
    }

    if(isset($_SESSION['MoveFailed']) && $_SESSION['MoveFailed'] == false){
        $mensajes[] =  "La imagen se almacenó correctamente en nuestros servidores.";
        unset($_SESSION['MoveFailed']);
    }

    if(isset($_SESSION['numPedidoAlreadyExists']) && $_SESSION['numPedidoAlreadyExists'] == true){
        $mensajes[] =  "Lo sentimos, el  código indicado ya está en uso, por favor, seleccione otro.";
        unset($_SESSION['numPedidoAlreadyExists']);
    }

    if(isset($_SESSION['SinNumero']) && $_SESSION['SinNumero'] == true){
        $mensajes[] =  "El código que escribió le faltaba la parte numérica.";
        unset($_SESSION['SinNumero']);
    }

    if(isset($_SESSION['NumeroGrande']) && $_SESSION['NumeroGrande'] == true){
        $mensajes[] =  "La parte numérica que indicó en el código debe ser como máximo 99999 (5 digitos)";
        unset($_SESSION['NumeroGrande']);
    }

    if(isset($_SESSION['FileAlreadyExists']) && $_SESSION['FileAlreadyExists'] == true){
        $mensajes[] =  "Lo sentimos, ya existe un archivo en nuestros servidores con ese codArticulo, recodArticulo su archivo y pruebe de nuevo.";
        unset($_SESSION['FileAlreadyExists']);
    }

    if(isset($_SESSION['BadnumPedido']) && $_SESSION['BadnumPedido'] == true){
        $mensajes[] =  "El código introducido no tenía un formato adecuado, por favor, pruebe con 3 letras y 5 números en ese orden.";
        unset($_SESSION['BadnumPedido']);
    }
    if(isset($_SESSION['numPedidoDeberiaExistir']) && $_SESSION['numPedidoDeberiaExistir'] == true){
        $mensajes[] =  "Estamos editando, el código original debería estar en la base de datos pero no se ha encontrado.";
        unset($_SESSION['numPedidoDeberiaExistir']);
    }

    if(isset($_SESSION['BadOperation']) && $_SESSION['BadOperation'] == true){
        $mensajes[] =  "Hubo algún problema con la conexión a la base de datos.";
        unset($_SESSION['BadOperation']);
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

    if(isset($_SESSION['BadUpdateContenidoPedido']) && ($_SESSION['BadUpdateContenidoPedido'] == true)) {
        unset($_SESSION['BadUpdateContenidoPedido']);
        $mensajes[] =  "El sistema tuvo un problema actualizando los datos del artículo, por favor, pruebe de nuevo.";
    }

    if(isset($_SESSION['ExitoBorrandoContenidoPedido']) && ($_SESSION['ExitoBorrandoContenidoPedido'] == true)) {
        unset($_SESSION['ExitoBorrandoContenidoPedido']);
        $mensajes[] =  "El artículo se borró correctamente.";

    } elseif(isset($_SESSION['ExitoBorrandoContenidoPedido']) && ($_SESSION['ExitoBorrandoContenidoPedido'] == false)) {
        unset($_SESSION['ExitoBorrandoContenidoPedido']);
        $mensajes[] =  "El artículo no ha sido borrado.";
    }

    if(isset($_SESSION['OperationFailed']) && ($_SESSION['OperationFailed'] == true)) {
        unset($_SESSION['OperationFailed']);
        $mensajes[] =  " operación falló de una forma inesperada, quizás la conexión a la base de datos no fue correcta";
    }

    if(isset($_SESSION['BorradoContenidoPedidoCancelado']) && ($_SESSION['BorradoContenidoPedidoCancelado'] == true)) {
        unset($_SESSION['BorradoContenidoPedidoCancelado']);
        $mensajes[] =  "Borrado cancelado.";
    }
    if(isset($_SESSION['BadDescuento']) && ($_SESSION['BadDescuento'] == false)) {
        unset($_SESSION['BadDescuento'] );
        $mensajes[]= "El descuento introdducido no se detectó como numérico-decimal y no se pudo procesar correctamente.";
    }
    if(isset($_SESSION['OperationFailed']) && $_SESSION['OperationFailed'] == true){
        $_SESSION['OperationFailed']=false;
        $mensajes[] = "Operación no completada, es posible que hubiera un pproblema con la conenxión a la base de datos.";
    }
    if(isset($_SESSION['numPedidoNotFound']) && $_SESSION['numPedidoNotFound'] == true){
        $_SESSION['numPedidoNotFound']=false;
        $mensajes[] = "No se encontró el código consultado.";
    }
    if(isset($_SESSION['codArticuloNotFound']) && $_SESSION['codArticuloNotFound'] == true){
        $_SESSION['codArticuloNotFound']=false;
        $mensajes[] = "No se encontró ningún artículo que contenga su consulta en el codArticulo.";
    }
    if(isset($_SESSION['GoodInsertContenidoPedido']) && $_SESSION['GoodInsertContenidoPedido'] == true){
        $mensajes[] =  "ContenidoPedido añadido correctamente.";
        unset($_SESSION['GoodInsertContenidoPedido']);
    };

    if(isset($_SESSION['numPedidoAlreadyExists']) && $_SESSION['numPedidoAlreadyExists'] == true){
        $mensajes[] =  "Lo sentimos, el  código indicado ya está en uso, por favor, seleccione otro.";
        unset($_SESSION['numPedidoAlreadyExists']);
    }

    if(isset($_SESSION['BadOperation']) && $_SESSION['BadOperation'] == true){
        $mensajes[] =  "Hubo algún problema con la conexión a la base de datos.";
        unset($_SESSION['BadOperation']);
    }

    if(isset($_SESSION['BadInsertContenidoPedido']) && $_SESSION['BadInsertContenidoPedido'] == true){
        $mensajes[] =  "Fallo al insertar sus datos, la operación no tuvo lugar. Puede ser un fallo de la base de datos. La imagen subida no ha persistido en nuestros servidores.";
        unset($_SESSION['BadInsertContenidoPedido']);
    }

    if(isset($_SESSION['LongcodArticulo']) && $_SESSION['LongcodArticulo'] == true){
        $mensajes[] =  "Introdujo un codArticulo demasiado largo. Abrévielo por favor.";
        unset($_SESSION['LongcodArticulo']);
    }

    if(isset($_SESSION['GoodUpdateContenidoPedido']) && ($_SESSION['GoodUpdateContenidoPedido'] == true)) {
        unset($_SESSION['GoodUpdateContenidoPedido']);
        $mensajes[]= "Datos del ContenidoPedido actualizados correctamente.";
    }

    if(isset($_SESSION['BadnumPedido']) && ($_SESSION['BadnumPedido'] == true)) {
        unset($_SESSION['BadnumPedido']);
        $mensajes[]= "El formato del código no es adecuado 3 letras y 5 números se esperaba.";
    }

    if(isset($_SESSION['ImageBadFormat']) && ($_SESSION['ImageBadFormat'] == true)) {
        unset($_SESSION['ImageBadFormat']);
        $mensajes[]= "Hubo un problema con el formato de la imagen o su ruta";
    }

    if(isset($_SESSION['numPedidoAlreadyExists']) && ($_SESSION['numPedidoAlreadyExists'] == true)) {
        unset($_SESSION['numPedidoAlreadyExists']);
        $mensajes[]= "El código no se guardó porque ya existía previamente en nuestra base de datos. Pruebe otro por favor.";
    }

    if(isset($_SESSION['BadUpdateContenidoPedido']) && ($_SESSION['BadUpdateContenidoPedido'] == true)) {
        unset($_SESSION['BadUpdateContenidoPedido']);
        $mensajes[]= "Hubo un fallo actualizando los datos del ContenidoPedido, por favor, pruebe de nuevo. La imagen subida no ha persistido en nuestros servidores.";
    }

    if(isset($_SESSION['LongcodArticulo']) && ($_SESSION['LongcodArticulo'] == true)) {
        unset($_SESSION['LongcodArticulo']);
        $mensajes[]= "El codArticulo que introdujo es muy largo, por favor, abrévielo.";
    }

    if(isset($_SESSION['LongDescripcion']) && ($_SESSION['LongDescripcion'] == true)) {
        unset($_SESSION['LongDescripcion']);
        $mensajes[]= "La descripción que introdujo es muy larga, por favor, abréviela.";
    }

    if(isset($_SESSION['LongContenidoPedido']) && ($_SESSION['LongContenidoPedido'] == true)) {
        unset($_SESSION['LongContenidoPedido']);
        $mensajes[]= "La Categooría que introdujo es muy larga, por favor, abréviela.";
    }

    if(isset($_SESSION['LongPrecio']) && ($_SESSION['LongPrecio'] == true)) {
        unset($_SESSION['LongPrecio']);
        $mensajes[]= "El precio que introdujo es muy grande, por favor, revíselo y si es correcto el precio contacte con el adminsitrador de la base de datos.";
    }

    if(isset($_SESSION['LongActivo']) && ($_SESSION['LongActivo'] == true)) {
        unset($_SESSION['LongActivo']);
        $mensajes[]= "indico un estado para activo demasiado largo (solo se permite 0 para inactivo o 1 para activo.";
    }

    if(isset($_SESSION['ExitoBorrandoContenidoPedido']) && ($_SESSION['ExitoBorrandoContenidoPedido'] == true)) {
        unset($_SESSION['ExitoBorrandoContenidoPedido']);
        $mensajes[]= "El artículo se borró correctamente.";

    } elseif(isset($_SESSION['ExitoBorrandoContenidoPedido']) && ($_SESSION['ExitoBorrandoContenidoPedido'] == false)) {
        unset($_SESSION['ExitoBorrandoContenidoPedido'] );
        $mensajes[]= "El borrado del artículo NO tuvo lugar.";
    }

    if(isset($_SESSION['BorradoContenidoPedidoCancelado']) && ($_SESSION['BorradoContenidoPedidoCancelado'] == true)) {
        unset($_SESSION['BorradoContenidoPedidoCancelado']);
        $mensajes[]= "Borrado cancelado.";
    }

    if(isset($_SESSION['GoodInsertContenidoPedido']) && ($_SESSION['GoodInsertContenidoPedido'] == true)) {
        unset($_SESSION['GoodInsertContenidoPedido']);
        $mensajes[]= "Nuevo ContenidoPedido añadido correctamente.";
    }

    if(isset($_SESSION['BadInsertContenidoPedido']) && ($_SESSION['BadInsertContenidoPedido'] == true)) {
        unset($_SESSION['BadInsertContenidoPedido']);
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
    if(isset($_SESSION['ErrorGetContenidoPedidos']) && ($_SESSION['ErrorGetContenidoPedidos'] == false)) {
        unset($_SESSION['ErrorGetContenidoPedidos'] );
        $mensajes[]= "No se pudieron recuperar ContenidoPedidos de la base de datos.";
    }
    if(isset($_SESSION['BadDescuento']) && ($_SESSION['BadDescuento'] == false)) {
        unset($_SESSION['BadDescuento'] );
        $mensajes[]= "El descuento introdducido no se detectó como numérico-decimal y no se pudo procesar correctamente.";
    }
    if(isset($_SESSION['codPadreNoExiste']) && ($_SESSION['codPadreNoExiste'] == false)) {
        unset($_SESSION['codPadreNoExiste'] );
        $mensajes[]= "El numPedido padre indicado no existe.";
    }
    if(isset($_SESSION['LongPadre']) && ($_SESSION['LongPadre'] == false)) {
        unset($_SESSION['LongPadre'] );
        $mensajes[]= "El numPedido padre indicado era demasiado largo.";
    }


    if( count($mensajes) == 0){
        return false;//si no encontró ningún mensaje a mostrar devolverá false;
    } else{
        return $mensajes;
    }
}
?>
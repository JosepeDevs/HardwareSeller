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

    if(isset($_SESSION['badFecha']) && $_SESSION['badFecha'] == true){
        $mensajes[] =  "El formato de la fecha no era adecuado.";
        unset($_SESSION['badFecha']);
    }
    if(isset($_SESSION['estadoNotFound']) && $_SESSION['estadoNotFound'] == true){
        $mensajes[] =  "No se ha encontrado nada con ese estado.";
        unset($_SESSION['estadoNotFound']);
    }
    if(isset($_SESSION['ExitoBorrandoPedido']) && $_SESSION['ExitoBorrandoPedido'] == true){
        $mensajes[] =  "El Pedido se canceló satisfactoriamente.";
        unset($_SESSION['ExitoBorrandoPedido']);
    }
    if(isset($_SESSION['ExitoBorrandoContenidoPedido']) && $_SESSION['ExitoBorrandoContenidoPedido'] == true){
        $mensajes[] =  "El contenido del pedido se vació correctamente.";
        unset($_SESSION['ExitoBorrandoContenidoPedido']);
    }
    if(isset($_SESSION['FalloBorrandoPedido']) && $_SESSION['FalloBorrandoPedido'] == true){
        $mensajes[] =  "No es posible cancelar el pedido, tal vez el estado no sea adecuado (enviados, pago realizado, recibidos, finalizads...) Contacte con nosotros si cree que es un error.";
        unset($_SESSION['FalloBorrandoPedido']);
    }
    if(isset($_SESSION['FalloBorrandoContenidoPedido']) && $_SESSION['FalloBorrandoContenidoPedido'] == true){
        $mensajes[] =  "No se pudo borrar/cancelar los contenidos del pedido seleccionado, tal vez el estado no sea el adecuado (ya enviado, recibido, etc). Contacte con nosotros si cree que es un error.";
        unset($_SESSION['FalloBorrandoContenidoPedido']);
    }
    if(isset($_SESSION['SinNumero']) && $_SESSION['SinNumero'] == true){
        $mensajes[] =  "El idPedido que escribió le faltaba la parte numérica.";
        unset($_SESSION['SinNumero']);
    }

    if(isset($_SESSION['idPedidoDeberiaExistir']) && $_SESSION['idPedidoDeberiaExistir'] == true){
        $mensajes[] =  "Estamos editando, el idPedido original debería estar en la base de datos pero no se ha encontrado.";
        unset($_SESSION['idPedidoDeberiaExistir']);
    }

    if(isset($_SESSION['BadOperation']) && $_SESSION['BadOperation'] == true){
        $mensajes[] =  "Hubo algún problema con la conexión a la base de datos.";
        unset($_SESSION['BadOperation']);
    }

    if(isset($_SESSION['BadUpdatePedido']) && ($_SESSION['BadUpdatePedido'] == true)) {
        unset($_SESSION['BadUpdatePedido']);
        $mensajes[] =  "El sistema tuvo un problema actualizando los datos del artículo, por favor, pruebe de nuevo.";
    }

    if(isset($_SESSION['ExitoBorrandoPedido']) && ($_SESSION['ExitoBorrandoPedido'] == true)) {
        unset($_SESSION['ExitoBorrandoPedido']);
        $mensajes[] =  "El artículo se borró correctamente.";

    } elseif(isset($_SESSION['ExitoBorrandoPedido']) && ($_SESSION['ExitoBorrandoPedido'] == false)) {
        unset($_SESSION['ExitoBorrandoPedido']);
        $mensajes[] =  "El artículo no ha sido borrado.";
    }

    if(isset($_SESSION['BorradoPedidoCancelado']) && ($_SESSION['BorradoPedidoCancelado'] == true)) {
        unset($_SESSION['BorradoPedidoCancelado']);
        $mensajes[] =  "Borrado cancelado.";
    }

    if(isset($_SESSION['idPedidoNotFound']) && $_SESSION['idPedidoNotFound'] == true){
        $_SESSION['idPedidoNotFound']=false;
        $mensajes[] = "No se encontró el idPedido consultado.";
    }

    if(isset($_SESSION['fechaNotFound']) && $_SESSION['fechaNotFound'] == true){
        $_SESSION['fechaNotFound']=false;
        $mensajes[] = "No se encontró ningún artículo que contenga su consulta en el fecha.";
    }
    if(isset($_SESSION['GoodInsertPedido']) && $_SESSION['GoodInsertPedido'] == true){
        $mensajes[] =  "Pedido añadido correctamente.";
        unset($_SESSION['GoodInsertPedido']);
    };


    if(isset($_SESSION['BadInsertPedido']) && $_SESSION['BadInsertPedido'] == true){
        $mensajes[] =  "Fallo al insertar sus datos, la operación no tuvo lugar. Puede ser un fallo de la base de datos. La imagen subida no ha persistido en nuestros servidores.";
        unset($_SESSION['BadInsertPedido']);
    }

    if(isset($_SESSION['GoodUpdatePedido']) && ($_SESSION['GoodUpdatePedido'] == true)) {
        unset($_SESSION['GoodUpdatePedido']);
        $mensajes[]= "Datos del Pedido actualizados correctamente.";
    }

    if(isset($_SESSION['BadidPedido']) && ($_SESSION['BadidPedido'] == true)) {
        unset($_SESSION['BadidPedido']);
        $mensajes[]= "El formato del idPedido no es adecuado 3 letras y 5 números se esperaba.";
    }

    if(isset($_SESSION['ImageBadFormat']) && ($_SESSION['ImageBadFormat'] == true)) {
        unset($_SESSION['ImageBadFormat']);
        $mensajes[]= "Hubo un problema con el formato de la imagen o su ruta";
    }

    if(isset($_SESSION['idPedidoAlreadyExists']) && ($_SESSION['idPedidoAlreadyExists'] == true)) {
        unset($_SESSION['idPedidoAlreadyExists']);
        $mensajes[]= "El idPedido no se guardó porque ya existía previamente en nuestra base de datos. Pruebe otro por favor.";
    }


    if(isset($_SESSION['Longfecha']) && ($_SESSION['Longfecha'] == true)) {
        unset($_SESSION['Longfecha']);
        $mensajes[]= "El fecha que introdujo es muy largo, por favor, abrévielo.";
    }

    if(isset($_SESSION['LongDescripcion']) && ($_SESSION['LongDescripcion'] == true)) {
        unset($_SESSION['LongDescripcion']);
        $mensajes[]= "La descripción que introdujo es muy larga, por favor, abréviela.";
    }

    if(isset($_SESSION['LongPedido']) && ($_SESSION['LongPedido'] == true)) {
        unset($_SESSION['LongPedido']);
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

    if(isset($_SESSION['ExitoBorrandoPedido']) && ($_SESSION['ExitoBorrandoPedido'] == true)) {
        unset($_SESSION['ExitoBorrandoPedido']);
        $mensajes[]= "El artículo se borró correctamente.";

    } elseif(isset($_SESSION['ExitoBorrandoPedido']) && ($_SESSION['ExitoBorrandoPedido'] == false)) {
        unset($_SESSION['ExitoBorrandoPedido'] );
        $mensajes[]= "El borrado del artículo NO tuvo lugar.";
    }

    if(isset($_SESSION['BorradoPedidoCancelado']) && ($_SESSION['BorradoPedidoCancelado'] == true)) {
        unset($_SESSION['BorradoPedidoCancelado']);
        $mensajes[]= "Borrado cancelado.";
    }

    if(isset($_SESSION['GoodInsertPedido']) && ($_SESSION['GoodInsertPedido'] == true)) {
        unset($_SESSION['GoodInsertPedido']);
        $mensajes[]= "Nuevo Pedido añadido correctamente.";
    }

    if(isset($_SESSION['BadInsertPedido']) && ($_SESSION['BadInsertPedido'] == true)) {
        unset($_SESSION['BadInsertPedido']);
        $mensajes[]= "hubo un fallo al insertar los datos del artículo, quizás debido a la conexión con la base de datos.";
    }

    if(isset($_SESSION['OperationFailed']) && ($_SESSION['OperationFailed'] == true)) {
        unset($_SESSION['OperationFailed'] );
        $mensajes[]= " operación falló de una forma inesperada, quizás la conexión a la base de datos no fue correcta.";
    }

    if(isset($_SESSION['ErrorGetPedidos']) && ($_SESSION['ErrorGetPedidos'] == false)) {
        unset($_SESSION['ErrorGetPedidos'] );
        $mensajes[]= "No se pudieron recuperar Pedidos de la base de datos.";
    }
    if(isset($_SESSION['codUsuarioNotFound']) && ($_SESSION['codUsuarioNotFound'] == false)) {
        unset($_SESSION['codUsuarioNotFound'] );
        $mensajes[]= "No se encontró el ususario indicado .";
    }


    if( count($mensajes) == 0){
        return false;//si no encontró ningún mensaje a mostrar devolverá false;
    } else{
        return $mensajes;
    }
}
?>
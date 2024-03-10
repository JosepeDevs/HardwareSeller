<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
/// las funciones de contenido pedidos y pedidos ya bloquean para que los usuarios solo puedan ver lo suyo propio si no tienen un rol de admin o empleado
//NO PERMITIMOS POR AHORA BORRAR PARTE DE UN PEDIDO, SE BORRA ENTERO O NO SE BORRA
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "ContenidoPedidoBORRARMensajes dice: no está user en session";
    header("Location: /index.php");
    exit;

}
$rol = GetRolDeSession();
if( $rol == "admin" || $rol == "empleado" ){
} else{
    session_destroy();
    print "Articulos alta dice: no está user en session";
    header("Location: /index.php");
    exit;

}


include_once("header.php");

$numPedido = isset($_GET['numPedido']) ? $_GET['numPedido'] : null;


if(isset($_GET['confirmacion']) && $_GET['confirmacion'] ==  'false' ){
    $_SESSION['BorradoContenidoPedidoCancelado'] = true;
    header("Location: ContenidoPedidoLISTAR.php");
    exit;
}else if(isset($_GET['numPedido']) && isset($_GET['confirmacion']) && $_GET['confirmacion']== 'true') {
    include_once("../Controllers/ContenidoPedidoBORRARController.php");
    $operacionConfirmada = borradoLogicoContenidoPedido($numPedido);
    header("Location: ContenidoPedidoLISTAR.php");
    exit;
}
?>
        <h1>¿Está seguro de que desea desactivar el contenido de este pedido?</h1>
        <div class="finForm">
            <h2><a href="ContenidoPedidoBORRAR.php?numPedido=<? print $numPedido;?>&confirmacion=true">Sí, desactivar la linea del pedido seleccionada (Borrado lógico).</a></h2>
            <h2><a href="ContenidoPedidoBORRAR.php?numPedido=<? print $numPedido;?>&confirmacion=false">Cancelar desactivación.</a></h2>
        </div>
<?php
include_once("footer.php");
?>

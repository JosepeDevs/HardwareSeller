<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedidosBORRARMensajes dice: no está user en session";
    header("Location: /index.php");
}

include_once("header.php");

$numPedido = isset($_GET['numPedido']) ? $_GET['numPedido'] : null;


if(isset($_GET['confirmacion']) && $_GET['confirmacion'] ==  'false' ){
    $_SESSION['BorradoContenidoPedidoCancelado'] = true;
    header("Location: ContenidoPedidosLISTAR.php");
    exit;
}else if(isset($_GET['numPedido']) && isset($_GET['confirmacion']) && $_GET['confirmacion']== 'true') {
    include_once("../Controllers/ContenidoPedidoBORRARController.php");
    $operacionConfirmada = borradoLogico($numPedido);
    header("Location: ContenidoPedidosLISTAR.php");
    exit;
}
?>
        <h1>¿Está seguro de que desea desactivar el contenido de este pedido?</h1>
        <div class="finForm">
            <h2><a href="ContenidoPedidoBORRAR.php?numPedido=<?php echo $numPedido;?>&confirmacion=true">Sí, desactivar todo el contenido de este pedido (Borrado lógico).</a></h2>
            <h2><a href="ContenidoPedidoBORRAR.php?numPedido=<?php echo $numPedido;?>&confirmacion=false">Cancelar desactivación.</a></h2>
        </div>
<?php
include_once("footer.php");
?>

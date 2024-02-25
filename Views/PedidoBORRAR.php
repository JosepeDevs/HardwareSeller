<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "PedidoBORRARMensajes dice: no está user en session";
    header("Location: /index.php");
}

include_once("header.php");

$idPedido = isset($_GET['idPedido']) ? $_GET['idPedido'] : null;


if(isset($_GET['confirmacion']) && $_GET['confirmacion'] ==  "no" ){
    $_SESSION['BorradoPedidoCancelado'] = true;
    header("Location: PedidoLISTAR.php");
    exit;
}else if(isset($_GET['idPedido']) && isset($_GET['confirmacion']) && $_GET['confirmacion']== "yes") {
    include_once("../Controllers/PedidoBORRARController.php");
    $operacionConfirmada = borradoLogicoPedido($idPedido);
    header("Location: PedidoLISTAR.php");
    exit;
}
?>
        <h1>¿Está seguro de que desea cancelar este pedido y sus contenidos?</h1>
        <div class="finForm">
            <h2><a href="PedidoBORRAR.php?idPedido=<? echo $idPedido;?>confirmacion=yes">Sí, cancelar pedido y sus contenidos.</a></h2>
            <h2><a href='PedidoBORRAR.php?idPedido=<? echo $idPedido;?>confirmacion=no'>Cancelar desactivación.</a></h2>

        </div>
<?php
include_once("footer.php");
?>

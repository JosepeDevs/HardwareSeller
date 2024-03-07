<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//Aquí si que pueden acceder usuarios, las funciones de contenido pedido y pedido ya filtran para que solo tengan sus propios pedidos
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "PedidoBORRARMensajes dice: no está user en session";
    header("Location: /index.php");
    exit;

}

include_once("header.php");
include_once("../Controllers/PedidoBORRARController.php");

$idPedido = isset($_GET['idPedido']) ? $_GET['idPedido'] : null;
$estado = isset($_GET['estado']) ? $_GET['estado'] : null;

$estadoCancelable = SePuedeCancelarPedido($estado);
if($estadoCancelable ==false){
    $_SESSION['BadEstadoParaCancelar'] = true;
}

if(isset($_GET['confirmacion']) && $_GET['confirmacion'] ==  "false" ){
    $_SESSION['BorradoPedidoCancelado'] = true;
    header("Location: PedidosLISTAR.php");
    exit;
}else if(isset($_GET['idPedido']) && isset($_GET['confirmacion']) && $_GET['confirmacion']== "true" && $estadoCancelable) {
    echo($_GET['confirmacion']);
    $operacionConfirmada = borradoLogicoPedido($idPedido);
    header("Location: PedidosLISTAR.php");
    exit;
}
?>
        <h1>¿Está seguro de que desea cancelar este pedido y sus contenidos?</h1>
        <div class="finForm">
        <?php


echo'<h2><a href="../Views/PedidoBORRAR.php?idPedido='.$idPedido.'&confirmacion=true">Sí, cancelar pedido y sus contenidos.</a></h2>';
echo'<h2><a href="../Views/PedidoBORRAR.php?idPedido='.$idPedido.'&confirmacion=false">Cancelar desactivación.</a></h2>';
?>

        </div>
<?php
//SECCION DE IMPRIMIR MENSAJE DE ERROR/CONFIRMACIÓN
include_once("../Controllers/PedidosMensajes.php");
            $arrayMensajes=getArrayMensajesPedidos();
            if(is_array($arrayMensajes)){
                foreach($arrayMensajes as $mensaje) {
                    echo "<h3>$mensaje</h3>";
                }
            };

include_once("footer.php");
?>

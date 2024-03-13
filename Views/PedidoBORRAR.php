<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//Aquí si que pueden acceder usuarios, las funciones de contenido pedido y pedido ya filtran para que solo tengan sus propios pedidos
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "PedidoBORRARMensajes dice: no está user en session";
    header("Location: /index.php");
    exit;

}
 
include_once("header.php");
include_once("../Controllers/PedidoBORRARController.php");

$idPedido = isset($_GET['idPedido']) ? $_GET['idPedido'] : null;
$estado = isset($_GET['estado']) ? $_GET['estado'] : null;

$estadoCancelable = SePuedeCancelarPedido($estado);

if(isset($_GET['confirmacion']) && $_GET['confirmacion'] ==  "false" ){
    $_SESSION['BorradoPedidoCancelado'] = true;
    header("Location: PedidosLISTAR.php");
    exit;
}else if(isset($_GET['idPedido']) && isset($_GET['confirmacion']) && $_GET['confirmacion']== "true" && $estadoCancelable) {
    if(isset($_SESSION['BadEstadoParaCancelar']) && $_SESSION['BadEstadoParaCancelar'] !== true){
        $operacionExitosa = borradoLogicoPedido($idPedido);
        $_SESSION['PedidoCanceladoExitoso'] = true;
    } else if(!isset($_SESSION['BadEstadoParaCancelar']) && $estadoCancelable){
        //si todo va bien no se subirá a session badestado, no sirve como comprobación única, por eso si no existe es qeu todo OK pero también debe ser cancelable
        //esto es para pedidos que sí son cancelables que no existe BadEstado
        $operacionExitosa = borradoLogicoPedido($idPedido);
        $_SESSION['PedidoCanceladoExitoso'] = true;
    } else{
        $_SESSION['BorradoPedidoFallido'] = true;
        header("Location: PedidosLISTAR.php");
        exit;
    }
    header("Location: PedidosLISTAR.php");
    exit;
}
?>
        <h1>¿Está seguro de que desea cancelar este pedido y sus contenidos?</h1>
        <div class="finForm">
        <?php


print'<h2><a href="../Views/PedidoBORRAR.php?idPedido='.$idPedido.'&confirmacion=true">Sí, cancelar pedido y sus contenidos.</a></h2>';
print'<h2><a href="../Views/PedidoBORRAR.php?idPedido='.$idPedido.'&confirmacion=false">No deseo cancelar el pedido.</a></h2>';
?>

        </div>
<?php
//SECCION DE IMPRIMIR MENSAJE DE ERROR/CONFIRMACIÓN
//////print_r($_SESSION);
include_once("../Controllers/PedidosMensajes.php");
            $arrayMensajes=getArrayMensajesPedidos();
            if(is_array($arrayMensajes)){
                foreach($arrayMensajes as $mensaje) {
                    print "<h3>$mensaje</h3>";
                }
            };
//tras hacer print del error se hará unset, pero si insisten en borrar aquí subimos de nuevo el error para que se vea en listar pedidos
if($estadoCancelable == false ){
    $_SESSION['BadEstadoParaCancelar'] = true;
}
include_once("footer.php");
?>

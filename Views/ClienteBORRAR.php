<?php
include_once("header.php");
?>
<h1>¿Está seguro de que desea eliminar este artículo?</h1>
        <div class="finForm">
            <h2><a href="ClienteBORRAR.php?codigo=<?php echo $dni;?>&confirmacion=true">Sí, borrar el cliente (Borrado lógico).</a></h2>
            <h2><a href="ClienteBORRAR.php?codigo=<?php echo $dni;?>&confirmacion=false">Cancelar borrado.</a></h2>
        </div>
        <?php
include_once("footer.php");
?>

<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ClienteBORRRAR dice: no está user en session";
    header("Location: index.php");
}

$dni = isset($_GET['dni']) ? $_GET['dni'] : null;

include_once("/../Controllers/CheckRol.php");
if(isset($_GET['confirmacion']) && $_GET['confirmacion'] ==  'false' ){
    $dni=$_GET["dni"];
    $_SESSION['BorradoClienteCancelado'] = true;
} else if(isset($_GET['dni']) && isset($_GET['confirmacion']) && $_GET['confirmacion']== 'true') {
    include_once("/../Controllers/BorrarClienteController.php");
    $operacionConfirmada = borradoLogico($dni);
    $_SESSION['BorradoArticuloCancelado'] = true;
}

include_once("/../Controllers/UserSession.php");
$tieneAdminYEstaLogeado = AuthYRolAdmin();
if($tieneAdminYEstaLogeado == true &&   ( $_SESSION['BorradoArticuloCancelado'] == true || $_SESSION['BorradoArticuloCancelado'] == false) ){
    header("Location: TablaClientes.php");
    exit;
} else {
    header("Location: editarcliente.php?dni=$dni");
    exit;
}
?>
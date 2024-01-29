<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ClienteBORRRAR dice: no está user en session";
    header("Location: ../index.php");
}

include_once("header.php");

$dni = isset($_GET['dni']) ? $_GET['dni'] : null;
if($dni !== null){
    $_SESSION['dni'] = $dni;
}

if(isset($_GET['confirmacion']) && $_GET['confirmacion'] ==  false ){
    $dni=$_GET["dni"];
    $_SESSION['BorradoClienteCancelado'] = true;
} else if(isset($_GET['dni']) && isset($_GET['confirmacion']) && $_GET['confirmacion']== true) {
    include_once("../Controllers/ClienteBORRARController.php");
    $operacionConfirmada = borradoLogicoCliente($dni);
    $_SESSION['BorradoClienteCancelado'] = true;
} else{
    //no hay confirmación, es la primera vez que entran en la página
    $_SESSION['BorradoClienteCancelado'] = "otro";//aun no han borrado ni confirmado
}

?>
<h1>¿Está seguro de que desea eliminar este artículo?</h1>
        <div class="finForm">
            <h2><a href="ClienteBORRAR.php?dni=<?php echo $dni;?>&confirmacion=1">Sí, borrar el cliente (Borrado lógico).</a></h2>
            <h2><a href="ClienteBORRAR.php?dni=<?php echo $dni;?>&confirmacion=0">Cancelar borrado.</a></h2>
        </div>
<?php
print_r($_SESSION);
include_once("footer.php");

if(( $_SESSION['BorradoClienteCancelado'] !== "otro")){
    $tieneAdminYEstaLogeado = AuthYRolAdmin();
    if($tieneAdminYEstaLogeado == true ){
        header("Location: TablaClientes.php");
        print"hola";
        exit;
    } else {
        print"adios";
        header("Location: ClienteEDITAR.php?dni=$dni");
        exit;
    }
}
?>

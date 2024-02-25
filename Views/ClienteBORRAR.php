<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ClienteBORRRAR dice: no está user en session";
    header("Location: ../index.php");
}


//NO PROTEGEMOS COMO ADMIN O 

include_once("header.php");

$dni = isset($_GET['dni']) ? $_GET['dni'] : null;
$dniUsuario=GetDniByEmail($_SESSION['user']);
$estaMirandoSuPropioDni=($dni===$dniUsuario);
if(!$estaMirandoSuPropioDni){
    session_destroy();
    echo "ClienteBorrar dice: estaba intentando mirara algo que no debería";
    header("Location: /index.php");
}
if($dni !== null){
    $_SESSION['dni'] = $dni;
}

$_SESSION['operacionCancelada'] = null;//aun no han borrado ni confirmado

if(isset($_GET['confirmacion']) && isset($_GET['confirmacion']) && $_GET['confirmacion'] ==  "false" ){
    $dni=$_GET["dni"];
    $_SESSION['operacionCancelada'] = "true";
} else if(isset($_GET['dni']) && isset($_GET['confirmacion']) && $_GET['confirmacion']== "true") {
    include_once("../Controllers/ClienteBORRARController.php");
    $operacionConfirmada = borradoLogicoCliente($dni);
    $_SESSION['operacionCancelada'] = "false";
} else{
    //no hay confirmación, es la primera vez que entran en la página.no hay que hacer nada
}

?>
<h1>¿Está seguro de que desea desactivar/borrar este cliente?</h1>
        <div class="finForm">
            <h2><a href="ClienteBORRAR.php?dni=<?php echo $dni;?>&confirmacion=true">Sí, borrar el cliente (Borrado lógico).</a></h2>
            <h2><a href="ClienteBORRAR.php?dni=<?php echo $dni;?>&confirmacion=false">Cancelar borrado.</a></h2>
        </div>
<?php
include_once("footer.php");

if(( $_SESSION['operacionCancelada'] !== null)){
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
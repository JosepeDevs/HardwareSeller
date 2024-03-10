<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "ClienteBORRRAR dice: no está user en session";
    header("Location: ../index.php");
    exit;
}



include_once("header.php");
//SOLO ADMIN PUEDEN BORRAR DNI AJENOS, CUALQUIER OTRO LO MANDA A INDEX
$dni = isset($_GET['dni']) ? $_GET['dni'] : null;
$dniUsuario=GetDniByEmail($_SESSION['user']);
$rol = GetRolDeSession();
$estaMirandoSuPropioDni=($dni===$dniUsuario);//comprobamos si son iguales y guardamos el resultado bool en la variable
if(!$estaMirandoSuPropioDni && $rol !== "admin"){
    session_destroy();
    $_SESSION['NoBorrarDniAjeno'] = true;
   // print "ClienteBorrar dice: estaba intentando mirara algo que no debería";
    header("Location: /index.php");
    exit;

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

//si ya han respondido esto les redigirá a un sitio u otro
if(( $_SESSION['operacionCancelada'] !== null)){
    $tieneRolAdminYEstaLogeado = AuthYRolAdmin();
    if($tieneRolAdminYEstaLogeado == true ){
        header("Location: TablaClientes.php");
        exit;
    } else {
        header("Location: ../Controllers/DestructorSession.php");
        exit;
    }
}
?>
<h1>¿Está seguro de que desea desactivar/borrar este cliente?</h1>
        <div class="finForm">
            <h2><a href="ClienteBORRAR.php?dni=<?php print $dni;?>&confirmacion=true">Sí, borrar el cliente (Borrado lógico).</a></h2>
            <h2><a href="ClienteBORRAR.php?dni=<?php print $dni;?>&confirmacion=false">Cancelar borrado.</a></h2>
        </div>
<?php
include_once("footer.php");
?>
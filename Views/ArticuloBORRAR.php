<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "ArticulosBORRARMensajes dice: no está user en session";
    header("Location: /index.php");
    exit;

}
$rol = GetRolDeSession();
if( $rol == "admin" || $rol == "empleado" || $rol == "editor"){
} else{
    session_destroy();
    print "Articulos alta dice: no está user en session";
    header("Location: /index.php");
    exit;

}

include_once("header.php");

$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : null;


if(isset($_GET['confirmacion']) && $_GET['confirmacion'] ==  'false' ){
    $_SESSION['BorradoArticuloCancelado'] = true;
    header("Location: ArticulosLISTAR.php");
    exit;
}else if(isset($_GET['codigo']) && isset($_GET['confirmacion']) && $_GET['confirmacion']== 'true') {
    include_once("../Controllers/ArticuloBORRARController.php");
    $operacionConfirmada = borradoLogico($codigo);
    header("Location: ArticulosLISTAR.php");
    exit;
}
?>
        <h1>¿Está seguro de que desea desactivar este artículo?</h1>
        <div class="finForm">
            <h2><a href="ArticuloBORRAR.php?codigo=<?php print $codigo;?>&confirmacion=true">Sí, borrar el artículo (Borrado lógico).</a></h2>
            <h2><a href="ArticuloBORRAR.php?codigo=<?php print $codigo;?>&confirmacion=false">Cancelar borrado.</a></h2>
        </div>
<?php
include_once("footer.php");
?>

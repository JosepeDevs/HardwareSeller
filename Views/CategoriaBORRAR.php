<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "CategoriasBORRARMensajes dice: no está user en session";
    header("Location: /index.php");
}
$rol = GetRolDeSession();
if( $rol !== "admin" || $rol !== "empleado" ){
    session_destroy();
    echo "Articulos alta dice: no está user en session";
    header("Location: /index.php");
}

include_once("header.php");

$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : null;


if(isset($_GET['confirmacion']) && $_GET['confirmacion'] ==  'false' ){
    $_SESSION['BorradoCategoriaCancelado'] = true;
    header("Location: CategoriasLISTAR.php");
    exit;
}else if(isset($_GET['codigo']) && isset($_GET['confirmacion']) && $_GET['confirmacion']== 'true') {
    include_once("../Controllers/CategoriaBORRARController.php");
    $operacionConfirmada = borradoLogico($codigo);
    header("Location: CategoriasLISTAR.php");
    exit;
}
?>
        <h1>¿Está seguro de que desea desactivar este Categoria?</h1>
        <div class="finForm">
            <h2><a href="CategoriaBORRAR.php?codigo=<?php echo $codigo;?>&confirmacion=true">Sí, borrar el Categoria (Borrado lógico).</a></h2>
            <h2><a href="CategoriaBORRAR.php?codigo=<?php echo $codigo;?>&confirmacion=false">Cancelar borrado.</a></h2>
        </div>
<?php
include_once("footer.php");
?>

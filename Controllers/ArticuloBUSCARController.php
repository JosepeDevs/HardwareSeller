<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticuloBUSCAR dice: no está user en session";
    header("Location: /../Views/index.php");
}

function getArticuloByCodigo($codigo){
    include_once("/../config/conectarBD.php");
    include_once("/../Models/Articulo.php");
    $articulo = new Articulo();
    $articulo->GetArticuloByCodigo($codigo);
    if($articulo == false){
        $_SESSION['CodigoNotFound'] = true;
        return false;
    } else{
        return $articulo;
    }
}
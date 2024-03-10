<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "ArticuloEDITAR dice: no está user en session";
    header("Location: index.php");
    exit;
}

function getArticuloByCodigo($codigo){
    include_once("../Models/Articulo.php");
    $articulo = Articulo::getArticuloByCodigo($codigo);
    return $articulo;
}

function getArrayAtributosArticulo(){
    include_once("../Models/Articulo.php");
    $arrayArticulos = Articulo::getArrayAtributosArticulo();
    return $arrayArticulos;
}
?>

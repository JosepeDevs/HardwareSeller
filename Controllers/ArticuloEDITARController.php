<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticuloEDITAR dice: no está user en session";
    header("Location: index.php");
}

function getArticuloByCodigo($codigo){
    include_once("../Models/Articulo.php");
    $articulo = getArticuloByCodigo($codigo);
    return $articulo;
}

function getArrayAtributos(){
    include_once("../Models/Articulo.php");
    $arrayArticulos = getArrayAtributos();
    return $arrayArticulos;
}
?>

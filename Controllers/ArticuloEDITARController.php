<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticuloEDITAR dice: no está user en session";
    header("Location: index.php");
}

function getAllArticulos(){
    include_once("/../Models/Articulo.php");
    $arrayArticulos = getAllArticulos();
    return $arrayArticulos;
}

function getArrayAtributos(){
    include_once("/../Models/Articulo.php");
    $arrayArticulos = getArrayAtributos();
    return $arrayArticulos;
}

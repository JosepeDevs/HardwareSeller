<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");

checkAdminOEmpleado();

$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "CategoriasLISTARMensajes dice: no está user en session";
    header("Location: index.php");
    exit;
}

function borradoLogico($codigo){
    include_once("../Models/Categoria.php");
    $Categoria = new Categoria();
    $operacionConfirmada = $Categoria->borradoLogico($codigo);
    return $operacionConfirmada;
}
?>

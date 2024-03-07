<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//NO PROTEGER, USADO EN ASIDE (NO REQUIERE LOGIN)

include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "CategoriaEDITAR dice: no está user en session";
    header("Location: index.php");
    exit;
}

function getCategoriaByCodigo($codigo){
    include_once("../Models/Categoria.php");
    $Categoria = Categoria::getCategoriaByCodigo($codigo);
    return $Categoria;
}

function getArrayAtributosCategoria(){
    include_once("../Models/Categoria.php");
    $arrayCategorias = Categoria::getArrayAtributosCategoria();
    return $arrayCategorias;
}
?>

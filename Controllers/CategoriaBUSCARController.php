<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "CategoriaBUSCAR dice: no está user en session";
    header("Location: /index.php");
}


function getCategoriaByCodigo($codigo){
    include_once("../Models/Categoria.php");
    $Categoria = Categoria::GetCategoriaByCodigo($codigo);
    if($Categoria == false){
        $_SESSION['CodigoNotFound'] = true;
        return false;
    } else{
        return $Categoria;
    }
}

function getSubCategorias($codigoCategoria){
    include_once("../Models/Categoria.php");
    $arraySubCategorias = Categoria::getSubCategorias($codigoCategoria);
    if($arraySubCategorias == false){
        $_SESSION['SubCategoriasNotFound'] = true;
        return false;
    } else{
        return $arraySubCategorias;
    }
}

function getArrayAtributosCategoria(){
    include_once("../Models/Categoria.php");
    $arrayCategorias = Categoria::getArrayAtributosCategoria();
    return $arrayCategorias;
}

function GetCategoriasByBusquedaNombre($nombre){
    include_once("../Models/Categoria.php");
    $arrayCategorias = Categoria::GetCategoriasByBusquedaNombre($nombre);
    return $arrayCategorias;
}
?>
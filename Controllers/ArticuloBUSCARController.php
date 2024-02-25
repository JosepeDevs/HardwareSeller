<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//NO PROTEGER SE USA EN CARRITo
function TransformarCodigo($codigo){
    include_once("../Models/Articulo.php");
    $codigo = Articulo::TransformarCodigo($codigo);
    return $codigo;
}

function getArticuloByCodigo($codigo){
    include_once("../Models/Articulo.php");
    $articulo = Articulo::GetArticuloByCodigo($codigo);
    if($articulo == false){
        $_SESSION['CodigoNotFound'] = true;
        return false;
    } else{
        return $articulo;
    }
}

function getArrayAtributosArticulo(){
    include_once("../Models/Articulo.php");
    $arrayArticulos = Articulo::getArrayAtributosArticulo();
    return $arrayArticulos;
}

function GetArticulosByBusquedaNombre($nombre){
    include_once("../Models/Articulo.php");
    $arrayArticulos = Articulo::GetArticulosByBusquedaNombre($nombre);
    return $arrayArticulos;
}

function GerArticulosRelacionadosByCodigo($codigo){
    include_once("../Models/Articulo.php");
    $arrayArticulos = Articulo::GerArticulosRelacionadosByCodigo($codigo);
    return $arrayArticulos;
}
?>
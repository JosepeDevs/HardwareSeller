<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//si lo protejo en el carrito de la compra no pueden buscar los datos del artículo

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
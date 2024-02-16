<?php

//ESTE RECOGE LOS DATOS Y LOS MANDA A MODELOS
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedidoVALIDAR dice: no está user en session";
   header("Location: /index.php");
}

include_once("../Models/ContenidoPedido.php");
print_r($_SESSION);

$numPedido = isset($_POST["numPedido"]) ? $_POST["numPedido"] : null;
$numPedidoOriginal = isset($_SESSION["numPedido"]) ? $_SESSION["numPedido"] : null;
$codArticulo = isset($_POST["codArticulo"]) ? $_POST["codArticulo"] : null;
$numeroLinea = isset($_POST["numLinea"]) ? $_POST["numLinea"] : null;
$cantidad = isset($_POST["cantidad"]) ? $_POST["cantidad"] : null;
$precio = isset($_POST["precio"]) ? $_POST["precio"] : null;
$descuento = isset($_POST["descuento"]) ? $_POST["descuento"] : null;
$activo = isset($_POST["activo"]) ? $_POST["activo"] : null;

if($numPedido == $numPedidoOriginal ){//si el código escrito es el mismo --> es que estaban editando y no lo quieren cambiar
    $mantenemosnumPedido= true;
} else{
    $mantenemosnumPedido= false;
}

$codArticuloValido = ContenidoPedido::ComprobarLongitud($codArticulo,50);
if($codArticuloValido == false) {    $_SESSION['LongcodArticulo']= true; }

$codPadreValido = ContenidoPedido::ComprobarLongitud($codContenidoPedidoPadre,50);
if($codPadreValido == false) {    $_SESSION['LongPadre']= true; }

$codPadreExiste = ContenidoPedido::numPedidoPadreExiste($codContenidoPedidoPadre);
if($codPadreExiste == false) {    $_SESSION['codPadreNoExiste']= true; }

$activoValido = ContenidoPedido::ComprobarLongitud($activo,11);
if($activoValido == false) { $_SESSION['LongActivo']= true;}

if( isset($_SESSION["editandoContenidoPedido"]) && $_SESSION["editandoContenidoPedido"] == "true" ){
    if($_SESSION['numPedido'] !== null){
        //no han escrito código, quieren que se mantega el que ya tenía
        $numPedidoOriginal = $_SESSION["numPedido"];
        $numPedidoOriginalLibre = ContenidoPedido::numPedidoLibre($numPedidoOriginal);
        if($numPedidoOriginalLibre == true) {  $_SESSION['numPedidoDeberiaExistir'] = true;}
    }

    if( !$mantenemosnumPedido ){
        //entonces hay numPedido nuevo, validamos formato y que esté libre (el nuevo)
        $numPedido = $_POST["numPedido"];
        $numPedidoLibre = ContenidoPedido::numPedidoLibre($numPedido);
        if($numPedidoLibre == false) {  $_SESSION['numPedidoAlreadyExists']= true;}
    }

}else if( isset($_SESSION["nuevoContenidoPedido"]) && $_SESSION["nuevoContenidoPedido"] == "true" ){

    if( isset($_POST['numPedido']) ) {//numPedido nuevo  ContenidoPedido llega por POST, aqui numPedido es obligatorio.
    $numPedido = $_POST["numPedido"];
    $numPedidoLibre = ContenidoPedido::numPedidoLibre($numPedido);
    if($numPedidoLibre == false) {  $_SESSION['numPedidoAlreadyExists']= true;}
    }

};

if(
    ( isset($_SESSION['LongcodArticulo']) && $_SESSION['LongcodArticulo'] == true) ||
    ( isset($_SESSION['LongPadre']) && $_SESSION['LongPadre'] == true) ||
    ( isset($_SESSION['BadnumPedido']) && $_SESSION['BadnumPedido'] == true ) ||
    ( isset($_SESSION['numPedidoAlreadyExists']) && $_SESSION['numPedidoAlreadyExists'] == true )||
    ( isset($_SESSION['numPedidoDeberiaExistir']) && $_SESSION['numPedidoDeberiaExistir'] == true )||
    ( isset( $_SESSION['ActivoGrande']) && $_SESSION['ActivoGrande']== true ) ||
    ( isset( $_SESSION['codPadreNoExiste']) && $_SESSION['codPadreNoExiste']== true )
){
    //algo dio error, go back para que allí de donde venga se muestre el error
    echo "<script>history.back();</script>";
    exit;
} else {
    //no han habido errores
    $_SESSION["codArticulo"] = $codArticulo;
    $_SESSION["activo"] = $activo;
    $_SESSION["codContenidoPedidoPadre"] = $codContenidoPedidoPadre;
}

if( isset($_SESSION["editandoContenidoPedido"]) && $_SESSION["editandoContenidoPedido"] == "true"){
    //llegamos aquí si está todo OK y estamos editando
    $_SESSION["numPedido"]=$numPedidoOriginal;

    //rescatamos de session los datos subidos por ValidarDatos
    $codArticulo = ( isset($_SESSION["codArticulo"]) ? $_SESSION["codArticulo"] : null );
    $numPedidoOriginal = ( isset($_SESSION["numPedido"]) ? $_SESSION["numPedido"] : null );//por session llega el código ORIGINAL

    $activo = ( isset($_SESSION["activo"]) ? $_SESSION["activo"] : 1 );
    $codContenidoPedidoPadre = ( isset($_SESSION["codContenidoPedidoPadre"]) ? $_SESSION["codContenidoPedidoPadre"] : null );//por session llega el código ORIGINAL

    $numPedido = ( isset($_GET["numPedido"]) ? $_GET["numPedido"] : null ); //por la URL llega el código NUEVO
    $ContenidoPedido = new ContenidoPedido();
    $operacionExitosa = $ContenidoPedido->updateContenidoPedido($codArticulo, $numPedido, $numPedidoOriginal, $activo, $codContenidoPedidoPadre);
    if($operacionExitosa){
        $_SESSION['GoodUpdateContenidoPedido']= true;
    }
    header("Location: ../Views/ContenidoPedidosLISTAR.php");
    exit;
}else if( isset($_SESSION["nuevoContenidoPedido"]) && $_SESSION["nuevoContenidoPedido"] == "true" && $numPedidoLibre == true){

    //all good y estamos añadiendo artículo nuevo

    $_SESSION["numPedido"]=$numPedido;
    $operacionExitosa = ContenidoPedido::AltaContenidoPedido($codArticulo, $numPedido, $numPedidoOriginal, $activo, $codContenidoPedidoPadre);
    if($operacionExitosa){
        $_SESSION['GoodInsertContenidoPedido']= true;
        print"all good $operacionExitosa";
    } else{
        print"all bad $operacionExitosa";
    }
   header("Location: ../Views/ContenidoPedidosLISTAR.php");
    exit;
};

?>
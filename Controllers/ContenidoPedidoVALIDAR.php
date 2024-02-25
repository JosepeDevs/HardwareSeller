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

$numPedido = isset($_SESSION["numPedido"]) ? $_SESSION["numPedido"] : null;
$contenidoPedido = isset($_SESSION["CarritoConfirmado"]) ? $_SESSION["CarritoConfirmado"] : null;

foreach ($contenidoPedido as $index => $array) {
    $arrayDatosArticulo = $contenidoPedido[$index];
    $numLinea = $index+1;
    $codArticulo =  $arrayDatosArticulo['codigo'];
    $precio =  $arrayDatosArticulo['precio'];
    $descuento =  $arrayDatosArticulo['descuento'];
    $cantidad =  $arrayDatosArticulo['cantidad'];
    $activo = isset($_SESSION['activo']) ? $_SESSION['activo'] : 1;
    $numPedidoOriginal = isset($_SESSION['numPedidoOriginal']) ? $_SESSION['numPedidoOriginal'] : null;
    $numPedido = isset($_POST['numPedido']) ? $_POST['numPedido'] : null;

    $numPedidoValido = ContenidoPedido::ComprobarLongitud($numPedido,11);
    if($numPedidoValido == false) {    $_SESSION['LongNumPedido']= true; }

    $numPedidoOriginalValido = ContenidoPedido::ComprobarLongitud($numPedidoOriginal,11);
    if($numPedidoOriginalValido == false) {    $_SESSION['LongNumPedidoOriginal']= true; }

    $codArticuloValido = ContenidoPedido::ComprobarLongitud($codArticulo,8);
    if($codArticuloValido == false) {    $_SESSION['LongCodArticulo']= true; }

    $numLineaValida = ContenidoPedido::ComprobarLongitud($numLinea,11);
    if($numLineaValida == false) {    $_SESSION['LongNumeroLinea']= true; }

    $cantidadValida = ContenidoPedido::ComprobarLongitud($cantidad,11);
    if($cantidadValida == false) {    $_SESSION['LongCantidad']= true; }

    $precioEsFloat = ContenidoPedido::ValorFloat($precio);
    $precioEsFloat ? $precio = round($precio, 2) : $precioEsFloat ; //si era float lo redondeamos a la segunda cifra decimal
    if($precioEsFloat == false) {    $_SESSION['BadPrecio']= true; }

    $descuentoEsFloat = ContenidoPedido::ValorFloat($descuento);
    $descuentoEsFloat ? $descuento = round($descuento, 2) : $descuentoEsFloat ; //si era float lo redondeamos a la segunda cifra decimal
    if($descuentoEsFloat == false) {    $_SESSION['BadDescuento']= true; }

    $activoValido = ContenidoPedido::ComprobarLongitud($activo,1);
    if($activoValido == false) { $_SESSION['LongActivo']= true;}

    if(
        ( isset($_SESSION['LongNumPedido']) && $_SESSION['LongNumPedido'] == true) ||
        ( isset($_SESSION['LongNumPedidoOriginal']) && $_SESSION['LongNumPedidoOriginal'] == true) ||
        ( isset($_SESSION['LongCodArticulo']) && $_SESSION['LongCodArticulo'] == true ) ||
        ( isset($_SESSION['LongNumeroLinea']) && $_SESSION['LongNumeroLinea'] == true )||
        ( isset($_SESSION['LongCantidad']) && $_SESSION['LongCantidad'] == true )||
        ( isset( $_SESSION['BadPrecio']) && $_SESSION['BadPrecio']== true ) ||
        ( isset( $_SESSION['BadDescuento']) && $_SESSION['BadDescuento']== true ) ||
        ( isset( $_SESSION['LongActivo']) && $_SESSION['LongActivo']== true )
    ){
        //algo dio error, go back para que allí de donde venga se muestre el error
        echo "<script>history.back();</script>";
        exit;
    } else {
        //array con objetos "contenidopedido",ahora en cada indice con todos los datos que me hacen falta para dar de alta el contenidoPedido 
        $arrayCotenidoPedido [] = new ContenidoPedido($numPedido, $numLinea, $codArticulo, $cantidad, $precio, $descuento, $activo);
    }
}

    if($numPedido == $numPedidoOriginal ){//si el código escrito es el mismo --> es que estaban editando y no lo quieren cambiar
        $mantenemosnumPedido= true;
    } else{
        $mantenemosnumPedido= false;
    }


    if( isset($_SESSION["editandoContenidoPedido"]) && $_SESSION["editandoContenidoPedido"] == "true" ){
      //todo esto tendré que manejarlo recibiendo datos por $_POST
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

        if( isset($_SESSION["editandoContenidoPedido"]) && $_SESSION["editandoContenidoPedido"] == "true"){
            //llegamos aquí si está todo OK y estamos editando
            $_SESSION["numPedido"]=$numPedidoOriginal;
    
            //rescatamos de session los datos subidos por ValidarDatos
            $codArticulo = ( isset($_SESSION["codArticulo"]) ? $_SESSION["codArticulo"] : null );
            $numPedidoOriginal = ( isset($_SESSION["numPedido"]) ? $_SESSION["numPedido"] : null );//por session llega el código ORIGINAL
    
            $activo = ( isset($_SESSION["activo"]) ? $_SESSION["activo"] : 1 ); //si no encuentra nada de forma predeterminada estará activado valdrá (1)
            $codContenidoPedidoPadre = ( isset($_SESSION["codContenidoPedidoPadre"]) ? $_SESSION["codContenidoPedidoPadre"] : null );//por session llega el código ORIGINAL
    
            $numPedido = ( isset($_GET["numPedido"]) ? $_GET["numPedido"] : null ); //por la URL llega el código NUEVO
            $ContenidoPedido = new ContenidoPedido();
            $operacionExitosa = $ContenidoPedido->updateContenidoPedido($numPedido, $numPedidoOriginal, $numLinea, $codArticulo, $cantidad, $precio, $descuento, $activo);
            if($operacionExitosa){
                $_SESSION['GoodUpdateContenidoPedido']= true;
            }
    
        }


    }else if( isset($_SESSION["nuevoPedido"]) && $_SESSION["nuevoPedido"] == "true" ){
        //todo hacer esto y alta de pedido transaccional para que ocurra todo o no ocurra nada
        $contador=0;
        foreach ($arrayCotenidoPedido as $contenidoPedido) {
            //all good y estamos añadiendo artículo nuevo
            $numPedido=$contenidoPedido->getNumPedido();
            $numLinea=$contenidoPedido->getNumLinea();
            $codArticulo=$contenidoPedido->getCodArticulo();
            $cantidad=$contenidoPedido->getCantidad();
            $precio=$contenidoPedido->getPrecio();
            $descuento=$contenidoPedido->getDescuento();
            $activo=$contenidoPedido->getActivo();
            $operacionExitosa = ContenidoPedido::AltaContenidoPedido($numPedido, $numLinea,$codArticulo, $cantidad, $precio, $descuento, $activo);
            if($operacionExitosa){
                $contador+= 1;
            } 
            if($contador == count($arrayContenidoPedido)){
                $_SESSION['GoodInsertContenidoPedido']= true;
                print"all good $operacionExitosa";
            }
        }
    };

header("Location: ../Views/ContenidoPedidoLISTAR.php");
exit;

?>
<?php

//ESTE RECOGE LOS DATOS Y LOS MANDA A MODELOS
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "PedidoVALIDAR dice: no está user en session";
   header("Location: /index.php");
}

include_once("../Models/Pedido.php");

$fecha = date("Y-m-d"); //esto no lo comprobamos porque lo estoy generando aquí mismo
$_SESSION['fecha']=$fecha;
print$fecha;
print_r($_SESSION);
$total = isset($_SESSION["total"]) ? round($_SESSION["total"],2) : null;
$estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : null; //aquío llegará solo 3 o 4 en función del método de pago
$codUsuario = isset($_SESSION["codUsuario"]) ? $_SESSION["codUsuario"] : null; //dni
$activo = isset($_SESSION["PedidoActivo"]) ? $_SESSION["PedidoActivo"] : 1; //de forma predeterminada valdra 1

//no permitimos la edición del número de pedido, eso lo calcula la BBDD

$totalEsFloat = Pedido::ValorFloat($total);
$totalEsFloat ? $total = round($total, 2) : $totalEsFloat ; //si era float lo redondeamos a la segunda cifra decimal
if($totalEsFloat == false) {    $_SESSION['Badtotal']= true; }

$estadoValido = Pedido::ComprobarLongitud($estado,1);
if($estadoValido == false) {    $_SESSION['LongEstado']= true; }


include_once("../Models/Cliente.php");
$usuarioExiste = Cliente::getClienteByDni($codUsuario);
if($usuarioExiste == false) {    $_SESSION['ClienteNoExiste']= true; }

include_once("../Controllers/OperacionesSession.php");
$rol=GetRolDeSession();

//no comprobamos si es pedido nuevo o pedido que está siendo editando, porque a estas alturas cogeriamos los códigos y para pedidos los da la BBDD

if(
    ( isset($_SESSION['Badtotal']) && $_SESSION['Badtotal'] == true) ||
    ( isset($_SESSION['LongEstado']) && $_SESSION['LongEstado'] == true) ||
    ( isset($_SESSION['ClienteNoExiste']) && $_SESSION['ClienteNoExiste'] == true )
){
    //algo dio error, go back para que allí de donde venga se muestre el error
  //  echo "<script>history.back();</script>";
    exit;
} 

if( isset($_SESSION["editandoPedido"]) && $_SESSION["editandoPedido"] == "true"){
    //llegamos aquí si está todo OK y estamos editando

    //rescatamos de session los datos subidos
    $Pedido = new Pedido();
    $numPedido = $Pedido->updatePedido($numPedido, $fecha, $total, $estado, $codUsuario, $activo);
    if($operacionExitosa){
        $_SESSION['GoodUpdatePedido']= true;
    }
   // header("Location: ../Views/PedidosLISTAR.php");
    exit;
}else if( isset($_SESSION["nuevoPedido"]) && $_SESSION["nuevoPedido"] == "true"){
    unset($_SESSION['nuevoPedido']);
    //all good y estamos añadiendo artículo nuevo

    $numPedido = Pedido::AltaPedido($fecha, $total, $estado, $codUsuario, $activo);
    if($numPedido !== false){
        $_SESSION['GoodInsertPedido']= true;
        $pedido = Pedido::getPedidoByNumPedido($numPedido);
        $_SESSION['pedido']= $pedido;
        print"all good $operacionExitosa";
    }
    if(isset($_SESSION['CarritoConfirmado']) && !empty($_SESSION['CarritoConfirmado']) ){
        //ahora que ya tenemos el pedido creado y en session vamos a poblar su contenido
       // header("Location: ../Controllers/ContenidoPedidoVALIDAR.php");
        exit;
    }else{
      //  header("Location: ../Views/PedidosLISTAR.php");
         exit;
    }
};

?>
<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

// NO PROTEGER PARA QUE SE PUEDAN HACER PEDIDOS SIN REGISTRARSE


include_once("../Models/Pedido.php");

if(isset($_POST["estado"]) || isset($_POST["fecha"]) ) {
    //ENtramos aqui si estamos editando el pedido
    print"entramos a coger variables";
    //con que compruebe que hay algún dato además del estado sabremos que venimos de modificar como admin los pedidos
    $idPedido = isset($_SESSION["idPedido"]) ? $_SESSION["idPedido"] : null; // CUIDADO QUE ESTO SE SACA DE *****SESSION*******, NO DE POST
    $codUsuario = isset($_SESSION["codUsuario"]) ? $_SESSION["codUsuario"] : null; //dni
    $total = isset($_SESSION["total"]) ? round($_SESSION["total"],2) : null;

    $fecha = isset($_POST["fecha"]) ? $_POST["fecha"] : null;
    $estado = isset($_POST["estado"]) ? $_POST["estado"] : null; 
    $activo = isset($_POST["activo"]) ? $_POST["activo"] : null; 
}else {
    //entramos aquí al crear pedidos, solo llega por post el estado 
    //No necesitamso idPedido, lo dará la BBDD
    $fecha = date("Y-m-d"); //esto no lo comprobamos porque lo estoy generando aquí mismo
    $_SESSION['fecha']=$fecha;
    $total = isset($_SESSION["total"]) ? round($_SESSION["total"],2) : null;
    $_SESSION['estado'] = ($_SESSION['estado'] . "2");  // añadimos el 2 para indicar que ya han confirmado el pedido y está en firme
    $estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : null; //aquí llegará solo 3 o 4 en función del método de pago
    $codUsuario = isset($_SESSION["codUsuario"]) ? $_SESSION["codUsuario"] : null; //dni
    $activo = isset($_SESSION["PedidoActivo"]) ? $_SESSION["PedidoActivo"] : 1; //de forma predeterminada valdra 1
}

//no permitimos la edición del número de pedido, eso lo calcula la BBDD

$totalEsFloat = Pedido::ValorFloat($total);
$totalEsFloat ? $total = round($total, 2) : $totalEsFloat ; //si era float lo redondeamos a la segunda cifra decimal
if($totalEsFloat == false) {    $_SESSION['Badtotal']= true; }

$estadoValido = Pedido::ComprobarLongitud($estado,11);
if($estadoValido == false) {    $_SESSION['LongEstado']= true; }

$fechaValida= Pedido::fechaValida($fecha);
if($fechaValida == false) {    $_SESSION['BadFecha']= true; }

include_once("../Models/Cliente.php");
$usuarioExiste = Cliente::getClienteByDni($codUsuario);
if($usuarioExiste == false) {    $_SESSION['ClienteNoExiste']= true; }

include_once("../Controllers/OperacionesSession.php");
$rol=GetRolDeSession();

if(
    ( isset($_SESSION['Badtotal']) && $_SESSION['Badtotal'] == true) ||
    ( isset($_SESSION['LongEstado']) && $_SESSION['LongEstado'] == true) ||
    ( isset($_SESSION['BadFecha']) && $_SESSION['BadFecha'] == true) ||
    ( isset($_SESSION['ClienteNoExiste']) && $_SESSION['ClienteNoExiste'] == true )
){
    //algo dio error, go back para que allí de donde venga se muestre el error
   echo "<script>history.back();</script>";
    exit;
} 
 
if( isset($_SESSION["editandoPedido"]) && $_SESSION["editandoPedido"] == "true"){
    //llegamos aquí si está todo OK y estamos editando
    $_SESSION["editandoPedido"] == "false";
    //rescatamos de session los datos subidos
    $Pedido = new Pedido();
    $idPedido = $Pedido->updatePedido($idPedido, $fecha, $total, $estado, $codUsuario, $activo);
    if($operacionExitosa){
        $_SESSION['GoodUpdatePedido']= true;
    }
   header("Location: ../Views/PedidosLISTAR.php");
    exit;
}else if( isset($_SESSION["nuevoPedido"]) && $_SESSION["nuevoPedido"] == "true"){
    //all good y estamos añadiendo artículo nuevo
    $_SESSION["nuevoPedido"] == "false";

    $numPedido = Pedido::AltaPedido($fecha, $total, $estado, $codUsuario, $activo);
    if($numPedido !== false){
        $_SESSION['GoodInsertPedido']= true;
        $_SESSION['numPedido']= $numPedido;
       // print"all good $pedido";
    }
    if(isset($_SESSION['CarritoConfirmado']) && !empty($_SESSION['CarritoConfirmado']) ){
        //ahora que ya tenemos el pedido creado y en session vamos a poblar su contenido
       // print('hola');
        header("Location: ../Controllers/ContenidoPedidoVALIDAR.php");
        exit;
    }else{
       // print('hola2');
        header("Location: ../Views/ PedidosLISTAR.php");
        exit;
    }
};

?>
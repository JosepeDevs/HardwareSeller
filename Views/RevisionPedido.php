<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
$_SESSION['nuevoPedido']="true";
include_once("../Views/header.php");
?>
<h1>Revisión del pedido</h1>
<?php //include_once("../Views/aside.php") ?>
<h2> Referencias y cantidades seleccionadas</h2>
<table class="table">
    <thead>
        <tr>
            <th>Nº linea del pedido</th>
            <th>Código del producto</th>
            <th>Producto</th>
            <th>Precio (€)</th>
            <th>Descuento (%)</th>
            <th>Cantidad</th>
            <th>Sub total (€)</th>
        </tr>
    </thead>
    <tbody>
<?
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

include_once('../Controllers/ArticuloBUSCARController.php');
if(isset($_SESSION['CarritoConfirmado'])){
    $arrayItems = $_SESSION['CarritoConfirmado'];//array asociativo con codigo del articulo y cantidad
    foreach($arrayItems as $index => $arrayDatosArticulo){//aquí los indices al ser asociativo son los propios codigos de artículo
        $codigo = $arrayDatosArticulo["codigo"];
        $articulo = getArticuloByCodigo($codigo);
        $precio = $arrayDatosArticulo["precio"];
        $descuento = $arrayDatosArticulo["descuento"];
        $cantidad = $arrayDatosArticulo["cantidad"];
        $subTotal=($precio*(1-($descuento/100)))*$cantidad;
        if($articulo !== false){
            echo'
            <tr>
                <td>'.$index.'</td>
                <td>'.$codigo.'</td>
                <td>'.$articulo->getNombre().'</td>
                <td>'.$precio.'</td>
                <td>'.$descuento.'</td>
                <td>'.$cantidad.'</td>
                <td>'.$subTotal.'</td>
            </tr>
            ';
            $arraySubtotales [] = $subTotal;
            $total = array_sum($arraySubtotales);
            $_SESSION['total'] = $total;
        } else{
            $total=0;
            echo '<tr><td colspan="5"><p>Carrito sin artículos que mostrar</p></td>';
        }
    } 
    echo'
    <tfoot>
        <tr>';
            if(count($_SESSION['CarritoConfirmado']) > 0){ 
                echo'
                    <td colspan="3"><h4> TOTAL (€) (IVA incluido): </h4></td>
                    <td colspan="4"><h2><b class="total">'.round($total,2).'</b></h2></td>
                 <br>';
            } 
        echo'
        </tr>
    </tfoot>
</table>';
}
?>
<br><br>
<button type='button'><a href='../Views/Carrito.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Modificar cantidades</a></button>
<br><br><br><br>
<?
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

if(isset($_SESSION['user'])) {
    include_once("../Controllers/ClienteBUSCARController.php");
    $usuario = getClienteByEmail($_SESSION['user']);
    $dni=$usuario->getDni();
    $_SESSION['codUsuario'] = $dni;
    include_once('../Controllers/ClienteBUSCARController.php');
    //NOTHING LIKE A GOOD RETURNING CLIENT!
    echo"
    <h2> Datos usuario </h2>
    <br>
    <p>Nombre: ".$usuario->getNombre()."</p>
    <p>DNI: ".$usuario->getDni()."</p>
    <p>Email: ".$usuario->getEmail()."</p>
    <p>Teléfono: ".$usuario->getTelefono()."</p>
    <p>Dirección: ".$usuario->getDireccion()."</p>
    <p>Localidad: ".$usuario->getLocalidad()."</p>
    <p>Provincia: ".$usuario->getProvincia()."</p>";
}
$estado= isset($_SESSION["estadoEnvio"])? $_SESSION["estadoEnvio"]: null;
if (strpos($estado,"5")==false){
    //si encontramos un 5 es que querían recogida en tienda
    //no hace nada aquí dentro, si seleccionaron 5 el mensaje correspondiente se muestra en la seccion de método de pago
    echo"<h3>Han seleccionado Recogida en tienda</h3>";
} else{
    //en cualquier otro caso eligieron el envío  a su dirección
    echo"<h3>Han seleccionado la opción de envío a esta dirección. Gratis hasta que se implemente la búsqueda de precio en una tarifa de nuestros transportistas y se incluya en el total</h3>";
    $_SESSION['estado'] = ($_SESSION['estado'] . 0); //metemos esto en el session de estado para indicar que es envío a dirección del cliente
}


?>
<br><br>
<button type='button'><a href='../Views/DireccionPedido.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Escoger otra opción de envío/recogida</a></button>
<br>
<button type='button'><a href='../Views/ClienteEDITAR.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-up'></i>Modificar dirección de envío</a></button>
<br><br><br><br>

<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

//todo de aquík pabajo nose ve

if(isset($_POST["estado"])) {
    if( $_POST["estado"] == 3 ){
        echo"
        <h2>Transferencia bancaria</h2>
        <br>
        <p> 
            Indique en el concepto de la transferencia el número de pedido de la siguiente página (también disponible en su área de cliente), una vez confirme el pedido.
        </p>";
        $_SESSION['estado'] = ($_SESSION['estado'] . 3);  //pedido confirmado, transferencia, pago pendiente, envio pendiente
    }
    if( $_POST["estado"] == 4 ){
        echo"
        <h2>Pago mediante tarjeta</h2>
        <br>
        <p> 
        ¡Actualmente no disponible, gracias por su comprensión! Aunque marque esta opción tendrá que pagar por transferencia.
        </p>
        <p> 
        Indique en el concepto de la transferencia el número de pedido de la siguiente página (también disponible en su área de cliente), una vez confirme el pedido.
        </p>";
        $_SESSION['estado'] = ($_SESSION['estado'] . 4);   //pedido confirmado, tarjeta, pago pendiente, envio pendiente
    }
    if( $_POST["estado"] == 5 ){
        echo"
        <h2>Pago y recogida en tienda</h2>
        <p> Nuestra dirección: Calle existente nº infinito, avenida de la indeterminación/0 , CP 00000, Elche, Alicante, España, Europa, Tierra, Universo #3 </p>
        <p> Se reservará el stock un máximo de 5 días, transcurrido ese tiempo se pondrá de nuevo a la venta</p>
        <br>";
        $_SESSION['estado'] = ($_SESSION['estado'] . 4);   //pedido confirmado, tarjeta, pago pendiente, envio pendiente
    }
   
}

?>
<br><br>
<button type='button'><a href='../Views/MetodoDePago.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i>Modificar método de pago</a></button>
<br><br><br><br>
<button type='button'><a href='../Controllers/PedidoVALIDAR.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-right'></i><i class='lni lni-chevron-right'></i><b>CONFIRMAR PEDIDO</b></a></button>

<?

include_once("../Views/footer.php");

?>
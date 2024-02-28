<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//NO PROTEGER SI QUEREMOS QUE GENTE SIN REGISTRASE  PUEDA HACER PEDIDOS
$_SESSION['nuevoPedido']="true";
print_r($_SESSION);
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

$estadoEnvio= isset($_SESSION["estadoEnvio"])? $_SESSION["estadoEnvio"]: null;


$nombre=isset($_SESSION['nombre']) ? $_SESSION['nombre'] : null;
$email=isset($_SESSION['email']) ? $_SESSION['email'] : null;
$dni=isset($_SESSION['dni']) ? $_SESSION['dni'] : null;
$telefono=isset($_SESSION['telefono']) ? $_SESSION['telefono'] : null;
$direccion=isset($_SESSION['direccion']) ? $_SESSION['direccion'] : null;
$localidad=isset($_SESSION['localidad']) ? $_SESSION['localidad'] : null;
$provincia=isset($_SESSION['provincia']) ? $_SESSION['provincia'] : null;


if(!isset($_SESSION['estado'])){
    $_SESSION['estado']=null;
}

if(isset($_SESSION['user'])) {
    include_once("../Controllers/ClienteBUSCARController.php");
    $usuario = getClienteByEmail($_SESSION['user']);
    $dni=$usuario->getDni();
    $_SESSION['codUsuario'] = $dni;
    include_once('../Controllers/ClienteBUSCARController.php');
    
    if (strpos($estadoEnvio,"5") !== false){
        //si encontramos un 5 en algun sitio de estadoEnvio es que querían recogida en tienda
        echo"<h3>Han seleccionado Recogida del pedido en tienda.</h3>";
        $_SESSION['estado'] = ($_SESSION['estado'] . "5"); //metemos esto en el session de estado para indicar que es envío a dirección del cliente}if ($estado =="direccionYcuenta"){
    } else if (strpos($estadoEnvio,"0") !== false){
        //si es 0 es envío,  leemos sus datos
        echo"<h3>Han seleccionado la opción de envío a esta dirección. Gratis hasta que se implemente la búsqueda de precio en una tarifa de nuestros transportistas y se incluya en el total</h3>";
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
        $_SESSION['estado'] = ($_SESSION['estado'] . "0"); //metemos esto en el session de estado para indicar que es envío a dirección del cliente
    }else{
        echo"<h3>No pudimos determinar el método de envío/recogida seleccionado </h3>";
    }

} else{
   //NO existe user (estan comprando sin registrarse)
    if (strpos($estadoEnvio,"5") !== false){
        //si encontramos un 5 es que querían recogida en tienda
        echo"<h3>Han seleccionado Recogida del pedido en tienda.</h3>";
        $_SESSION['estado'] = ($_SESSION['estado'] . "5"); //metemos esto en el session de estado para indicar que es envío a dirección del cliente}if ($estado =="direccionYcuenta"){
    } else if (strpos($estadoEnvio,"0") !== false){
        //si es 0 es envío, decimos leemos sus datos
        echo"<h3>Han seleccionado la opción de envío a esta dirección. Gratis hasta que se implemente la búsqueda de precio en una tarifa de nuestros transportistas y se incluya en el total</h3>";
        echo("<p>Dirección de envío: Nombre=$nombre, DNI=$dni, telefono=$telefono, Direccion=$direccion, Poblacion=$localidad, Provincia=$provincia, email=$email</p>");
        $_SESSION['estado'] = ($_SESSION['estado'] . "0"); //metemos esto en el session de estado para indicar que es envío a dirección del cliente
    }else{
        echo"<h3>No pudimos determinar el método de envío/recogida seleccionado </h3>";
    }
}

?>
<br><br>
<button type='button'><a href='../Views/DireccionPedido.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Escoger otra opción de envío/recogida</a></button>
<br>

<?
if(isset($_SESSION["user"])) { ?>
    <button type='button'><a href='../Views/ClienteEDITAR.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-up'></i>Modificar mi dirección de envío en mi area de cliente (tendrá que pasar por el carrito de nuevo)</a></button>
    
<?php
}

if(isset($_POST["estado"])) {
    if(!isset($_SESSION['estado'])){
        $_SESSION['estado']='';
    }
    if( $_POST["estado"] == 3 ){
        echo"<br><br><br><br>
        <h2>Transferencia bancaria</h2>
        <br>
        <p> 
            Indique en el concepto de la transferencia el número de pedido de la siguiente página (también disponible en su área de cliente), una vez confirme el pedido.
        </p>";
        $_SESSION['estado'] = ($_SESSION['estado'] . "3");  
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
        $_SESSION['estado'] = ($_SESSION['estado'] . "4");   
    }
    if( $_POST["estado"] == 5 ){
        echo"
        <h2>Pago y recogida en tienda</h2>
        <p> Nuestra dirección: Calle existente nº infinito, avenida de la indeterminación/0 , CP 00000, Elche, Alicante, España, Europa, Tierra, Universo #3 </p>
        <p> Se reservará el stock un máximo de 5 días, transcurrido ese tiempo se pondrá de nuevo a la venta</p>
        <br>";
        if (strpos($_SESSION['estado'],"5") == false){
            //si no lo entontramos el numero en $_SESSION['estado'] lo metemos 
            $_SESSION['estado'] = ($_SESSION['estado'] . "5");  
        } 
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
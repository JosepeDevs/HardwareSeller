<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//NO PROTEGER SI QUEREMOS QUE GENTE SIN REGISTRASE  PUEDA HACER PEDIDOS
$_SESSION['nuevoPedido']="true";
include_once("../Views/header.php");
print_r($_SESSION);
?>
<h1>Revisión del pedido</h1>
<?php //include_once("../Views/aside.php") ?>
<h2> Referencias y cantidades seleccionadas</h2>
<table class="table">
        <tr>
            <th>Nº linea del pedido</th>
            <th>Código del producto</th>
            <th>Producto</th>
            <th>Precio (€)</th>
            <th>Descuento (%)</th>
            <th>Cantidad</th>
            <th>Sub total (€)</th>
        </tr>

<?
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
/////////////////////////CANTIDADES
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
        <tr>';
            if(count($_SESSION['CarritoConfirmado']) > 0){ 
                echo'
                    <td colspan="3"><h4> TOTAL (€) (IVA incluido): </h4></td>
                    <td colspan="4"><h2><b class="total">'.round($total,2).'</b></h2></td>
                 <br>';
            } 
        echo'
        </tr>
</table>';
}
?>
<br><br>
<button type='button'><a href='../Views/Carrito.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Modificar cantidades</a></button>
<br><br><br><br>
<?
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//////////////////////////////////////////DIRECCION ENVIO
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

if(isset($_SESSION['sinCuenta']) && $_SESSION['sinCuenta']== true && isset($_SESSION['dni']) ){
    $_SESSION['codUsuario'] = $_SESSION['dni'];
}else if(isset($_SESSION['user'])){
   $usuario = getClienteByEmail($_SESSION['user']);
   $dni=$usuario->getDni();
   $_SESSION['codUsuario'] = $dni;
} else{
    $_SESSION['codUsuario'] = "No hay DNI";
}

if(isset($_SESSION['user'])) {
    include_once("../Controllers/ClienteBUSCARController.php");
    
    if (strpos($estadoEnvio,"5") !== false){
        //si encontramos un 5 en algun sitio de estadoEnvio es que querían recogida en tienda
        echo"<h2>Han seleccionado Recogida del pedido en tienda.</h2>";
        $_SESSION['estado'] = ($_SESSION['estado'] . "5"); //metemos esto en el session de estado para indicar que es envío a dirección del cliente}if ($estado =="direccionYcuenta"){
    } else if (strpos($estadoEnvio,"0") !== false){
        //si es 0 es envío,  leemos sus datos
        echo"<h2>Han seleccionado la opción de envío a esta dirección.</h2>";
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
        echo"<h3>Gatos de transporte gratis hasta que se implemente la búsqueda de precio en una tarifa de nuestros transportistas y se incluya en el total</h3>";
        $_SESSION['estado'] = ($_SESSION['estado'] . "0"); //metemos esto en el session de estado para indicar que es envío a dirección del cliente
    }else if($estadoEnvio==null){
        echo"<h2>método de envío/recogida no determinado </h2>";
    }else{
        echo"<h2>No pudimos determinar el método de envío/recogida seleccionado </h2>";
    }

} else{
   //NO existe user (estan comprando sin registrarse)
    if (strpos($estadoEnvio,"5") !== false){
        //si encontramos un 5 es que querían recogida en tienda
        echo"<h2>Han seleccionado Recogida del pedido en tienda.</h2>";
        $_SESSION['estado'] = ($_SESSION['estado'] . "5"); //metemos esto en el session de estado para indicar que es envío a dirección del cliente}if ($estado =="direccionYcuenta"){
    } else if (strpos($estadoEnvio,"0") !== false){
        //si es 0 es envío, decimos leemos sus datos
        echo"<h2>Han seleccionado la opción de envío. La dirección de envío es:</h2>";
        echo("<p>Dirección de envío:
        <br> Nombre=$nombre<br> DNI=$dni, <br>telefono=$telefono,<br> Direccion=$direccion,<br> Poblacion=$localidad,<br> Provincia=$provincia,<br> email=$email</p>");
        $_SESSION['estado'] = ($_SESSION['estado'] . "0"); //metemos esto en el session de estado para indicar que es envío a dirección del cliente
        echo"<h3>Gatos de transporte gratis hasta que se implemente la búsqueda de precio en una tarifa de nuestros transportistas y se incluya en el total</h3>";
    }else{
        echo"<h2>No pudimos determinar el método de envío/recogida seleccionado </h2>";
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
        <h2>Pago en tienda</h2>
        <p> Nuestra dirección: Calle existente nº infinito, avenida de la indeterminación/0 , CP 00000, Elche, Alicante, España, Europa, Tierra, Universo #3 </p>
        <p> Se reservará el stock un máximo de 5 días, transcurrido ese tiempo se pondrá de nuevo a la venta</p>
        <p> Si seleccionó envío a su dirección se lo haremos llegar tan pronto venga a nuestra tienda a realizar el pago y tengamos stock, si ya hubiera disponible cuando realice el pago podrá llevarse el material usted mismo. </p>
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
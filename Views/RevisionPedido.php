<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

include_once("../Views/header.php");
?>
<h1>Confirmación del pedido</h1>
<?php include_once("../Views/aside.php") ;


if(isset($_SESSION['user'])) {
            include_once('../Controllers/ClienteBUSCARController.php');
            $usuario = getClienteByemail($_SESSION['user']);
            //NOTHING LIKE A GOOD RETURNING CLIENT!
            echo"
            <h2>Datos usuario y dirección de envío</h2>
            <br>
            <p>Nombre: ".$usuario->getNombre()."</p>
            <p>Email: ".$usuario->getEmail()."</p>
            <p>Teléfono: ".$usuario->getTelefono()."</p>
            <p>Dirección: ".$usuario->getDireccion()."</p>
            <p>Localidad: ".$usuario->getLocalidad()."</p>
            <p>Provincia: ".$usuario->getProvincia()."</p>";
}
?>
<button type='button'><a href='../Views/ClienteEDITAR.php' class='btn btn-warning'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Modificar dirección de envío</a></button>
<br><br><br><br>

<?

if(isset($_POST["estado"])) {
    if( $_POST["estado"] == 3 ){
        echo"
        <h2>Transferencia bancaria</h2>
        <br>
        <p> 
            Indique en el concepto de la transferencia el número de pedido de la siguiente página (también disponible en su área de cliente)
        </p>";
    }
    if( $_POST["estado"] == 4 ){
        echo"
        <h2>Pago mediante tarjeta</h2>
        <br>
        <p> 
            ¡Actualmente no disponible, gracias por su comprensión! Aunque marque esta opción le aparecerá para pagar por transferencia.
        </p>";
    }
?>
<button type='button'><a href='../Views/MetodoDePago.php' class='btn btn-warning'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Modificar métood de pago</a></button>
<br><br><br><br>
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
                    <td colspan="3"><h4> TOTAL (€): </h4></td>
                    <td colspan="4"><h2><b class="total">'.round($total,2).'</b></h2></td>
                 <br>';
            } 
        echo'
        </tr>
    </tfoot>';
}

?>
<button type='button'><a href='../Views/Carrito.php' class='btn btn-warning'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Modificar cantidades</a></button>
<br><br><br>
<h2><button type='button'><a href='../Views/PedidoConfirmado.php' class='btn btn-warning'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>CONFIRMAR PEDIDO</a></button></h2>
<?
}

include_once("../Views/footer.php");

?>
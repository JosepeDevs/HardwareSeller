<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//ESTA PÁGINA NO SE DEBE PROTEGER, ACCESIBLE A TODOS LOS NAVEGANTES


$codigoParaCarrito = isset($_GET["codigo"]) ? $_GET["codigo"] : "";

if(!array_key_exists('productos', $_SESSION)) {
    $_SESSION['productos'] = []; // declara que dentro de la key "productos" vamos a guardar un array
}

//mira si existe ya el producto, si ya existe añade 1 , si no existe, guarda 1
$_SESSION['productos'][$codigoParaCarrito] = array_key_exists($codigoParaCarrito, $_SESSION['productos']) ? $_SESSION['productos'][$codigoParaCarrito] + 1 : 1;

?>
<div class="panel-body">
    <h1>Vista previa del pedido</h1>
    <table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Descuento</th>
            <th>Cantidad</th>
            <th>Sub total</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if(count($_SESSION['productos']) > 0){
            $arrayItems = array($_SESSION['productos']);
            foreach($arrayItems as $item){
                $precio=$item->getPrecio();
                $descuento=$item->getDescuento();
                $cantidad=$item["$codigoParaCarrito"];
                $subTotal=($precio*(1-($descuento/100)))*$cantidad;
                echo'
                <tr>
                    <td>'.$item->getNombre().'</td>
                    <td>'.$precio.' €'.'</td>
                    <td>'.$descuento.'</td>
                    <td>'.$cantidad.'</td>
                    <td>'.$subTotal.' €'.'</td>
                </tr>
                ';
                $arraySubtotales [] = $subTotal;
                $total = array_sum($arraySubtotales);
            } 
        }else{
            echo '<tr><td colspan="4"><p>Carrito sin artículos (carrito vacío)</p></td>';
        } 
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"></td>
            <?php if(count($_SESSION['productos']) > 0){ ?>
                <td class="text-center"><strong>Total <?php echo $total.' €'; ?></strong></td>
            <?php } ?>
        </tr>
    </tfoot>
    </table>
    <div class="shipAddr">
        <h4>Datos usuario y dirección de envío</h4>
        <?php

        echo"
        <p> Nombre</p>
        <p>email</p>
        <p>telefono</p>
        <p>direccion</p>
        <p>localidad</p>
        <p>provincia</p>
        <p>CP</p>
        ";
        ?>
    </div>
    <div class="footBtn">
        <a href="index.php" class="btn btn-warning"><i class="glyphicon glyphicon-menu-left"></i> Continue Comprando</a>
        <a href="AccionCarta.php?action=placeOrder" class="btn btn-success orderBtn">Realizar pedido <i class="glyphicon glyphicon-menu-right"></i></a>
    </div>
</div>

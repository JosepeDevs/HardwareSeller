<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//ESTA PÁGINA NO SE DEBE PROTEGER, ACCESIBLE A TODOS LOS NAVEGANTES

//HEADER Y TITULO
include_once("header.php");

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
        include_once('../Controllers/OperacionesSession.php');
        $estaClienteRegistrado = UserEstablecido();
        $usuario = getClienteByemail($_SESSION['user']);
        if($estaClienteRegistrado){
            //NOTHING LIKE A GOOD RETURNING CLIENT!
            echo"
            <p>".$usuario->getNombre()."</p>
            <p>".$usuario->getEmail()."</p>
            <p>".$usuario->getTelefono()."</p>
            <p>".$usuario->getDireccion()."</p>
            <p>".$usuario->getLocalidad()."</p>
            <p>".$usuario->getProvincia()."</p>
            ";
        } else{
            //ESTABAN COMPRANDO SIN REGISTRARSE LOS MUY TRUANES
            $_SESSION['RegistroInSitu'] = 1;
            echo '
            <form action="../Controllers/ValidarDatosCliente.php" method="post">
                <table>
                    <tr>
                        <th><label for="nombre">Nombre:</label></th>
                        <th><label for="direccion">direccion:</label></th>
                        <th><label for="localidad">localidad:</label></th>
                        <th><label for="provincia">provincia:</label></th>
                        <th><label for="telefono">telefono:</label></th>
                        <th><label for="email">email:</label></th>
                        <th><label for="dni">DNI:</label></th>
                        <th><label for="psswrd">contraseña:</label></th>
                    </tr>
                    <tr>
                        <td><input type="text" name="nombre" id="nombre" required><br><br></td>
                        <td><input type="text" name="direccion" id="direccion" required ><br><br></td>
                        <td><input type="text" name="localidad" id="localidad" required ><br><br></td>
                        <td><input type="text" name="provincia" id="provincia" required><br><br>
                        <td><input type="tel" name="telefono" id="telefono" required><br><br>
                        <td><input type="email" name="email" id="email" required><br><br>
                        <td><input type="text" name="dni" id="dni" required pattern="^\d{8}\w{1}$"><br><br></td>
                        <td><input type="password" name="psswrd" id="pssword" required><br><br>
                    </tr>
                </table>
                <h2><input type="submit" value="Guardar"></h2>
            </form>
            ';
        }

        //SECCION ERRORES EN EL ALTA DE USER
        include_once("../Controllers/ClienteALTAMensajes.php");
        $arrayMensajes=getArrayMensajesNuevo();
        if(is_array($arrayMensajes)){
            foreach($arrayMensajes as $mensaje) {
                echo "<h3>$mensaje</h3>";
            }
        };
        //todo si suben a session la seccion que estaba navegando podemos consultarla aquí para que cuando le dén a seguir navegando le siga listando articulos relevantes
        ?>
    </div>
    <div class="footBtn">
        <a href="../Views/Catalogo.php" class="btn btn-warning"><i class="glyphicon glyphicon-menu-left"></i> Seguir navegando </a>
        <a href="AccionCarta.php?action=placeOrder" class="btn btn-success orderBtn">Continuar con el pedido <i class="glyphicon glyphicon-menu-right"></i></a>
    </div>
</div>
<?php
include_once("footer.php");
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
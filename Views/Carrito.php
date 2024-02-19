<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//ESTA PÁGINA NO SE DEBE PROTEGER, ACCESIBLE A TODOS LOS NAVEGANTES

//HEADER Y TITULO
include_once("header.php");

?>
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
    include_once('../Controllers/ArticuloBUSCARController.php');
        if(count($_SESSION['productos']) > 0){
            $arrayItems = $_SESSION['productos'];//array asociativo con codigo del articulo y cantidad
            foreach($arrayItems as $codigo => $cantidad){//aquí los indices al ser asociativo son los propios codigos de artículo
                $articulo = getArticuloByCodigo($codigo);
                if($articulo !== false){
                    $precio=$articulo->getPrecio();
                    $descuento=$articulo->getDescuento();
                    $cantidad = $arrayItems[$codigo];
                    $subTotal=($precio*(1-($descuento/100)))*$cantidad;
                    echo'
                    <tr>
                        <td>'.$articulo->getNombre().'</td>
                        <td>'.$precio.' €'.'</td>
                        <td>'.$descuento.'</td>
                        <td>'.$cantidad.'</td>
                        <td>'.$subTotal.' €'.'</td>
                    </tr>
                    ';
                    $arraySubtotales [] = $subTotal;
                    $total = array_sum($arraySubtotales);
                } else{
                    $total=0;
                    echo '<tr><td colspan="5"><p>Carrito sin artículos que mostrar</p></td>';
                }
            } 
        }else{
            echo '<tr><td colspan="5"><p>Carrito sin artículos (carrito vacío)</p></td>';
        } 
    ?>
    </tbody>
    <tfoot>
        <tr>
            <?php if(count($_SESSION['productos']) > 0){ ?>
                <td class="text-center" colspan="5"><h2><b>Total <?php echo $total.' €'; ?></b></h2></td>
                <br>
            <?php } ?>
        </tr>
    </tfoot>
    </table>
    <?php
        if(isset($_SESSION['user'])) {
            include_once('../Controllers/ClienteBUSCARController.php');
            $usuario = getClienteByemail($_SESSION['user']);
            //NOTHING LIKE A GOOD RETURNING CLIENT!
            echo"
            <h2>Datos usuario y dirección de envío</h2>
            <p>Nombre: ".$usuario->getNombre()."</p>
            <p>Email: ".$usuario->getEmail()."</p>
            <p>Teléfono: ".$usuario->getTelefono()."</p>
            <p>Dirección: ".$usuario->getDireccion()."</p>
            <p>Localidad: ".$usuario->getLocalidad()."</p>
            <p>Provincia: ".$usuario->getProvincia()."</p>
            <br>
            <div class='finForm'>
                <button type='button'><a href='../Views/Catalogo.php' class='btn btn-warning'><i class='lni lni-chevron-left'></i>Seguir navegando</a></button>
                <button type='submit' class='submit-button'><span>Proceder al método de pago </span><i class='lni lni-chevron-right'></i></button> 
                </form> 
            </div>
            ";
        } else{
            //ESTABAN COMPRANDO SIN REGISTRARSE LOS MUY TRUANES
            $_SESSION['RegistroInSitu'] = 1;
            echo '
            <h2>Datos usuario y dirección de envío no encontrados, por favor indicar a continuación</h2>
            <br>
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
                <br>
                <div class="finForm">
                    <button type="button"><a href="../Views/Catalogo.php" class="btn btn-warning"><i class="lni lni-chevron-left"></i>Seguir navegando </a></button>
                    <button type="submit" class="submit-button"><span>Proceder al método de pago </span><i class="lni lni-chevron-right"></i></button> 
                    </form>
                </div>
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
        print_r($_SESSION);
        //todo si suben a session la seccion que estaba navegando podemos consultarla aquí para que cuando le dén a seguir navegando le siga listando articulos relevantes
        ?>

<?php
include_once("footer.php");
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
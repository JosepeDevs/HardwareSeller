<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//ESTA PÁGINA NO SE DEBE PROTEGER, ACCESIBLE A TODOS LOS NAVEGANTES
//HEADER Y TITULO
include_once("../Views/header.php");
echo'<h1>Datos de la dirección de envío</h1>';
print_r($_SESSION);
//RECIBIMOS POR POST LOS DATOS 
   
///////////////////QuÉ MOSTRAR SI ESTÁN REGISTRADOS///////////////////
    if(isset($_SESSION['user'])) {
        include_once('../Controllers/ClienteBUSCARController.php');
        $usuario = getClienteByEmail($_SESSION['user']);
        echo"
        <h2>Datos usuario y dirección de envío</h2>
        <br>
        <p>Nombre: ".$usuario->getNombre()."</p>
        <p>Email: ".$usuario->getEmail()."</p>
        <p>Teléfono: ".$usuario->getTelefono()."</p>
        <p>Dirección: ".$usuario->getDireccion()."</p>
        <p>Localidad: ".$usuario->getLocalidad()."</p>
        <p>Provincia: ".$usuario->getProvincia()."</p>
        ";
    } else{
////////////////////QUE MOSTRAR SI NO ESTAN REGISTRADOS////////////////////
        $_SESSION['RegistroDurantePedido'] = 1;
        $_SESSION["nuevoCliente"] = "true";
        echo '
        <h2>Datos de contacto y dirección de envío</h2>
        <br>
        <h3>Si ya tiene cuenta puede hacer login ahora para pasar a la selección del método de pago ↑↑↑</h3>
        <br>';
        if(isset($_SESSION['sinCuenta']) && $_SESSION['sinCuenta'] == true){
            echo'
            <h4>Ha seleccionado no crear cuenta con los datos que nos va a facilitar</h4>';
            unset($_SESSION['sinCuenta']);
        }
        echo'
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
                    <td><input type="text" name="nombre" id="nombre"  class="disabled-required" required><br><br></td>
                    <td><input type="text" name="direccion" id="direccion" class="disabled-required" required ><br><br></td>
                    <td><input type="text" name="localidad" id="localidad" class="disabled-required" required ><br><br></td>
                    <td><input type="text" name="provincia" id="provincia" class="disabled-required" required><br><br>
                    <td><input type="tel" name="telefono" id="telefono"  class="disabled-required" required><br><br>
                    <td><input type="email" name="email" id="email"  class="disabled-required" required><br><br>
                    <td><input type="text" name="dni" id="dni"  class="disabled-required" required pattern="^\d{8}\w{1}$"><br><br></td>
                    <td><input type="password" name="psswrd" id="pssword"  class="disabled-required" required><br><br>
                </tr>
            </table>
        ';
    }  
    //todo si suben a session la seccion que estaba navegando podemos consultarla aquí para que cuando le dén a seguir navegando le siga listando articulos relevantes

    ?>

<div class='finForm'>
        <br>
        <button type='button'><a href='../Views/Catalogo.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Seguir navegando</a></button>
        <button type='button'><a href='../Views/Carrito.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i>Volver a carrito</a></button>
        <button type='button'><a href='../Views/DireccionPedido.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i>Cambiar la dirección</a></button>
        <input id='Registrarse' style='display: block;' type='submit' value='Proceder al método de pago'>
</div> 

    </form> 
        <br>
        <?php
        //SECCION ERRORES EN EL ALTA DE USER
        include_once("../Controllers/ClienteALTAMensajes.php");
        $arrayMensajes=getArrayMensajesNuevo();
        if(is_array($arrayMensajes)){
            foreach($arrayMensajes as $mensaje) {
                echo "<h3>$mensaje</h3>";
            }
        };
        ?>
<?php
include_once("footer.php");
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
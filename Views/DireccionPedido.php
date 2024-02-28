<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//ESTA PÁGINA NO SE DEBE PROTEGER, ACCESIBLE A TODOS LOS NAVEGANTES

//HEADER Y TITULO
include_once("header.php");
echo'<h1>Dirección de envío</h1>';
//include_once("aside.php");

//RECIBIMOS POR POST LOS DATOS DE CONTENIDOPEDIDO
if(isset( $_POST['numLinea1'] )){
    //si llegamos del post vamos a subir a session todos los datos recibidos
foreach ($_POST as $AtributoYNumero => $valor) {
    // AtributoYNumero que empiecen  por alguna de estas palabras y terminar en un numero
    if (preg_match('/^(codigo|cantidad|precio|descuento)\d+$/', $AtributoYNumero)) { //así filtro dde todo lo que llega lo que quiero guardar
        $hayNumeros = preg_match('/(\d+)$/', $AtributoYNumero, $matches, PREG_OFFSET_CAPTURE); //mete en $matches si encuentra el regex, es un array multidimensional 
        $posicionNumeros=$matches[0][1];//$matches[0][0] es lo que ha encontrado que coincide con el regex, mientras que $matches[0][1] es donde lo ha encontrado
        if ($hayNumeros) {
            
            $atributo = substr($AtributoYNumero, 0, $posicionNumeros); //coge del principio hasta donde aparece el primer número, eso es el nombre del atributo

            $numLinea = intval(substr($AtributoYNumero, $posicionNumeros)); // en AtributoYNumero buscamos desde donde empiezan los números hasta el final (hacemos 0 offset cuando llegue al final)

            if (!isset($productosYCantidadesConfirmadas[$numLinea])) {//si no existe ek array de productos lo crea
                $productosYCantidadesConfirmadas[$numLinea] = array(); 
            } 

            $productosYCantidadesConfirmadas[$numLinea][$atributo] = $valor;  //metemos dentro de la respectiva numLinea los atributos codigo, descuento, precio y cantidad
        }
    }
}

$_SESSION['CarritoConfirmado'] = $productosYCantidadesConfirmadas; //guardamos los datos del carrito en la sesión para tenerlos a mano
unset($_SESSION["productos"]);//nos cargamos la versión simplificada que nos llegó inicialmente
}
?>
<form action="../Controllers/ValidarDatosCliente.php" method="post">
        <select class="estado-metodoPago" name="estadoEnvio" id="estadoEnvio-metodoEnvioInput">
            <option for="estadoEnvio" value="5">Pago y Recogida en tienda</option>
            <option for="estadoEnvio" value="tiendaSINcuenta">Pago y Recogida en tienda (no crear cuenta)</option>
            <option for="estadoEnvio" value="0">Envío a mi dirección (usar datos de mi área de cliente)</option>
            <option for="estadoEnvio" value="direccionYcuenta">Envío a mi dirección (crear cuenta)</option>
            <option for="estadoEnvio" value="direccionSINcuenta">Envío a mi dirección (NO crear cuenta)</option>
        </select>

<?
    if(isset($_SESSION['user'])) {
            include_once('../Controllers/ClienteBUSCARController.php');
            $usuario = getClienteByEmail($_SESSION['user']);
            //NOTHING LIKE A GOOD RETURNING CLIENT!
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
            //ESTABAN COMPRANDO SIN REGISTRARSE LOS MUY TRUANES
            $_SESSION['RegistroDurantePedido'] = 1;
            $_SESSION["nuevoCliente"] = "true";
            ?>
<div id="formularioEnvio" style="display: none;">
            <h2>Datos de contacto y dirección de envío</h2>
            <br>
            <p>Futura funcionalidad: OAth autenticación con un click.</p>
            <br>
            <br>
</div> <!--cerramos id="formularioEnvio"-->
<div id="ocultoSiTienenCuenta" style="display: none;">
            <form id="datosCliente" action="../Controllers/ValidarDatosCliente.php" method="post">
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
</div><!--cerramos ocultoSiTIenenCuenta-->
<div id="DivIngresar" style="display: none;">
                <form id="ingresar" action="/Controllers/conexion.php" method="post">
                                    <table class="tablaLogin">
                                    <caption><h2>Ingresar:</h2></caption>
                                    <tr>
                                        <th colspan="2">Usuario (email) :<br></td>
                                        <td><input type="text" name="user" placeholder="example@example.com"></td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Contraseña<br></th>
                                        <td><input type="password" name="key" placeholder="***********"></td>
                                    </tr>
                                    </table>
                                    <div id="IngresarYReiniciarHeader" class="finForm">
                                        <br><button id="ingresarBoton">Ingresar, usar mis datos de envío y proceder al método de pago</button>
                                    </div>
                                </form>
</div><!--cerramos DivIngresar-->
       <? }  
    //todo si suben a session la seccion que estaba navegando podemos consultarla aquí para que cuando le dén a seguir navegando le siga listando articulos relevantes

    ?>



<div class='finForm'>
        <br>
        <button type='button'><a href='../Views/Catalogo.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Seguir navegando</a></button>
        <button type='button'><a href='../Views/Carrito.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i>Volver a carrito</a></button>
        <button  id='Registrarse' style='display: block;' id="enviarFormularioBoton">Proceder al método de pago</button>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    //otros elementos
    var metodoEnvioInput = document.getElementById('estadoEnvio-metodoEnvioInput');
    var formularioEnvio = document.getElementById('formularioEnvio');
    var DivocultoSiTienenCuenta = document.getElementById('ocultoSiTienenCuenta');
    var DivIngresar = document.getElementById('DivIngresar');
    var datosClienteFormulario = document.getElementById('datosCliente');
    var ingresarFormulario = document.getElementById('ingresar');


    //botones
    var tiendaRegistrados = document.getElementById('tiendaRegistrados');
    var direccionRegistrados = document.getElementById('direccionRegistrados');
    var direccionSinRegistrase = document.getElementById('direccionSinRegistrase');
    var tiendaSinRegistrarse = document.getElementById('tiendaSinRegistrarse');
    var Registrarse = document.getElementById('Registrarse');

    function enviarFormulario(idFormulario) {
        document.getElementById(idFormulario).submit();
    }

    ingresarFormulario.addEventListener('click', function() {
        enviarFormulario("ingresar")
    })

    datosClienteFormulario.addEventListener('click', function() {
        enviarFormulario("datosCliente")
    })
    
    //primero hacemos visible el DIV que toque según dirección de envío
    metodoEnvioInput.addEventListener('change', function() {
        if (this.value == 'direccionYcuenta' || this.value == 'direccionSINcuenta') {
            formularioEnvio.style.display = 'block';
            DivocultoSiTienenCuenta.style.display = 'block';
            DivIngresar.style.display = 'none';
        }  else if(this.value == '0'){
            //ya tienen cuenta, no mostrar tabla de registrarse, mostrar tabla de ingresar
            formularioEnvio.style.display = 'block';
            DivocultoSiTienenCuenta.style.display = 'none';
            DivIngresar.style.display = 'block';
            Registrarse.style.display = 'none';
        }  else {
            //recogida en tienda
            formularioEnvio.style.display = 'none';
            DivocultoSiTienenCuenta.style.display = 'none';
            DivIngresar.style.display = 'none';
        }
    });
});
/*checkBox.addEventListener('change', function() {
        if (this.checked) {*/
</script>
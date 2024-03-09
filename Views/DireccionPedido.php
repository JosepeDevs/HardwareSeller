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
        <select class="estadoEnvio-metodoEnvioInput" name="estadoEnvio" id="estadoEnvio-metodoEnvioInput">
            <option for="estadoEnvio" value="5">Pago y Recogida en tienda</option>
            <option for="estadoEnvio" value="tiendaSINcuenta">Pago y Recogida en tienda (no crear cuenta)</option>
            <option for="estadoEnvio" value="0">Envío a mi dirección (usar datos de mi área de cliente)</option>
            <option for="estadoEnvio" value="direccionYcuenta">Envío a mi dirección (crear cuenta)</option>
            <option for="estadoEnvio" value="direccionSINcuenta">Envío a mi dirección (NO crear cuenta)</option>
        </select>

<div id="formularioEnvio" style="display: none;">


<?

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
            <p>Futura funcionalidad: OAth autenticación con un click.</p>
            <br>
            <a href="#">Esto en un futuro será un botón para hacer login/registrarse con google usando OAuth</a>
            <br>
            <div id="ocultoSiTienenCuenta" style="display: none;">
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
                        <td><input type="text" name="nombre" id="nombre"  class="disabled-required" disabled><br><br></td>
                        <td><input type="text" name="direccion" id="direccion" class="disabled-required" disabled ><br><br></td>
                        <td><input type="text" name="localidad" id="localidad" class="disabled-required" disabled ><br><br></td>
                        <td><input type="text" name="provincia" id="provincia" class="disabled-required" disabled><br><br>
                        <td><input type="tel" name="telefono" id="telefono"  class="disabled-required" disabled><br><br>
                        <td><input type="email" name="email" id="email"  class="disabled-required" disabled><br><br>
                        <td><input type="text" name="dni" id="dni"  class="disabled-required" disabled pattern="^\d{8}\w{1}$"><br><br></td>
                        <td><input type="password" name="psswrd" id="pssword"  class="disabled-required" disabled><br><br>
                    </tr>
                </table>
            </div>
            ';
        }  
    //todo si suben a session la seccion que estaba navegando podemos consultarla aquí para que cuando le dén a seguir navegando le siga listando articulos relevantes

    ?>
</div> <!--cerramos  id="formularioEnvio"-->



<div class='finForm'>
        <br>
        <button type='button'><a href='../Views/Catalogo.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Seguir navegando</a></button>
        <button type='button'><a href='../Views/Carrito.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i>Volver a carrito</a></button>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    //otros elementos
    var checkBox = document.getElementById('crearCuenta');
    var metodoEnvioInput = document.getElementById('estadoEnvio-metodoEnvioInput');
    var formularioEnvio = document.getElementById('formularioEnvio');// donde se ve la direccion ya existente o se da la opcion de registrarse 
    var DivdireccionRegistrados = document.getElementById('DivdireccionRegistrados');
    var DivdireccionSinRegistrase = document.getElementById('DivdireccionSinRegistrase');
    var DivRegistrarse = document.getElementById('DivRegistrarse');
    var DivtiendaSinRegistrarse = document.getElementById('DivtiendaSinRegistrarse');
    var DivtiendaRegistrados = document.getElementById('DivtiendaRegistrados');
    var DivocultoSiTienenCuenta = document.getElementById('ocultoSiTienenCuenta');
    
    //botones
    var tiendaRegistrados = document.getElementById('tiendaRegistrados');
    var direccionRegistrados = document.getElementById('direccionRegistrados');
    var direccionSinRegistrase = document.getElementById('direccionSinRegistrase');
    var tiendaSinRegistrarse = document.getElementById('tiendaSinRegistrarse');
    var Registrarse = document.getElementById('Registrarse');
    
    //primero hacemos visible el DIV que toque según dirección de envío
    metodoEnvioInput.addEventListener('change', function() {
        if (this.value == 'direccionSINcuenta') {
            //si eligen envío a direccion aparecerá el formulario
            formularioEnvio.style.display = 'block';
            ocultoSiTienenCuenta.style.display = 'block';
            HabilitarInputs() //si eleigieron envio a dirección sin crear cuenta deben estar activos los inputs
        }  else if (this.value == '0'){
            //eligieron envio a mi dirección
            formularioEnvio.style.display = 'block';
            ocultoSiTienenCuenta.style.display = 'none';
            DisableInputs() // si ya estan registrados los inputs deben estar disablesd para que no manden nulls 
        }else if(this.value == 'direccionYcuenta'){
            //envio a dirección y crear cuenta
            formularioEnvio.style.display = 'block';
            ocultoSiTienenCuenta.style.display = 'block';
            HabilitarInputs()
        }  else {
            //recogida en tienda
            formularioEnvio.style.display = 'none';
            ocultoSiTienenCuenta.style.display = 'none';
            DisableInputs() // si no es visible deben estar disabled para que no manden nulls 
        }
    });

    // CAMIBAMOS LOS DISABLED POR REQUIRED
    function HabilitarInputs(){
        document.querySelectorAll(".disabled-required").forEach(function(input) {//a cada uno de los elementos seleccionados aplicar↓
        input.removeAttribute("disabled"); //quitamos el disabled para que se envíe
        input.setAttribute("required", ""); //añadimos el required (nombre, valor) como queremos que solo ponga el atributo required sin ningun valor el segundo param es ""
        });
        console.log(document.querySelectorAll(".disabled-required"));
    }

    // CAMIBAMOS LOS REQUIRED POR DISABLED
        function DisableInputs(){
        document.querySelectorAll(".disabled-required").forEach(function(input) {//a cada uno de los elementos seleccionados aplicar↓
        input.removeAttribute("required"); //quitamos el required para que se envíe
        input.setAttribute("disabled", ""); //añadimos el required (nombre, valor) como queremos que solo ponga el atributo required sin ningun valor el segundo param es ""
        });
        console.log(document.querySelectorAll(".disabled-required"));       
    }

});

</script>
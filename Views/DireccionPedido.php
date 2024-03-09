<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//ESTA PÁGINA NO SE DEBE PROTEGER, ACCESIBLE A TODOS LOS NAVEGANTES
//HEADER Y TITULO
include_once("header.php");
echo'<h1>Dirección de envío</h1>';

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
            <option for="estadoEnvio" value="tiendaCONcuenta">Pago y Recogida en tienda</option>
            <option for="estadoEnvio" value="tiendaSINcuenta">Pago y Recogida en tienda (no crear cuenta)</option>
            <option for="estadoEnvio" value="DireccionAreaCliente">Envío a mi dirección (usar datos de mi área de cliente)</option>
            <option for="estadoEnvio" value="direccionYcuenta">Envío a mi dirección (crear cuenta)</option>
            <option for="estadoEnvio" value="direccionSINcuenta">Envío a mi dirección (NO crear cuenta)</option>
        </select>

    <div class='finForm'>
            <br>
            <button type='button'><a href='../Views/Catalogo.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Seguir navegando</a></button>
            <button type='button'><a href='../Views/Carrito.php' class='enlace-arriba-de-footer'><i class='lni lni-chevron-left'></i>Volver a carrito</a></button>
            <input id='Registrarse' style='display: block;' type='submit' value='Proceder al método de pago'>
    </div> 
</form> 
        <br>

<?php
include_once("footer.php");
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
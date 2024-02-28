<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//NO PROTEGER

include_once("header.php");

echo'<h1>Seleccione el método de pago</h1>';
//include_once("aside.php");

//por si estaban logeados pero eligieron recogida en tienda
$estadoEnvio = (isset($_SESSION['estadoEnvio']))? $_SESSION['estadoEnvio'] :null;
print_r($_SESSION);
print $estadoEnvio;
//transformamos lo que nos llega por POST en estadoEnvio en un número (estado)
if(trim($estadoEnvio) == "direccionSINcuenta"){
    $estadoEnvio = "0";
} else if(trim($estadoEnvio) == "tiendaSINcuenta") {
    $estadoEnvio = "5";
} else if(trim($estadoEnvio) == "direccionYcuenta"){
    $estadoEnvio = "0";
} else if(strpos($estadoEnvio,"0") !== false){
    $estadoEnvio = "0";
} else if(strpos($estadoEnvio,"5") !== false){
    $estadoEnvio = "5";
} else{
    $estadoEnvio = "9999";
} 

//subimmos a session el numero del estado correspondiente al envío
$_SESSION['estadoEnvio'] = $estadoEnvio;

?>
<form action="../Views/RevisionPedido.php" method="POST">
    <h2>
        <select class="estado-metodoPago" name="estado" id="estado">
            <option for="estado" value="3">Transferencia</option>
            <option for="estado"  value="4">Tarjeta</option>
            <option for="estado"  value="5">Pago en tienda</option>
        </select>
    </h2>
    <br><br>
    <div id="detallesTarjeta" style="display: none;">
        <br><br>
        <h3> Actualmente NO disponible, próximamente funcional, disculpe las molestias </h3>
        <label for="numeroTarjeta">Número de Tarjeta:</label>
        <input type="text" id="numeroTarjeta" name="numeroTarjeta" pattern="\d{16}" placeholder="0000111122223333" disabled>
        <br><br>
        <label for="caducidadTarjeta">Fecha de Expiración:</label>
        <input type="month" id="expiracionTarjeta" name="expiracionTarjeta" disabled>
        <br><br>
        <label for="cvv">CVV:</label>
        <input type="number" id="cvv" name="cvv" disabled min="100" max="999">
        <br><br>
    </div>
    <br><br>

    <div class='finForm'>
        <button type='button'><a href='../Views/Catalogo.php' class='btn btn-warning'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Seguir navegando</a></button>
        <button type='button'><a href='../Views/DireccionPedido.php' class='btn btn-warning'><i class='lni lni-chevron-left'></i>Volver a dirección de envío</a></button>
        <input type='submit' value="Guardar método de pago y revisión final del pedido"></input>
    </div> 
</form> 

<?php
include_once("footer.php");
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var metodoPago = document.getElementById('estado');
    var detallesTarjeta = document.getElementById('detallesTarjeta');

    metodoPago.addEventListener('change', function() {
        if (this.value === '4') {
            detallesTarjeta.style.display = 'block';
        } else {
            detallesTarjeta.style.display = 'none';
        }
    });
});
</script>

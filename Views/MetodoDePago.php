<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("header.php");

echo'<h1>Seleccione el método de pago</h1>';
include_once("aside.php");
?>

<form action="PedidoALTA.php" method="POST">
    <select name="estado" id="estado">
        <option value="3">Transferencia</option>
        <option value="4">Tarjeta</option>
    </select>
    <div id="detallesTarjeta" style="display: none;">
        <br>
        <summary>Detalles de la Tarjeta</summary>
        <label for="numeroTarjeta">Número de Tarjeta:</label>
        <br>
        <input type="text" id="numeroTarjeta" name="numeroTarjeta" required><br>
        <label for="caducidadTarjeta">Fecha de Expiración:</label>
        <br>
        <input type="month" id="expiracionTarjeta" name="expiracionTarjeta" required><br>
        <label for="cvv">CVV:</label>
        <input type="number" id="cvv" name="cvv" required min="100" max="999"><br>
    </div>
    <input type="submit" value="Guardar método de pago y confirmar pedido">
</form>


<?php
include_once("header.php");
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

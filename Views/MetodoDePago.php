<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("header.php");

echo'<h1>Seleccione el método de pago</h1>';
include_once("aside.php");
?>

<form action="../Controllers/ProcesarPedidoController.php" method="POST">
    <h2>
        <select class="estado-metodoPago" name="estado" id="estado">
            <option value="3">Transferencia</option>
            <option value="4">Tarjeta</option>
        </select>
    </h2>
    <br><br>
    <div id="detallesTarjeta" style="display: none;">
        <br><br>
        <label for="numeroTarjeta">Número de Tarjeta:</label>
        <input type="text" id="numeroTarjeta" name="numeroTarjeta" pattern="\d{16}" placeholder="0000111122223333" required>
        <br><br>
        <label for="caducidadTarjeta">Fecha de Expiración:</label>
        <input type="month" id="expiracionTarjeta" name="expiracionTarjeta" required >
        <br><br>
        <label for="cvv">CVV:</label>
        <input type="number" id="cvv" name="cvv" required min="100" max="999">
        <br><br>
    </div>
    <br><br>

    <div class='finForm'>
        <button type='button'><a href='../Views/Catalogo.php' class='btn btn-warning'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Seguir navegando</a></button>
        <button type='button'><a href='../Views/DireccionPedido.php' class='btn btn-warning'><i class='lni lni-chevron-left'></i>Volver a dirección de envío</a></button>
        <button type='submit'><a href='../Views/DireccionPedido.php' class='btn btn-warning'><i class='lni lni-chevron-left'></i>Guardar método de pago y confirmar pedido</a></button>
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

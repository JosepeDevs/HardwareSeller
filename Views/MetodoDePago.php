<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("header.php");

echo'<h1>Seleccione el método de pago</h1>';
include_once("aside.php");
?>

<form action="PedidoALTA.php" method="POST">
    <select name="estado">
        <option value="3">Transferencia</option>
        <summary><option value="4">Tarjeta</option></summary>
            <details>
                <label for="numeroTarjeta">Número de Tarjeta:</label>
                <input type="text" id="numeroTarjeta" name="numeroTarjeta" required><br>
                <label for="caducidadTarjeta">Fecha de Expiración:</label>
                <input type="month" id="expiracionTarjeta" name="expiracionTarjeta" required><br>
                <label for="cvv">CVV:</label>
                <input type="number" id="cvv" name="cvv" required min="100" max="999"><br>
            </details>
    </select>
    <input type="submit" value="Guardar método de pago y confirmar pedido">
</form>


<?php
include_once("header.php");
?>
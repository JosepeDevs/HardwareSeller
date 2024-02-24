<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("header.php");
?>
<h1>Pedido confirmado</h1>
<?php
include_once("aside.php");
?>
<br>
<h2>Datos del pedido</h2>
<br>
<?php
include_once("../Controllers/PedidoALTAController.php")
?>
<?php
include_once("footer.php");
?>
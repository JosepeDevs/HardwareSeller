<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
//HEADER Y TITULO
include_once("header.php");
print("<h1>Datos de contacto para pedido sin registro</h1><br>");
?>
<form action="../Controllers/ValidarDatosClienteMinimos.php" method="post">
    <table>
        <tr>
            <th><label for="nombre">Nombre:</label></th>
            <th><label for="telefono">telefono:</label></th>
            <th><label for="email">email:</label></th>
            <th><label for="dni">DNI:</label></th>
        </tr>
        <tr>
            <td><input type="text" name="nombre" id="nombre"  class="disabled-required" required><br><br></td>
            <td><input type="tel" name="telefono" id="telefono"  class="disabled-required" required><br><br>
            <td><input type="email" name="email" id="email"  class="disabled-required" required><br><br>
            <td><input type="text" name="dni" id="dni"  class="disabled-required" required pattern="^\d{8}\w{1}$"><br><br></td>
        </tr>
    </table>
<?




include_once("footer.php");
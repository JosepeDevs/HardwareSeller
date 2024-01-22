<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="UpdatePsswrd.php" method="POST">
        <label>Email:</label> <br><input type="email" name="mail"><br><br>
        <label>Incluya su DNI para validar su identidad: </label><br><input type="text" id="dni" name="dni"><br><br><br><br>
        <div class="finForm">
            <input type="submit" value="Submit">
            <input type="reset" value="Borrar">
        </div>
    </form>
    <br>
    <br>
    <h3> <p><a id='cerrar' href="index.php">Cancelar</a></p></h3>
    </body>
</html>
<?php
include_once("RecuperarPsswrdMensajes.php");
$arrayMensajes=getArrayMensajes();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};

?>
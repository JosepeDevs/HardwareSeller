<?php
include_once("header.php");
//NO PROTEGER
?>
    <form id="recuperarpsswrd" action="../Controllers/UpdatePsswrd.php" method="POST">
        <label>Email:</label> <br><input type="email" name="mail"><br><br>
        <label>Incluya su DNI para validar su identidad: </label><br><input type="text" id="dni" name="dni"><br><br><br><br>
        <div class="finForm">
            <input type="submit" value="Submit">
            <input type="reset" value="Borrar">
        </div>
    </form>
    <br>
    <br>
    <h3> <p><a id='cerrar' href="../index.php">Cancelar</a></p></h3>

<?php
include_once("../Controllers/RecuperarPsswrdMensajes.php");
$arrayMensajes=getArrayMensajes();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        print "<h3>$mensaje</h3>";
    }
};

include_once("footer.php");

?>
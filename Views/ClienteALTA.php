<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
//esta página no requiere validación de rol ni autenticación, nos da igual que quien sea acceda a esta página.

include("header.php");


$_SESSION["nuevoCliente"]="true";//ponemos esto a true para que cuando vaya a validar datos lo trate como un insert
$_SESSION['auth'] = isset($_GET['auth']) ? $_GET['auth'] : 'OK';
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;
unset($_SESSION['nombre']);
unset($_SESSION['direccion']);
unset($_SESSION['localidad']);
unset($_SESSION['provincia']);
unset($_SESSION['telefono']);
unset($_SESSION['email']);
unset($_SESSION['email']);
unset($_SESSION['psswrd']);
unset($_SESSION['dni']);
?>

    <h1>Nuevo Cliente</h1>
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
                <?php if($rol == "admin"){echo'<th><label for="rol">Rol (user/editor):</label></th>';}?>
            </tr>
            <td><input type="text" name="nombre" id="nombre" required><br><br></td>
            <td><input type="text" name="direccion" id="direccion" required ><br><br></td>
            <td><input type="text" name="localidad" id="localidad" required ><br><br></td>
            <td><input type="text" name="provincia" id="provincia" required><br><br>
            <td><input type="tel" name="telefono" id="telefono" required><br><br>
            <td><input type="email" name="email" id="email" required><br><br>
            <td><input type="text" name="dni" id="dni" required pattern="^\d{8}\w{1}$"><br><br></td>
            <td><input type="password" name="psswrd" id="pssword" required><br><br>
            <?php if($rol == "admin"){ echo "
                    <td>
                        <select id='rol' name='rol' required>
                            <option value='user'>User</option>
                            <option value='editor'>Editor</option>
                            <option value='admin'>Administrador</option>
                        </select>
                    </td>
                ";} ?>
        </table>
        <div class="finForm">
            <h2><input type="submit" value="Guardar"></h2><br><br><br>
            <h2><input type="reset" value="Reiniciar formulario"></h2>
        </div>
    </form>
<?php

include_once("../Controllers/ClienteALTAMensajes.php");
$arrayMensajes=getArrayMensajesNuevo();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};


//todo: poner que si intentan registrar un usuario, si este está desactivado que dé la opción de activvarlo en lugar de darlo de alta de nuevo.


if($rol == "admin"){
    echo"<button id='cerrar'><a href='TablaClientes.php'>Cancelar / volver a la tabla</a></button>";
}else{
    echo"<button id='cerrar'><a href='/index.php'>Volver al inicio</a></button>";
}

include("footer.php");
?>
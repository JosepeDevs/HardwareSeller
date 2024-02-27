<?php
include_once("header.php");
print"<h1>Área privada</h1>";
include_once("../Controllers/OperacionesSession.php");
$userExiste = UserEstablecido();

//NO PROTEGER, AQUÍ PUEDEN REGISTRARSE TAMBIÉN

if( !$userExiste){
    ?>
    <form action="/Controllers/conexion.php" method="post">
    <table class="tablaLogin">
    <caption><h2>Ingresar:</h2></caption>
    <tr>
        <th colspan="2">Usuario (email) :<br></td>
        <td><input type="text" name="user" placeholder="example@example.com"></td>
    </tr>
    <tr>
        <th colspan="2">Contraseña<br></th>
        <td><input type="password" name="key" placeholder="***********"></td>
    </tr>
    </table>
    <div id="IngresarYReiniciarHeader">
        <br> <input type="submit" value="Ingresar">
        <br> <input type="reset" value="Reiniciar">
    </div>
    </form>
    <div id="sinAcceso">
    <p><a href="/Views/ClienteALTA.php?auth=temp">Registrar nuevo usuario</a></p>
    <p><a href="/Views/RecuperarPsswrd.php">Recuperar contraseña</a></p>
    </div>

    <?php
} else{
    print"<h2>Bienvenido ".$_SESSION['user']."</h2>";
    $esEditor = AuthYRolEditor();
    $esAdmin = AuthYRolAdmin();
    $esEmpleado = AuthYRolEmpleado();
    if($esAdmin){
        print ('
        <li><a href="/Views/ClienteEDITAR.php">Ver/editar mis datos</a></li>
        <li><a href="/Views/PedidosLISTAR.php">Administrar PEDIDOS</a></li>
        <li><a href="/Views/TablaClientes.php">Administrar CLIENTES</a></li>
        <li><a href="/Views/ArticulosLISTAR.php">Administrar ARTÍCULOS</a></li>
        <li><a href="/Views/CategoriasLISTAR.php">Administrar CATEGORÍAS</a></li>
        <li><a href="/Views/CategoriasLISTAR.php">Ver/generar informes</a></li>
        <li><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></li>');
    } else if ($esEditor){
        print ('
        <li><a href="/Views/ClienteEDITAR.php?dni='.$dni.'">Ver/editar mis datos</a></li>
        <li><a href="/Views/ArticulosLISTAR.php">Administrar ARTÍCULOS</a></li>
        <li><a href="/Views/CategoriasLISTAR.php">Administrar CATEGORÍAS</a></li>
        <li><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></li>');
    } else if($esEmpleado){
        print ('
        <li><a href="/Views/PedidosLISTAR.php">Administrar PEDIDOS</a></li>
        <li><a href="/Views/ArticulosLISTAR.php">Administrar ARTÍCULOS</a></li>
        <li><a href="/Views/CategoriasLISTAR.php">Administrar CATEGORÍAS</a></li>
        <li><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></li>');
    } else{
        print ('
        <li><a href="/Views/AreaCliente.php">Acceder al área personal</a></li>
        <li><a href="/Views/ClienteEDITAR.php">Ver/editar mis datos</a></li>
        <li><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></li>');
    }
}

include_once("footer.php");
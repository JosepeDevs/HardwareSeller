<?php
include_once("header.php");
print"<h1>Área privada</h1>";
include_once("../Controllers/OperacionesSession.php");
$userExiste = UserEstablecido();
//comento resetear session porque ahora pasamos por aquí en parte del proceso de compra y necesito mantener los datos de session
//ResetearSesion(); // para que después de estar haciendo operaciones como les da la opción de venir aquí la session se limpie y no cause comportamientos no deseados
//NO PROTEGER, AQUÍ PUEDEN REGISTRARSE TAMBIÉN
////print_r($_SESSION);;

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
        <table class="table table-bordered">
            <tr>
                <td><a href="/Views/ClienteEDITAR.php">Ver/editar mis datos</a></td>
                <td><a href="/Views/PedidosLISTAR.php">Administrar PEDIDOS</a></td>
                <td><a href="/Views/TablaClientes.php">Administrar CLIENTES</a></td>
            </tr>
            <tr>
                <td><a href="/Views/ArticulosLISTAR.php">Administrar ARTÍCULOS</a></td>
                <td><a href="/Views/CategoriasLISTAR.php">Administrar CATEGORÍAS</a></td>
                <td><a href="/Views/InformesLISTAR.php">Ver/generar informes</a></td>
            </tr>
            <tr>
                <td><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></td>
            </tr>
        </table>
        ');
    } else if ($esEditor){
        print ('
        <table class="table table-bordered">
            <tr>
                <td><a href="/Views/ClienteEDITAR.php?dni='.$dni.'">Ver/editar mis datos</a></td>
                <td><a href="/Views/ArticulosLISTAR.php">Administrar ARTÍCULOS</a></td>
            </tr>
            <tr>
                <td><a href="/Views/CategoriasLISTAR.php">Administrar CATEGORÍAS</a></td>
                <td><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></td>
            </tr>
        </table>    
                ');
    } else if($esEmpleado){
        print ('
        <table class="table table-bordered">
            <tr>
                <td><a href="/Views/PedidosLISTAR.php">Administrar PEDIDOS</a></td>
                <td><a href="/Views/ArticulosLISTAR.php">Administrar ARTÍCULOS</a></td>
            </tr>
            <tr>
                <td><a href="/Views/CategoriasLISTAR.php">Administrar CATEGORÍAS</a></td>
                <td><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></td>
            </tr>
        </table>
        ');
    } else{
        print ('
        <table class="table table-bordered">
            <tr>
                <td><a href="/Views/PedidosLISTAR.php">Ver mis pedidos</a></td>
                <td><a href="/Views/ClienteEDITAR.php">Ver/editar mis datos</a></td>
                <td><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></td>
            </tr>
        </table>
        ');
    }
}



include_once("footer.php");
<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

include_once("../Controllers/OperacionesSession.php");
$rolEsAdmin = AuthYRolAdmin();
if(!$rolEsAdmin) {
    session_destroy();
    print "PedidoVALIDAR dice: no está user en session";
    header("Location: /index.php");
}

include_once("../Controllers/GetDniByEmailController.php");
$dni=GetDniByEmail($_SESSION['user']);//acabamos de comprobar que sea admin asíque este será el dni de un admin, así si despedimos un admin con cambiar en la BBDD a user ya no podrá acceder a los informes

include_once("header.php");
print"<h1>Informes desempeño HardWare Seller</h1>";

print"<br><h2><a href='InformesLISTAR.php'>Recargar página</a></h2><br>";
include_once("../Controllers/InformesLISTARController.php");
?>
<table class='table table-bordered'>
    <tr>
        <td class="container-fluid">
            <button type='button' class='btn btn-secondary'><a href='InformesLISTAR.php?EstadisticasUsuariosWeb=1'>Generar enlace para descargar informe Clientes</button>
        </td>
    </tr>
    <tr>
        <td class="container-fluid">
            <button type='button' class='btn btn-secondary'><a href='InformesLISTAR.php?EstadisticasArticulosWeb=1'>Generar enlace para descargar informe Artículos</button>
        </td>
    </tr>
    <tr>
        <td class="container-fluid">
            <button type='button' class='btn btn-secondary'><a href='InformesLISTAR.php?EstadisticasPedidosWeb=1'>Generar enlace para descargar informe Pedidos</button>
        </td>
    </tr>
</table>

<br><br>

<form action="InformesLISTAR.php" method="POST">
    <table>
        <tr>
            <th>
                <h2><label for="fecha">Fecha inicio</label></h2>
            </th>
            <th>
                <h2><label for="fecha">Fecha fin</label></h2>
            </th>
        </tr>
        <tr>
            <td><input type="date" name="fechaInicio" autofocus><br><br></td>
            <td><input type="date" name="fechaFin" ><br><br></td>
        </tr>

        </td></tr>
    </table>

    <div>
        <h2><input type="submit" value="Generar enlace para descargar informe Pedidos"></h2><br><br><br>
    </div>
</form>

<?php
print($_POST['fechaInicio']);
print($_POST['fechaFin']);
print_r($_POST);
$nombreInforme=false;
       if( isset( $_GET["EstadisticasUsuariosWeb"] ) && $_GET["EstadisticasUsuariosWeb"] == 1 )  {
    $nombreInforme= EstadisticasUsuariosWeb($dni);
} else if( isset( $_GET["EstadisticasArticulosWeb"] ) && $_GET["EstadisticasArticulosWeb"] == 1 )  {
    $nombreInforme= EstadisticasArticulosWeb($dni);
} else if( isset( $_GET["EstadisticasPedidosWeb"] ) && $_GET["EstadisticasPedidosWeb"] == 1 )  {
    $nombreInforme= EstadisticasPedidosWeb($dni);
} else if( isset( $_POST["fechaInicio"] ) && !empty($_POST['fechaInicio']) && isset($_POST["fechaFin"]) && !empty($_POST['fechaFin']) )  {
    print"entramos";
    $fechaInicio = !empty($_POST["fechaInicio"]) ? $_POST['fechaInicio'] : null ; 
    $fechaFin = !empty($_POST["fechaFin"]) ?  $_POST['fechaFin'] : null ; 
    $nombreInforme= EstadisticasPedidosRangoFechas($dni,$fechaInicio, $fechaFin);
}
if($nombreInforme !== false){
    print(" <div class='container-fluid'>
       <button class='btn btn-secondary'><a href='download.php?informe=".urlencode($nombreInforme)."'>Descargar informe</a></button>
    </div>");//urlencode para no mandar caracteres no compatibles por la url
}

include_once("footer.php");
?>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
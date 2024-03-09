<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

include_once("../Controllers/OperacionesSession.php");
$rolEsAdmin = AuthYRolAdmin();
if(!$rolEsAdmin) {
    session_destroy();
    print "PedidoVALIDAR dice: no está user en session";
    header("Location: /index.php");
    exit;

}

include_once("../Controllers/GetDniByEmailController.php");
$dni=GetDniByEmail($_SESSION['user']);//acabamos de comprobar que sea admin asíque este será el dni de un admin, así si despedimos un admin con cambiar en la BBDD a user ya no podrá acceder a los informes

include_once("header.php");
print"<h1>Informes desempeño HardWare Seller</h1>";

print"<br><h2><a href='InformesLISTAR.php'>Reiniciar página</a></h2><br>";
include_once("../Controllers/InformesLISTARController.php");

$arrayInforme=false;
if( isset( $_GET["EstadisticasUsuariosWeb"] ) && $_GET["EstadisticasUsuariosWeb"] == 1 )  {
    $arrayInforme= EstadisticasUsuariosWeb($dni);

} else if( isset( $_GET["EstadisticasArticulosWeb"] ) && $_GET["EstadisticasArticulosWeb"] == 1 )  {
    $arrayInforme= EstadisticasArticulosWeb($dni);

} else if( isset( $_GET["EstadisticasPedidosWeb"] ) && $_GET["EstadisticasPedidosWeb"] == 1 )  {
    $arrayInforme= EstadisticasPedidosWeb($dni);

} else if( isset( $_POST["fechaInicio"] ) && !empty($_POST['fechaInicio']) && isset($_POST["fechaFin"]) && !empty($_POST['fechaFin']) )  {
    $fechaInicio = !empty($_POST["fechaInicio"]) ? $_POST['fechaInicio'] : null ; 
    $fechaFin = !empty($_POST["fechaFin"]) ?  $_POST['fechaFin'] : null ; 
    $arrayInforme= EstadisticasPedidosRangoFechas($dni,$fechaInicio, $fechaFin);

}
if($arrayInforme !== false){//urlencode para no mandar caracteres no compatibles por la url, mostramos por pantalla también el contenido del informe
    print(" <div class='container-fluid'>
        <button class='btn btn-secondary'><a href='download.php?informe=".urlencode($arrayInforme[0])."'>Descargar informe</a></button>
        <br><br>
        <table><tr><td>
        <h3 class='display-1'>".$arrayInforme[1]."</h3>
        </td></tr></table><br><br>
        </div>");
}

?>
<table class='table table-bordered'>
    <tr>
        <td class="container-fluid">
            <button type='button' class='btn btn-secondary'><a href='InformesLISTAR.php?EstadisticasUsuariosWeb=1'>Generar enlace para descargar y mostrar informe Clientes</button>
        </td>
    </tr>
    <tr>
        <td class="container-fluid">
            <button type='button' class='btn btn-secondary'><a href='InformesLISTAR.php?EstadisticasArticulosWeb=1'>Generar enlace para descargar y mostrar informe Artículos</button>
        </td>
    </tr>
    <tr>
        <td class="container-fluid">
            <button type='button' class='btn btn-secondary'><a href='InformesLISTAR.php?EstadisticasPedidosWeb=1'>Generar enlace para descargar y mostrar  informe Pedidos</button>
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
        <h2><input type="submit" value="Generar enlace para descargar y mostrar informe Pedidos"></h2><br><br><br>
    </div>
</form>

<?php



include_once("footer.php");
?>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
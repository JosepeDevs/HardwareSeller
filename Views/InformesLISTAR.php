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

include_once("../Controllers/InformesLISTARController.php");

print"<table class='table table-bordered'>";
    print'<tr><td class="container-fluid">';
    print("<button class='btn btn-secondary'><a href='InformesLISTAR.php?EstadisticasUsuariosWeb=1'>Generar informe Clientes</button>");//así solo pueden llamar a la función los que tengan rol de admin y no escribirmos en la url rol=admin que eso es muy obvio
    if( isset( $_GET["EstadisticasUsuariosWeb"] ) && $_GET["EstadisticasUsuariosWeb"] == 1 )  {
            $textoGenerado = EstadisticasUsuariosWeb($dni);//con llamar al método se debería descargar
            print "<tr><td>Guarde como PDF esta página para disponer del informe</td></tr>" ;
            print $textoGenerado;
            print "</td></tr>" ;
        }
    print"</td></tr>";

    print'<tr><td class="container-fluid">';
        print("<button class='btn btn-secondary'><a href='InformesLISTAR.php?EstadisticasArticulosWeb=1'>Generar informe Artículos</button>");//así solo pueden llamar a la función los que tengan rol de admin y no escribirmos en la url rol=admin que eso es muy obvio
        if( isset( $_GET["EstadisticasArticulosWeb"] ) && $_GET["EstadisticasArticulosWeb"] == 1 )  {
            $textoGenerado = EstadisticasArticulosWeb($dni);//con llamar al método se debería descargar
            print "<tr><td>Guarde como PDF esta página para disponer del informe</td></tr>" ;
            print $textoGenerado;
            print "</td></tr>" ;
        }
    print"</td></tr>";

    print'<tr><td class="container-fluid">';
    print("<button class='btn btn-secondary'><a href='InformesLISTAR.php?EstadisticasPedidosWeb=1'>Generar informe Pedidos</button>");//así solo pueden llamar a la función los que tengan rol de admin y no escribirmos en la url rol=admin que eso es muy obvio
    if( isset( $_GET["EstadisticasPedidosWeb"] ) && $_GET["EstadisticasPedidosWeb"] == 1 )  {
            $textoGenerado = EstadisticasPedidosWeb($dni);//con llamar al método se debería descargar
            print "<tr><td>Guarde como PDF esta página para disponer del informe</td></tr>" ;
            print $textoGenerado;
            print "</td></tr>" ;
        }
    print'</td></tr>';
print'</table>

<form action="InformesLISTAR.php" method="POST">
    <table>
        <tr>
            <th><h2><label for="fecha">Fecha inicio</label></h2></th>
            <th><h2><label for="fecha">Fecha fin</label></h2></th>
        </tr>
        <tr>
            <td><input type="date" name="fechaInicio" autofocus><br><br></td>
            <td><input type="date" name="fechaFin" ><br><br></td>
        </tr>

        <tr><td class="container-fluid">';
        print("<button class='btn btn-secondary'><a href='InformesLISTAR.php?EstadisticasPedidosWeb=1'>Generar informe Pedidos</button>");//así solo pueden llamar a la función los que tengan rol de admin y no escribirmos en la url rol=admin que eso es muy obvio
        if( isset( $_POST["FechaInicio"] ) && isset($_POST["FechaFin"]) )  {

            $fechaInicio = !empty($_REQUEST["fechaInicio"]) ? $_REQUEST['fechaInicio'] : null ; 
            $fechaFin = !empty($_REQUEST["fechaFin"]) ?  $_REQUEST['fechaFin'] : null ; 
            $arrayPedido = GetPedidosByRangoFecha($fechaInicio,$fechaFin);

                $textoGenerado = EstadisticasPedidosRangoFechas($dni, $fechaInicio, $fechaFin);
                print "<tr><td>Guarde como PDF esta página para disponer del informe</td></tr>" ;
                print $textoGenerado;
                print "</td></tr>" ;
            } else{
                print "<tr><td>" ;
                print "Necesita especificar una fecha de inicio y una fecha de fin." ;
                print "</td></tr>" ;
            }
        print'</td></tr>
    </table>

    <div>
        <h2><input type="submit" value="Consultar"></h2><br><br><br>
    </div>
</form>
';
include_once("footer.php");


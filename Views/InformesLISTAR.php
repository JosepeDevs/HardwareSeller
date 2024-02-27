<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

include_once("../Controllers/OperacionesSession.php");
$rolEsAdmin = AuthYRolAdmin();
if(!$rolEsAdmin) {
    session_destroy();
    echo "PedidoVALIDAR dice: no está user en session";
    header("Location: /index.php");
}

include_once("../Controllers/GetDniByEmailController.php");
$dni=GetDniByEmail($_SESSION['user']);//acabamos de comprobar que sea admin asíque este será el dni de un admin, así si despedimos un admin con cambiar en la BBDD a user ya no podrá acceder a los informes

include_once("header.php");
print"<h1>Informes desempeño HardWare Seller</h1>";

include_once("../Controllers/InformesLISTARController.php");

print"<table  class='table table-bordered'>";
    print"<tr>";
        print("<button class='btn btn-secondary'><a href='InformesLISTAR.php??EstadisticasUsuariosWeb=1</button>");//así solo pueden llamar a la función los que tengan rol de admin y no escribirmos en la url rol=admin que eso es muy obvio
        if( isset( $_GET["EstadisticasUsuariosWeb"] ) && $_GET["EstadisticasUsuariosWeb"] == 1 )  {
            $textoGenerado = EstadisticasUsuariosWeb($dni);//con llamar al método se debería descargar
        }
    print"</tr>";

    print"<tr>";
        print("<button class='btn btn-secondary'><a href='InformesLISTAR.php??EstadisticasArticulosWeb=1</button>");//así solo pueden llamar a la función los que tengan rol de admin y no escribirmos en la url rol=admin que eso es muy obvio
        if( isset( $_GET["EstadisticasArticulosWeb"] ) && $_GET["EstadisticasArticulosWeb"] == 1 )  {
            $textoGenerado = EstadisticasArticulosWeb($dni);//con llamar al método se debería descargar
        }
    print"</tr>";

    echo'<tr>';
        print("<button class='btn btn-secondary'><a href='InformesLISTAR.php??EstadisticasPedidosWeb=1</button>");//así solo pueden llamar a la función los que tengan rol de admin y no escribirmos en la url rol=admin que eso es muy obvio
        if( isset( $_GET["EstadisticasPedidosWeb"] ) && $_GET["EstadisticasPedidosWeb"] == 1 )  {
            $textoGenerado = EstadisticasPedidosWeb($dni);//con llamar al método se debería descargar
        }
    echo'</tr>';

print"</table>";

include_once("footer.php");


//todo wrappear esto en función y llamar a CLASE Informes (también todo)
if(
    isset( $_GET["EstadisticasUsuariosWeb"] ) &&
    $_GET["EstadisticasUsuariosWeb"] == 1  &&
    isset( $_GET["dni"] ) 
) {

    $dni=$_GET['dni'];
    $textoGenerado = EstadisticasUsuariosWeb($dni);//con llamar al método se debería descargar
}



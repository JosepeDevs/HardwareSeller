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
        print("<a href='../Controllers/InformesLISTARController.php?EstadisticasUsuariosWeb=1&dni=$dni");//así solo pueden llamar a la función los que tengan rol de admin y no escribirmos en la url rol=admin que eso es muy obvio
        $textoEnInforme = EstadisticasUsuariosWeb($dni);//esto también lo descarga
        print($textoEnInforme);
    print"</tr>";

    print"<tr>";
        print("<a href='../Controllers/InformesLISTARController.php?EstadisticasArticulosWeb=1&dni=$dni");//así solo pueden llamar a la función los que tengan rol de admin y no escribirmos en la url rol=admin que eso es muy obvio
        $textoEnInforme = EstadisticasArticulosWeb($dni);//esto también lo descarga
        print($textoEnInforme);
    print"</tr>";

    echo'<tr>';
        print("<a href='../Controllers/InformesLISTARController.php?EstadisticasArticulosWeb=1&dni=$dni");//así solo pueden llamar a la función los que tengan rol de admin y no escribirmos en la url rol=admin que eso es muy obvio
        $textoEnInforme = EstadisticasPedidosWeb($dni);//esto también lo descarga
        print($textoEnInforme);
    echo'</tr>';

print"</table>";

include_once("footer.php");
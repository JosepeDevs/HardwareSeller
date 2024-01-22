<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "GetEmailByDniController.php dice: no está user en session";
    header("Location: /../Views/index.php");
    exit;
}

function GetDniByEmail($email){
    include_once("/../Models/Cliente.php");
    $dni = Cliente::GetDnibyEmail($email);
    return $dni;
}
?>

?>
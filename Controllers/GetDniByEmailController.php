<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

///no proteger no borrar, necesario para clientes nuevos
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "GetEmailByDniController.php dice: no está user en session";
    header("Location: /../Views/index.php");
    exit;
}

function GetDniByEmail($email){
    include_once("/../Models/Cliente.php");
    $cliente = Cliente::GetDniByEmail($email);
    $dni = $cliente->getDni();
    return $dni;
}
?>
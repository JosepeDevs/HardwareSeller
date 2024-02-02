<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

///no proteger no borrar, necesario para clientes nuevos
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "GetEmailByDniController.php dice: no está user en session";
    header("Location: ../Views/index.php");
    exit;
}

function GetDniByEmail($email){
    $raiz= dirname(__DIR__);
    $ruta = $raiz.'/Models/Cliente.php';
    include_once("$ruta");
    $dni = Cliente::GetDniByEmail($email);
    return $dni;
}
?>
<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

///no proteger no borrar, necesario para clientes nuevos, necesario también ppara busacr artiuclos

function GetDniByEmail($email){
    $raiz= dirname(__DIR__);
    $ruta = $raiz.'/Models/Cliente.php';
    include_once("$ruta");
    $dni = Cliente::GetDniByEmail($email);
    return $dni;
}
?>
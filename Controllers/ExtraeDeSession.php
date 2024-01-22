<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ExtraeDeSession dice:  no está user en session";
    header("Location: index.php");
}

Function GetRolDeSession(){
    if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
    if(isset($_SESSION['rol'])) {
        $rol = $_SESSION['rol'];
        return $rol;
    }
}

Function GetEmailDeSession(){
    if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
    if(isset($_SESSION['user'])) {
        $email = $_SESSION['user'];
        return $email;
    }
}
?>
<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "CheckRol dice: no está user en session ";
    header("Location: index.php");
}


/**
 * funcion para poner al principio de las paginas php que solo los admin pueden ver
 * @return boolean true si session auth=OK y rol= admin, en cualquier otro caso devuelve false.
 *
 */
function AuthYRolAdmin(){
    if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
    if(isset($_SESSION['rol']) && $_SESSION['rol'] == "admin" && isset($_SESSION['auth']) && $_SESSION['auth'] == "OK") {
        return true;
    } else {
        return false;
    }
}
function AuthYRolEditor(){
    if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
    if(isset($_SESSION['rol']) && $_SESSION['rol'] == "editor" && isset($_SESSION['auth']) && $_SESSION['auth'] == "OK") {
        return true;
    } else {
        return false;
    }
}

/**
 * @return boolean true si auth = OK  tanto admin como usuarios normales pueden ver estas páginas
 *
 */
function comprobarAuth(){
    if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
    if( isset($_SESSION['auth']) && $_SESSION['auth'] == "OK") {
        return true;
    } else {
        return false;
    }
}

?>
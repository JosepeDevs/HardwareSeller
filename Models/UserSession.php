<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "UserSession dice: shit no está user en session";
    //header("Location: index.php");
}

/**
 * @return bool Devuelve TRUE si existe user en session (solo ocurre si la contraseña era correcta). Devuelve FALSE si no encuentr
 *
 */
Function UserEstablecido(){
    if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
    $existeUsuario = isset($_SESSION['user']) ? $_SESSION['user']: null;
    if( $existeUsuario ==null ){
        return false;
    } else {
        return true;
    }
}

?>
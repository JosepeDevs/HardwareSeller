<?php
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ResetSession dice: shit no está user en session";
    header("Location: index.php");
}

//no entiendo porque esta función si la intento proteger no puedo acceder.
/**
 * Para llamarla después de imprimir mensajes de error/confirmación, ya que solo guarda user, Auth y rol, el resto lo deja UNSET.
 *
 */
Function ResetSession(){
    if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
    $user= isset($_SESSION['user']) ? $_SESSION['user'] : null;
    $usuario= isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
    $rol= isset($_SESSION['rol']) ? $_SESSION['rol'] : null;
    $Auth= isset($_SESSION['auth']) ? $_SESSION['auth'] : null;
    session_unset();
    $_SESSION['user'] = $user;
    $_SESSION['usuario'] = $usuario;
    $_SESSION['rol'] = $rol;
    $_SESSION['auth'] = $Auth;
}

?>
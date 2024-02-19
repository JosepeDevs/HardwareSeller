
<?php
//antes esto era usersession
//si protejo esto validarclientes no funciona y el alta de clientes no va

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


//esto antes era checkrol

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


//antes esto era extraedesession

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

/**
 * Para llamarla después de imprimir mensajes de error/confirmación, ya que solo guarda user, Auth y rol, el resto lo deja UNSET.
 *
 */
Function ResetearSesion(){
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
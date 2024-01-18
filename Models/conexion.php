<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

//NO PROTEGER ESTO, ES DONDE SE SUBE A SESSION USER Y KEY
include_once("GetClienteBBDD.php");
include_once("conectarBD.php");

/**
 * Requiere usarse esta función en páginas que reciben un POST de "user" y "key".
 * @return void|bool Devuelve TRUE Y ESTABLECE "user" y "key" en SESSION si para el email del cliente la contraseña posteada y la hasheada en la BBDD coincide. Si no lo consigue devuelve FALSE.
 *
 */

if(isset($_POST['user']) &&  isset($_POST['key'])) {
    $usuario = $_POST['user'];
    $psswrdSinHashear = $_POST['key'];
    $cliente= GetClientePorEmail($usuario);
    if($cliente == false) {
        $_SESSION['NoExiste']=true;
    }
    $psswrdHasheada =$cliente->getPsswrd();
    $psswrdExiste = password_verify($psswrdSinHashear, $psswrdHasheada);
    if( $psswrdExiste) {
        echo "all good";
        $_SESSION['user']=$usuario;
        $_SESSION['key'] = $psswrdSinHashear;
        print_r($_SESSION);
        echo "<script>history.back();</script>";
        exit;
    } else {
        echo "bad psswrd";
        $_SESSION['BadPsswrd'] = true;
    }
}

if( (isset($_SESSION['BadPsswrd']) && $_SESSION['BadPsswrd'] !== true) ||
  ( (isset($_SESSION['NoExiste']) && $_SESSION['NoExiste'] !== true) )){
    $_SESSION['UserNoSession'] = true;
    header("Location: index.php");
    exit;
}


?>
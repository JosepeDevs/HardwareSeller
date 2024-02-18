<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

//NO PROTEGER ESTO, ES DONDE SE SUBE A SESSION USER Y KEY Y ROL
include_once("../Models/Cliente.php");


/**
 * Requiere usarse esta función en páginas que reciben un POST de "user" y "key".
 * @return void|bool Devuelve TRUE Y ESTABLECE "user" y "key" en SESSION si para el email del cliente la contraseña posteada y la hasheada en la BBDD coincide. Si no lo consigue devuelve FALSE.
 *
 */
if(isset($_POST['user']) &&  isset($_POST['key'])) {
    $usuario = $_POST['user'];
    $cliente = Cliente::GetClientByEmail($usuario);
    if($cliente !== false){
        $rol = $cliente->getRol();
    } else{
        header("Location:/index.php");
    }
    $psswrdSinHashear = $_POST['key'];
    if($cliente == false) {
        $_SESSION['NoExiste']=true;
        echo"<p>cliente no existe</p>";
    } else{
        $psswrdHasheada =$cliente->getPsswrd();
        echo"$psswrdHasheada";
        $psswrdExiste = password_verify($psswrdSinHashear, $psswrdHasheada);
        if( $psswrdExiste) {
            echo "all good";
            $_SESSION['user']=$usuario;
            $_SESSION['key'] = $psswrdHasheada;
            $_SESSION['usuario']=$usuario; //no es lo mismo que session de user
            $_SESSION['auth'] = "OK";
            $_SESSION['rol'] = $rol;
            print_r($_SESSION);
            echo "<script>history.back();</script>";
            exit;
        } else {
            echo "bad psswrd";
            $_SESSION['BadPsswrd'] = true;
        }
    }
}

if( (isset($_SESSION['BadPsswrd']) && $_SESSION['BadPsswrd'] == true) ||
( (isset($_SESSION['NoExiste']) && $_SESSION['NoExiste'] == true) )){
    $_SESSION['UserNoSession'] = true;
    echo "hubo un error";
    header("Location: ../index.php");
    exit;
}


?>
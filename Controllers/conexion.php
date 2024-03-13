<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

//NO PROTEGER ESTO, ES DONDE SE SUBE A SESSION USER Y KEY Y ROL
include_once("../Models/Cliente.php");

//////print_r($_SESSION);
/**
 * Requiere usarse esta función en páginas que reciben un POST de "user" y "key".
 * @return void|bool Devuelve TRUE Y ESTABLECE "user" y "key" en SESSION si para el email del cliente la contraseña posteada y la hasheada en la BBDD coincide. Si no lo consigue devuelve FALSE.
 *
 */
///////////////////CONEXIÓN SI LOS DATOS LLEGAN POR SESSION
if( 
    isset($_SESSION["RegistroDurantePedido"]) && $_SESSION["RegistroDurantePedido"] == 1 && 
    isset($_SESSION['email']) &&  isset($_SESSION['psswrdSinHash']) 
){
    //entramos aquí si se están registrando en el carrito
    $email = $_SESSION['email'];
    //  print'entramos en registrodurante pedido';
    $cliente = Cliente::GetClientByEmail($email);
    if($cliente !== false){
        $rol = $cliente->getRol();
        $activo = $cliente->getActivo();
    } else{
        $_SESSION['NoExiste']=true;
        header("Location:/index.php");
        exit;
    }
    if( $activo == 0){
        //si la cuenta está desactivada no les dejamos hacer login
        $_SESSION['CuentaDesactivada']=true;
        header("Location:/index.php");
        exit;
    }

    $psswrdSinHashear = $_SESSION['psswrdSinHash'];
    //print"<br>psswrd SIN Hasheada=".$psswrdSinHashear;
    if($cliente == false) {
        $_SESSION['NoExiste']=true;
        //   print"<p>cliente no existe</p>";
    } else{
        $psswrdHasheada =$cliente->getPsswrd();
    //   print"<br>psswrdHaseada=".$psswrdHasheada;
        $psswrdExiste = password_verify($psswrdSinHashear, $psswrdHasheada);
        if( $psswrdExiste) {
            //  print "all good";
            $_SESSION['user']=$email;
            $_SESSION['key'] = $psswrdHasheada;
            $_SESSION['usuario']=$email; //no es lo mismo que session de user
            $_SESSION['auth'] = "OK";
            $_SESSION['rol'] = $rol;
            ////////print_r($_SESSION);;
            if( ( isset($_SESSION["RegistroDurantePedido"]) && $_SESSION["RegistroDurantePedido"] == 1 ) ||
                ( isset($_SESSION["CarritoConfirmado"]) && !empty($_SESSION["CarritoConfirmado"])) 
            ){
                //print'vamos a medoto de pago';
                header('Location: ../Views/MetodoDePago.php');
                exit;
            } else{
                //  print'vamos patras';
                print "<script>history.back();</script>";
                exit;
            }
        } else {
            //print "<br>bad psswrd";
            $_SESSION['BadPsswrd'] = true;
        }
    }
}

//////////////CONEXION SI LOS DATOS LLEGAN POR POST
    if(isset($_POST['user']) &&  isset($_POST['key'])) {
    $usuario = $_POST['user'];
    $cliente = Cliente::GetClientByEmail($usuario);
    if($cliente !== false){
        $rol = $cliente->getRol();
        $activo = $cliente->getActivo();
    } else{
        header("Location:/index.php");
      }
    if( $activo == 0){
        //si la cuenta está desactivada no les dejamos hacer login
        header("Location:/index.php");
        exit;
    } 
    $psswrdSinHashear = $_POST['key'];
    if($cliente == false) {
        $_SESSION['NoExiste']=true;
        print"<p>cliente no existe</p>";
    } else{
        $psswrdHasheada =$cliente->getPsswrd();
       // print"$psswrdHasheada";
        $psswrdExiste = password_verify($psswrdSinHashear, $psswrdHasheada);
        if( $psswrdExiste) {
            print "all good";
            $_SESSION['user']=$usuario;
            $_SESSION['key'] = $psswrdHasheada;
            $_SESSION['usuario']=$usuario; //no es lo mismo que session de user
            $_SESSION['auth'] = "OK";
            $_SESSION['rol'] = $rol;
           // //////print_r($_SESSION);;
           if( ( isset($_SESSION["RegistroDurantePedido"]) && $_SESSION["RegistroDurantePedido"] == 1 ) ||
           ( isset($_SESSION["CarritoConfirmado"]) && !empty($_SESSION["CarritoConfirmado"])) 
       ){         //       print'vamos a medoto de pago';
                header('Location: ../Views/MetodoDePago.php');
                exit;
            } else{
       //         print'vamos patras';
                print "<script>history.back();</script>";
                exit;
            }
        } else {
     //       print "bad psswrd";
            $_SESSION['BadPsswrd'] = true;
        }
    }
}

if( (isset($_SESSION['BadPsswrd']) && $_SESSION['BadPsswrd'] == true) ||
( (isset($_SESSION['NoExiste']) && $_SESSION['NoExiste'] == true) )){
    $_SESSION['UserNoSession'] = true;
   // print "hubo un error".$_SESSION['NoExiste']."psswrd:". $_SESSION['BadPsswrd'];
    header("Location: ../index.php?Destroy=Y");
    exit;
}

//si me mandan aquí estando ya logueado no debería hacer más que llevarme a un sitio u otro, pero no quedare aquí
if( ( isset($_SESSION['user']) && !empty($_SESSION['user'])  && isset($_SESSION["CarritoConfirmado"]) && !empty($_SESSION["CarritoConfirmado"])) 
){
//print'vamos a medoto de pago';
header('Location: ../Views/MetodoDePago.php');
exit;
} else{
//  print'vamos patras';
print "<script>history.back();</script>";
exit;
}
?>
 <?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "Router dice: user no estaba en session ";
    header("Location: ../index.php");
}
include_once("../Models/Cliente.php");
try {
    $usuario=$_SESSION['user'];
    $cliente = Cliente::GetClientByEmail($usuario);
    $rol = $cliente->getRol();

    if($rol == "admin" && $cliente !== false) {
        $_SESSION['usuario']=$usuario; //no es lo mismo que session de user
        $_SESSION['auth'] = "OK";
        $_SESSION['rol'] = $rol;
       header("Location: ../Views/TablaClientes.php");
    } elseif($rol == "user" && $cliente !== false) {
        $_SESSION['usuario']=$usuario;
        $_SESSION['auth'] = "OK";
        $_SESSION['rol'] = $rol;
        $dni=$cliente->getDni();
        header("Location: ../Views/ClienteEDITAR.php?dni=$dni");
    } elseif($rol == "editor" && $cliente !== false) {
        $_SESSION['usuario']=$usuario;
        $_SESSION['auth'] = "OK";
        $_SESSION['rol'] = $rol;
        $dni=$cliente->getDni();
        header("Location: ../Views/ClienteEDITAR.php?dni=$dni");
    } else {
        //usuario, rol o el cliente no existen, recargamos index
        $_SESSION['FailedAuth']=true;
        header("Location: ../index.php");
    }
}catch(PDOException $e){
    $_SESSION['BadOperation']=true;
    header("Location: ../index.php");
}
?>
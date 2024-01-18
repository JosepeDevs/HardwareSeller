<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

//NO PUEDO PROTEGER ESTO PORQUE POR AQUÍ PASA AÑADIR CLIENTE NUEVO QUE REQUIERE QUE NO HAYA LOGIN

include_once("ValidarDni.php");
include_once("ValidarEmail.php");
include_once("conectarBD.php");
include_once("Cliente.php");
include_once("ValidarTelefono.php");
include_once("ValidaLongitudes.php");

$nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : null;
$dniNuevo = isset($_POST["dni"]) ? $_POST["dni"] : null;//solo llegará por POST el DNI de los INSERT, el de los edit llega por SESSION (porque no permitimos edición de dni)
$direccion = isset($_POST["direccion"]) ? $_POST["direccion"] : null;
$localidad = isset($_POST["localidad"]) ? $_POST["localidad"] : null;
$provincia = isset($_POST["provincia"]) ? $_POST["provincia"] : null;
$telefono = isset($_POST["telefono"]) ? $_POST["telefono"] : null;
$email = isset($_POST["email"]) ? $_POST["email"]:null;
$rol = isset($_POST["rol"]) ? $_POST["rol"]: "user";

if(in_array(strtolower($rol), array("user", "admin", "editor"))){
    //se ha enviado un rol correcto, no validamos longitud porque ha cumplido con el enum
} else{
    $_SESSION['BadRol']= true;
}
//si no hay post de contraseña es que se dejó en blanco (no se quiere cambiar), por lo que psswrd debe ser la misma que ya tenía antes (nos llega por SESSION)
if(isset($_POST["psswrd"]) && !empty($_POST['psswrd'])){
    $psswrd = $_POST["psswrd"];
    $psswrd = password_hash($psswrd, PASSWORD_DEFAULT);
}else{
    $psswrd = isset($_SESSION['psswrd']) ? $_SESSION['psswrd'] : null;
    $_SESSION['NoPsswrd'] = true;
}

$nombreValido = ComprobarLongitud($nombre,50);
if($nombreValido == false) {    $_SESSION['LongNombre']= true; }

$direccionValida = ComprobarLongitud($direccion,60);
if($direccionValida == false) { $_SESSION['LongDireccion']= true;}

$localidadValida = ComprobarLongitud($localidad,60);
if($localidadValida == false) {  $_SESSION['LongLocalidad']= true;}

$provinciaValido = ComprobarLongitud($provincia,30);
if($provinciaValido == false) { $_SESSION['LongProvincia']= true;}

$emailOriginal = isset($_SESSION['email']) ? $_SESSION['email'] : null; //aquí estamos recibiendo el email original del cliente

$emailRepetido = EmailRepetido($email);
if($emailRepetido == true && ($emailOriginal !== $email)) {//entonces es que está intentando cambiarse el correo y ha puesto uno que ya existe
    echo"estamos comparando $emailOriginal y $email";
    $_SESSION['EmailAlreadyExists']= true;
}

$emailFormato=ValidarEmail($email);
$longitudCorrectaEmail= ComprobarLongitud($email, 30);
if($emailFormato == false || $longitudCorrectaEmail == false){
    $_SESSION['EmailBadFormat']= true;
};

$telefonoValido=ValidaTelefono($telefono);
if($telefonoValido == false){
     $_SESSION['TelefonoMal']= true;
}else{
    $telefono = str_replace('.', '', $telefono);
    $telefono = str_replace('-', '', $telefono);
}

//solo llegará DNI de los EDIT por SESSION
if(isset($_SESSION['dni'])) {
    $dniOriginal = $_SESSION["dni"];
    $dniOriginal =strtoupper($dniOriginal);
    echo $dniOriginal;
    $formatoDni = ValidaDni($dniOriginal);
    if($formatoDni == false) {
        echo "el dni estaba mal";
        $_SESSION['DniBadFormat']= true;
    }
}

//lso DNI de nuevos clientes llegará por POST
if(isset($_POST['dni'])) {
    $dniOriginal = $_POST["dni"];
    $dniOriginal =strtoupper($dniOriginal);
    $formatoDni = ValidaDni($dniOriginal);
    if($formatoDni == false) {
        $_SESSION['DniBadFormat']= true;
    }
}


if(
    isset($_SESSION['DniBadFormat'])|| isset($_SESSION['TelefonoMal']) || isset($_SESSION['EmailBadFormat'] )|| isset($_SESSION['EmailAlreadyExists'] )||
    isset($_SESSION['LongProvincia'] )|| isset($_SESSION['LongLocalidad'] )||     isset($_SESSION['LongDireccion'])|| isset($_SESSION['LongNombre'] )|| isset($_SESSION['BadRol'])
){
    if(
        $_SESSION['DniBadFormat'] == true || $_SESSION['TelefonoMal'] == true || $_SESSION['EmailBadFormat'] == true || $_SESSION['EmailAlreadyExists'] == true ||
        $_SESSION['LongProvincia'] == true || $_SESSION['LongLocalidad'] == true || $_SESSION['LongDireccion']== true || $_SESSION['LongNombre'] == true || $_SESSION['BadRol'] == true
    ) {
        echo "<script>history.back();</script>";
        exit;
    }
}
$_SESSION["direccion"] = $direccion;
$_SESSION["localidad"] = $localidad;
$_SESSION["provincia"] = $provincia;
$_SESSION["telefono"] = $telefono;
$_SESSION["nombre"] = $nombre;
$_SESSION["email"] = $email;
$_SESSION["psswrd"] = $psswrd;
$_SESSION["rolCliente"] = $rol;
//no subimos a session el rol porque  no se permite la edicion de eso

    if( isset($_SESSION["editandoCliente"]) && $_SESSION["editandoCliente"] == "true" ){
        $_SESSION["dni"]=$dniOriginal;
        echo "<p>'actualizando cliente...espere infinito...</p>";
        print_r($_SESSION);
        header("location:UpdateCliente.php?dni=$dniOriginal");
        exit;
    }else if( isset($_SESSION["nuevoCliente"]) && $_SESSION["nuevoCliente"] == "true" ){
        $_SESSION["dni"]=$dniNuevo;
        echo "<p>'insertando cliente...espere infinito...</p>";
        header("location:InsertarCliente.php");
        exit;
    };
?>
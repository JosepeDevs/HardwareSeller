<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

//NO PUEDO PROTEGER ESTO PORQUE POR AQUÍ PASA AÑADIR CLIENTE NUEVO QUE REQUIERE QUE NO HAYA LOGIN


//este archivo valida los datos de los clientes y según qué estaban haciendo los redirige si todo estaba bien
include_once("../Models/Cliente.php");

$nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : null;
$dniNuevo = isset($_POST["dni"]) ? $_POST["dni"] : null;//solo llegará por POST el DNI de los INSERT, el de los edit llega por SESSION (porque no permitimos edición de dni)
$telefono = isset($_POST["telefono"]) ? $_POST["telefono"] : null;
$email = isset($_POST["email"]) ? $_POST["email"]:null;
$rol = "user";
$activo = 0;
if(in_array(strtolower($rol), array("user", "admin", "editor", "empleado"))){
    //se ha enviado un rol correcto, no validamos longitud porque ha cumplido con el enum
} else{
    $_SESSION['BadRol']= true;
}

$nombreValido = Cliente::ComprobarLongitud($nombre,50);
if($nombreValido == false) {    $_SESSION['LongNombre']= true; }

$emailRepetido = Cliente::EmailRepetido($email);
if($emailRepetido == true){
    $_SESSION['EmailAlreadyExists']= true;
} 

$emailFormato = Cliente::ValidarEmail($email);
$longitudCorrectaEmail = Cliente::ComprobarLongitud($email, 30);
if($emailFormato == false || $longitudCorrectaEmail == false){
    $_SESSION['EmailBadFormat']= true;
} else{
    $_SESSION['EmailBadFormat']= false;
}

$telefonoValido = Cliente::ValidaTelefono($telefono);
if($telefonoValido == false){
    $_SESSION['TelefonoMal']= true;
}else{
    $_SESSION['TelefonoMal']= false;//telefono OK
    $telefono = str_replace('.', '', $telefono);
    $telefono = str_replace('-', '', $telefono);
}

//lso DNI de nuevos clientes llegará por POST
if(isset($_POST['dni'])) {
    $dni = $_POST["dni"];
    $dni =strtoupper($dni);
    $formatoDni = Cliente::ValidaDni($dni);
    if($formatoDni == false) {
        $_SESSION['DniBadFormat']= true;
    } 
}

if(
    ( isset($_SESSION['DniBadFormat']) && $_SESSION['DniBadFormat'] == true) ||
    ( isset($_SESSION['TelefonoMal']) && $_SESSION['TelefonoMal'] == true) ||
    ( isset($_SESSION['EmailBadFormat']) && $_SESSION['EmailBadFormat'] == true) ||
    ( isset($_SESSION['LongNombre']) && $_SESSION['LongNombre'] == true) ||
    ( isset($_SESSION['EmailAlreadyExists']) && $_SESSION['EmailAlreadyExists'] == true )
){
    //algo dio error, go back para que allí de donde venga se muestre el error
   echo "<script>history.back();</script>";
    exit;
} 

//SUBIR A SESSION DATOS
$_SESSION["telefono"] = $telefono;
$_SESSION["nombre"] = $nombre;
$_SESSION["email"] = $email;
$_SESSION["dni"] = $dni;
if(isset($_SESSION['RegistroDurantePedido']) && $_SESSION["RegistroDurantePedido"] == 1 ){
    //cuando están registrandose en el carrito necesito que en psswrd esté sin hashear
    $_SESSION["psswrdSinHash"] = $psswrdSinHash;
}
$_SESSION["psswrd"] = $psswrd;
$_SESSION["rolCliente"] = $rol;
$_SESSION["activo"] = $activo;
$direccion = null;
$localidad = null;
$provincia = null;
$psswrd = "HardWare"; //No quiero hacer nullable la columna de contraseña así que para los registros minimos tendrán la contraseña HardWare, podrán reestablecer contraseña.

if( isset($_SESSION["nuevoCliente"]) && $_SESSION["nuevoCliente"] == "true" ){
    $arrayDatosCliente  = array($dni, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol, $activo);

    $operacionExitosa = Cliente::InsertCliente($dni, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol, $activo);
    if($operacionExitosa == true){
        $_SESSION['GoodInsertCliente']= true;
    } else{
        $_SESSION['BadInsertCliente']= true;
    }
    header("Location: ../Views/MetodoDePago.php");
    exit;

};

?>
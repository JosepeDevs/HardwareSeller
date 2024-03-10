<?php
//meter en función de validar CLIENTE dentro de CLIENTE
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

//NO PUEDO PROTEGER ESTO PORQUE POR AQUÍ PASA AÑADIR CLIENTE NUEVO QUE REQUIERE QUE NO HAYA LOGIN

//este archivo valida los datos de los clientes y según qué estaban haciendo los redirige si todo estaba bien
include_once("../Models/Cliente.php");

$nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : null;
$dniNuevo = isset($_POST["dni"]) ? $_POST["dni"] : null;//solo llegará por POST el DNI de los INSERT, el de los edit llega por SESSION (porque no permitimos edición de dni)
$direccion = isset($_POST["direccion"]) ? $_POST["direccion"] : null;
$localidad = isset($_POST["localidad"]) ? $_POST["localidad"] : null;
$provincia = isset($_POST["provincia"]) ? $_POST["provincia"] : null;
$telefono = isset($_POST["telefono"]) ? $_POST["telefono"] : null;
$email = isset($_POST["email"]) ? $_POST["email"]:null;
$rol = isset($_POST["rol"]) ? $_POST["rol"]: "user";

if(in_array(strtolower($rol), array("user", "admin", "editor", "empleado"))){
    //se ha enviado un rol correcto, no validamos longitud porque ha cumplido con el enum
} else{
    $_SESSION['BadRol']= true;
}
//si no hay post de contraseña es que se dejó en blanco (no se quiere cambiar), por lo que psswrd debe ser la misma que ya tenía antes (nos llega por SESSION)
if(isset($_POST["psswrd"]) && !empty($_POST['psswrd'])){
    $psswrd = $_POST["psswrd"];
    $psswrdSinHash = $_POST["psswrd"];
    $noPsswrd = false;//este bool se usa para comprobar si hay contraseña nueva o no.
    $psswrd = password_hash($psswrd, PASSWORD_DEFAULT);
    //print("Contraseña ha llegado y vale:".$psswrd);
}else{
    $psswrd = isset($_SESSION['psswrd']) ? $_SESSION['psswrd'] : null;
    $_SESSION['NoPsswrd'] = true;
    $noPsswrd = true;
   // print("Contraseña no ha llegado:".$psswrd);
}

$nombreValido = Cliente::ComprobarLongitud($nombre,50);
if($nombreValido == false) {    $_SESSION['LongNombre']= true; }
//print("nombre valiod:".$nombreValido);

$direccionValida = Cliente::ComprobarLongitud($direccion,60);
if($direccionValida == false) { $_SESSION['LongDireccion']= true;}
//print("direccion valida:".$direccionValida);

$localidadValida = Cliente::ComprobarLongitud($localidad,60);
if($localidadValida == false) {  $_SESSION['LongLocalidad']= true;}
//print("localidad valida:".$localidadValida);

$provinciaValida = Cliente::ComprobarLongitud($provincia,30);
if($provinciaValida == false) { $_SESSION['LongProvincia']= true;}
//print("provincia valida:".$provinciaValida);

$activoValido = Cliente::ComprobarLongitud($activo,1);
if($activoValido == false && ($activo !== "1" || $activo !== "0")) { $_SESSION['LongActivo']= true;}
//print("activo valido:".$activoValido);



////print_r($_SESSION);;
$emailOriginal = isset($_SESSION['email']) ? $_SESSION['email'] : null; //aquí estamos recibiendo el email original del cliente
//print("email sin usar :".$emailOriginal);

$emailRepetido = Cliente::EmailRepetido($email);
if($emailRepetido == true && $emailOriginal == $email){
    //entonces es que no se ha cambiado el email, está manteniendo el mismo
    //print("<br>estamos comparando >$emailOriginal< y >$email< <br>");
    $_SESSION['EmailAlreadyExists']= false;
} else if($emailRepetido == true && ($emailOriginal !== $email)) {//entonces es que está intentando cambiarse el correo y ha puesto uno que ya existe
   // echo" <br>Se quiere cambiar el correo y estamos comparando $emailOriginal y $email porque el email está repetido:$emailRepetido<br>";
    $_SESSION['EmailAlreadyExists']= true;
}

$emailFormato = Cliente::ValidarEmail($email);
//print("email formato OK? :".$emailFormato);
$longitudCorrectaEmail = Cliente::ComprobarLongitud($email, 30);
if($emailFormato == false || $longitudCorrectaEmail == false){
    //print("email longitud OK?1");
    $_SESSION['EmailBadFormat']= true;
} else{
    $_SESSION['EmailBadFormat']= false;
}

$telefonoValido = Cliente::ValidaTelefono($telefono);
if($telefonoValido == false){
   // print("telefono mal");
    $_SESSION['TelefonoMal']= true;
}else{
    $_SESSION['TelefonoMal']= false;//telefono OK
    $telefono = str_replace('.', '', $telefono);
    $telefono = str_replace('-', '', $telefono);
  //  print("<br>telefono OK y lo dejamos bonito sin . ni , <br>");
}

//solo llegará DNI de los EDIT por SESSION
if(isset($_SESSION['dni'])) {
    $dniOriginal = $_SESSION["dni"];
    $dniOriginal =strtoupper($dniOriginal);
    //print("dni original".$dniOriginal);
    $formatoDni = Cliente::ValidaDni($dniOriginal);
    if($formatoDni == false) {
       // echo "el dni de edit (recibido por session) estaba mal";
        $_SESSION['DniBadFormat']= true;
    } else {
       // echo "el dni estaba bien";
        $_SESSION['DniBadFormat']= false;
    }
}

//lso DNI de nuevos clientes llegará por POST
if(isset($_POST['dni'])) {
    $dniOriginal = $_POST["dni"];
    $dniOriginal =strtoupper($dniOriginal);
    $formatoDni = Cliente::ValidaDni($dniOriginal);
    if($formatoDni == false) {
        $_SESSION['DniBadFormat']= true;
      //  print("dni de nuevo cliente mal");
    } else{
        $_SESSION['DniBadFormat']= false;
       // print("dni format bien");
    }
}

//RETROCEDER DE DONDE VINIERAMOS SI HAY ALGÚN ERROR

if(
    ( isset($_SESSION['DniBadFormat']) && $_SESSION['DniBadFormat'] == true) ||
    ( isset($_SESSION['TelefonoMal']) && $_SESSION['TelefonoMal'] == true) ||
    ( isset($_SESSION['EmailBadFormat']) && $_SESSION['EmailBadFormat'] == true) ||
    ( isset($_SESSION['LongNombre']) && $_SESSION['LongNombre'] == true) ||
    ( isset($_SESSION['LongProvincia']) && $_SESSION['LongProvincia'] == true) ||
    ( isset($_SESSION['LongLocalidad']) && $_SESSION['LongLocalidad'] == true) ||
    ( isset($_SESSION['LongDireccion']) && $_SESSION['LongDireccion'] == true) ||
    ( isset($_SESSION['BadRol']) && $_SESSION['BadRol'] == true) ||
    ( isset($_SESSION['EmailAlreadyExists']) && $_SESSION['EmailAlreadyExists'] == true )
){
    //algo dio error, go back para que allí de donde venga se muestre el error
   echo "<script>history.back();</script>";
    exit;
} 


//SUBIR A SESSION DATOS
$_SESSION["direccion"] = $direccion;
$_SESSION["localidad"] = $localidad;
$_SESSION["provincia"] = $provincia;
$_SESSION["telefono"] = $telefono;
$_SESSION["nombre"] = $nombre;
$_SESSION["email"] = $email;
if(isset($_SESSION['RegistroDurantePedido']) && $_SESSION["RegistroDurantePedido"] == 1 ){
    //cuando están registrandose en el carrito necesito que en psswrd esté sin hashear
    $_SESSION["psswrdSinHash"] = $psswrdSinHash;
}
$_SESSION["psswrd"] = $psswrd;
$_SESSION["rolCliente"] = $rol;
$_SESSION["activo"] = $activo;


//UPDATE o INSERT , SUBIR confirmación a SESSION y HEADER A DONDE TOQUE
if( isset($_SESSION["editandoCliente"]) && $_SESSION["editandoCliente"] == "true" ){
        
    $_SESSION["dni"]=$dniOriginal;
    //print "<p>'actualizando cliente...espere infinito...</p>";
    ////print_r($_SESSION);;
    //print "<p>'editando cliente...espere infinito...datos que estamos pasando: $dniOriginal, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol, $noPsswrd</p>";
    
    $operacionExitosa = Cliente::UpdateCliente($dniOriginal, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol, $activo, $noPsswrd);//le pasamos el DniOoriginal porque no permitimos el cambio del dni
    
    if($operacionExitosa){
        $_SESSION['GoodUpdateCliente']= true;
    }
    include_once("OperacionesSession.php");
    $rolAdmin = AuthYRolAdmin();
    if($rolAdmin == true) {
        header("Location: ../Views/TablaClientes.php");
        exit;
    } else {
        header("Location: ../Views/ClienteEDITAR.php?dni=$dniOriginal");
        exit;
    }
    
}else if( isset($_SESSION["nuevoCliente"]) && $_SESSION["nuevoCliente"] == "true" ){
    
    $_SESSION["dni"]=$dniNuevo;
    //echo "<p>'insertando cliente...espere infinito...datos que estamos pasando: $dniNuevo, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol, $activo</p>";
    $operacionExitosa = Cliente::InsertCliente($dniNuevo, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol, $activo);
    // echo"<br>la operacion ha sido existosa??$operacionExitosa<br>";
    if($operacionExitosa == true){
        $_SESSION['GoodInsertCliente']= true;
    } else{
        $_SESSION['GoodInsertCliente']= false;
    }

    include_once("OperacionesSession.php");
    $rolAdmin = AuthYRolAdmin();

    ////////////ROUTER
    if($rolAdmin == true) {
           header("Location: ../Views/TablaClientes.php");
            exit;
    } else if(isset($_SESSION['estadoEnvio']) &&  $_SESSION['sinCuenta'] == true){
        header("Location: ../Views/MetodoDePago.php");
        exit;
    } else if( isset($_SESSION["RegistroDurantePedido"]) && $_SESSION["RegistroDurantePedido"] == 1){
        header("Location: ../Controllers/conexion.php");
        exit;
    } else{
        header("Location: ../Views/ClienteALTA.php?dni=$dniNuevo");
        exit;
    }
};

?>
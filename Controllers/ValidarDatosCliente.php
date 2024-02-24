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
$activo = isset($_POST["activo"]) ? $_POST["activo"]:1;

if(in_array(strtolower($rol), array("user", "admin", "editor"))){
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

$nombreValido = ComprobarLongitud($nombre,50);
if($nombreValido == false) {    $_SESSION['LongNombre']= true; }
//print("nombre valiod:".$nombreValido);

$direccionValida = ComprobarLongitud($direccion,60);
if($direccionValida == false) { $_SESSION['LongDireccion']= true;}
//print("direccion valida:".$direccionValida);

$localidadValida = ComprobarLongitud($localidad,60);
if($localidadValida == false) {  $_SESSION['LongLocalidad']= true;}
//print("localidad valida:".$localidadValida);

$provinciaValida = ComprobarLongitud($provincia,30);
if($provinciaValida == false) { $_SESSION['LongProvincia']= true;}
//print("provincia valida:".$provinciaValida);

$activoValido = ComprobarLongitud($activo,1);
if($activoValido == false && ($activo !== 1 || $activo !== 0)) { $_SESSION['LongActivo']= true;}
//print("activo valido:".$activoValido);



//print_r($_SESSION);
$emailOriginal = isset($_SESSION['email']) ? $_SESSION['email'] : null; //aquí estamos recibiendo el email original del cliente
//print("email sin usar :".$emailOriginal);

$emailRepetido = EmailRepetido($email);
if($emailRepetido == true && $emailOriginal == $email){
    //entonces es que no se ha cambiado el email, está manteniendo el mismo
    //print("<br>estamos comparando >$emailOriginal< y >$email< <br>");
    $_SESSION['EmailAlreadyExists']= false;
} else if($emailRepetido == true && ($emailOriginal !== $email)) {//entonces es que está intentando cambiarse el correo y ha puesto uno que ya existe
   // echo" <br>Se quiere cambiar el correo y estamos comparando $emailOriginal y $email porque el email está repetido:$emailRepetido<br>";
    $_SESSION['EmailAlreadyExists']= true;
}

$emailFormato=ValidarEmail($email);
//print("email formato OK? :".$emailFormato);
$longitudCorrectaEmail= ComprobarLongitud($email, 30);
if($emailFormato == false || $longitudCorrectaEmail == false){
    //print("email longitud OK?1");
    $_SESSION['EmailBadFormat']= true;
} else{
    $_SESSION['EmailBadFormat']= false;
}

$telefonoValido=ValidaTelefono($telefono);
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
    $formatoDni = ValidaDni($dniOriginal);
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
    $formatoDni = ValidaDni($dniOriginal);
    if($formatoDni == false) {
        $_SESSION['DniBadFormat']= true;
      //  print("dni de nuevo cliente mal");
    } else{
        $_SESSION['DniBadFormat']= false;
       // print("dni format bien");
    }
}

//RETROCEDER DE DONDE VINIERAMOS SI HAY ALGÚN ERROR
if(isset($_SESSION['DniBadFormat']) && ($_SESSION['DniBadFormat'] == true)){
   //print("hubo un error en Dni ");
    echo "<script>history.back();</script>";
    exit;
}
if(isset($_SESSION['TelefonoMal']) && ($_SESSION['TelefonoMal']) == true){
   // print("hubo un error en Telefono ");
    echo "<script>history.back();</script>";
    exit;
}
if(isset($_SESSION['EmailBadFormat']) && ($_SESSION['EmailBadFormat'])== true){
   // print("hubo un error en Email ");
    echo "<script>history.back();</script>";
    exit;
}
if(isset($_SESSION['EmailAlreadyExists']) && ($_SESSION['EmailAlreadyExists'])  == true){
   // print("hubo un error EmailAlreadyExists ");
    echo "<script>history.back();</script>";
    exit;
}
if(isset($_SESSION['LongProvincia']) && ($_SESSION['LongProvincia']) == true){
   // print("hubo un error en LongProvincia ");
    echo "<script>history.back();</script>";
    exit;
}
if(isset($_SESSION['LongLocalidad']) && ($_SESSION['LongLocalidad']) == true){
   // print("hubo un error en LongLocalidad ");
    echo "<script>history.back();</script>";
    exit;
}
if(isset($_SESSION['LongDireccion']) && ($_SESSION['LongDireccion']) == true){
   // print("hubo un error en LongDireccion ");
    echo "<script>history.back();</script>";
    exit;
}
if(isset($_SESSION['LongNombre']) && ($_SESSION['LongNombre'])  == true){
   // print("hubo un error en LongNombre ");
    echo "<script>history.back();</script>";
    exit;
}
if(isset($_SESSION['BadRol']) && ($_SESSION['BadRol']) == true){
   // print("hubo un error en BadRol ");
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
    $_SESSION["psswrd"] = $psswrdSinHash;
}
$_SESSION["psswrd"] = $psswrd;
$_SESSION["rolCliente"] = $rol;
$_SESSION["activo"] = $activo;


//print("<br> array session:");
//print_r($_SESSION);

//UPDATE o INSERT , SUBIR confirmación a SESSION y HEADER A DONDE TOQUE
if( isset($_SESSION["editandoCliente"]) && $_SESSION["editandoCliente"] == "true" ){

    $arrayDatosCliente  = array($dniNuevo, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol, $activo, $noPsswrd);
    //print("<br> array del cliente:");
    //print_r($arrayDatosCliente);
    
    $_SESSION["dni"]=$dniOriginal;
    //print "<p>'actualizando cliente...espere infinito...</p>";
    //print_r($_SESSION);
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
    $arrayDatosCliente  = array($dniNuevo, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol, $activo);
    // print("<br> array del cliente:");
    //print_r($arrayDatosCliente);

    
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

    
    if($rolAdmin == true) {
           header("Location: ../Views/TablaClientes.php");
            exit;
        } else if( isset($_SESSION["RegistroDurantePedido"]) && $_SESSION["RegistroDurantePedido"] == 1){
            header("Location: ../Controllers/conexion.php");
            exit;
        } else{
           header("Location: ../Views/ClienteALTA.php?dni=$dniNuevo");
            exit;
        }
    };

    ///FUNCIONES PARA QUE ESTÉ TODO EN UN MISMO ARCHIVO PHP

    /**
  * @param $dni . (String) con el dni a comprobar (8 numeros y 1 letra, da igual minus o mayus)
  * @return bool devuelve true si la letra se corresponde a las 8 cifras introducidas. Devuelve false si la letra no es correcta, si no cumple regex de ser 8 numeros y 1 letra (permitimos que el input esté en minusculas), también devuelve false si dni es null.
  */
Function ValidaDni($dniNuevo){
    if($dniNuevo == null){
        return false;
    };
    if(preg_match("/^\d{8}\w{1}$/", $dniNuevo) == true ){
        $numerosString=substr($dniNuevo,0,8);
        $letra=strtoupper(substr($dniNuevo,8,9));
        $arrayLetras=array("T","R","W","A","G","M","Y","F","P","D","X","B","N","J","Z","S","Q","V","H","L","C","K","E");
        $numero=intval($numerosString);
        $resto=$numero%23;
        $letraCalculada=$arrayLetras[$resto];
        if($letra == $letraCalculada){
            return true;
        } else {
            return false;
        }
    }else{
        return false;
    }
}

/**
 * @param string telefono como texto cada 3 numeros puede haber una separación de un punto o un guión y sería aceptable. p.e. formatos aceptados formatos: 444-555-123, 246.555.888, 123456789
 *@return bool devuelve true si telefono cumple regex de 3 cifras guion?punto? 3 cifras guion?punto? 3 cifras. Devuelve false si no cumple regex
  *  */
  function ValidaTelefono($telefono){
    $formatoTelefonoCorrecto = preg_match("/\d{3}[-.]?\d{3}[-.]?\d{3}/", $telefono);
    if($formatoTelefonoCorrecto == true) {
        return true;
    } else {
        return false;
    }
}


 /**
 * @param $email el email a comprobar, permite lo que sea delante del arroba (puntos, barras bajas. guiones, comas, excepto @)
 * @return boolean true si cumple regex, si no, devuelve false
 *
 * */
function ValidarEmail($email) {
    $email = trim($email);
    $formatoEmailCorrecto = preg_match("/^[\w\-\.]+@([\w-]+\.)+[\w-]{2,}$/", $email);
    if ( $formatoEmailCorrecto == true ) {
        return true;// all good
    } else {
        return false;
    }
};


/**
 * @param string Recibe correo electrónico (esta función NO valida el formato), solo comprueba si ya existe en la BBDD.
 * @return bool true si ya existe en la BBDD, false si no está en uso.
 */
 function EmailRepetido($email) {
    $conPDO = contectarBbddPDO();
    $emailCheck = "SELECT `email` FROM `clientes` WHERE `email` = :email";
    $statement = $conPDO->prepare($emailCheck);
    $statement->bindParam(':email', $email);
    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_CLASS,"Cliente");
    $yaHayclienteConEseCorreo = $statement->fetch();
    if ( $yaHayclienteConEseCorreo !== false) {
        return true;
    } else {
        return false;
    }
};

/**
 * @param string $string String a comprobar.
 * @param int $longitud longitud máxima.
 * @return bool devuelve true si NO supera la longitud especificada, devuelve false si es más largo de lo especificado.
 */
Function ComprobarLongitud($string, $longitud) {
    if(strlen($string) > $longitud) {
        return false;
    }
    return true;
}

/*
/////////////TESTS//////////////

/////////EMAIL
$email1 = "usua.rio@123";
$email2 = "123.com";
$email3 = "usuario@321.com";
$email4 = "usua.rio@321.com";
$email5 = "us.u.a.ri.o@321.com";
print(validarEmail($email1)==true?"$email1 nice<br>":"bad<br>");//bad = test OK
print(validarEmail($email2)==true?"$email2 nice<br>":"bad<br>");//bad = test OK
print(validarEmail($email3)==true?"$email3 nice<br>":"bad<br>");//test ok
print(validarEmail($email4)==true?"$email4 nice<br>":"bad<br>");//test ok
print(validarEmail($email5)==true?"$email5 nicesdadas<br>":"bad<br>");//test ok

///////TELEFONO
$telefono1="123456789";
$telefono2="123.321.321";
$telefono3="123-321-321";
$telefono4="123.321321";
$telefonoMalo1="333,444,444";
$telefonoMalo2="333.44444";
$telefonoMalo3="333,444,444";

if(ValidaTelefono($telefono1)==true){print "nice $telefono1<br>";}else {print "bad";}; //test OK
if(ValidaTelefono($telefono2)==true){print "nice $telefono2<br>";}else {print "bad";};//test OK
if(ValidaTelefono($telefono3)==true){print "nice $telefono3<br>";}else {print "bad";};//test OK
if(ValidaTelefono($telefono4)==true){print "nice $telefono4<br>";}else {print "bad";};//test OK
if(ValidaTelefono($telefonoMalo1)==true){print "nice $telefonoMalo1<br>";}else {print "bad";}; //test deberia ser BAD --> OK!
if(ValidaTelefono($telefonoMalo2)==true){print "nice $telefonoMalo2<br>";}else {print "bad";};//test deberia ser BAD --> OK!
if(ValidaTelefono($telefonoMalo3)==true){print "nice $telefonoMalo3<br>";}else {print "bad";};//test deberia ser BAD --> OK!

///////////DNI
$dni1="13123122A";
if(ValidaDni($dni1)){echo"good";}else{echo"bad";} //test OK si ==bad -->  OK!
$dni2="13123122N";
if(ValidaDni($dni2)){echo"good";}else{echo"bad";} //test OK si ==good -->  OK!
$dninull=null;
if(ValidaDni($dni3)){echo"good";}else{echo"bad";} //test OK si ==good -->  OK!


/////////LONGITUDES
$nombre="hola me llamo montesino del sur y me gusta comer monos crudos cuando aun están intentando correr por sus vidas";
$nombre2="junan";
$char59="1234567891234567891234567894561231232312345678912";
$char60="12234567891234567891234567894561231232312345678912";
$char61="122234567891234567891234567894561231312312345678912";
echo strlen($char59)."<br>";
echo strlen($char60)."<br>";
echo strlen($char61)."<br>";
if(ComprobarLongitud($nombre,50)==true){echo "nice";}else{echo "bad";};//debería ser bad--->OK
echo"<br>";
if(ComprobarLongitud($nombre2,50)==true){echo "nice";}else{echo "bad";}; //debería ser nice --->OK
echo"<br>";
if(ComprobarLongitud($char59,50)==true){echo "nice";}else{echo "bad";}; //debería ser nice --->OK
echo"<br>";
if(ComprobarLongitud($char60,50)==true){echo "nice";}else{echo "bad";}; //debería ser nice --->OK
echo"<br>";
if(ComprobarLongitud($char61,50)==true){echo "nice";}else{echo "bad";}; //debería ser bad --->OK
*/
?>
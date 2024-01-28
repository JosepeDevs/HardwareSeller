<?php
//meter en función de validar CLIENTE dentro de CLIENTE
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

//NO PUEDO PROTEGER ESTO PORQUE POR AQUÍ PASA AÑADIR CLIENTE NUEVO QUE REQUIERE QUE NO HAYA LOGIN



//este archivo valida los datos de los clientes y según qué estaban haciendo los redirige si todo estaba bien
include_once("/../Models/Cliente.php");

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

//RETROCEDER DE DONDE VINIERAMOS SI HAY ALGÚN ERROR
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

//SUBIR A SESSION DATOS
$_SESSION["direccion"] = $direccion;
$_SESSION["localidad"] = $localidad;
$_SESSION["provincia"] = $provincia;
$_SESSION["telefono"] = $telefono;
$_SESSION["nombre"] = $nombre;
$_SESSION["email"] = $email;
$_SESSION["psswrd"] = $psswrd;
$_SESSION["rolCliente"] = $rol;

$arrayDatosCliente  = array($dni, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol, $noPsswrd);

//UPDATE o INSERT , SUBIR confirmación a SESSION y HEADER A DONDE TOQUE
    if( isset($_SESSION["editandoCliente"]) && $_SESSION["editandoCliente"] == "true" ){
        $_SESSION["dni"]=$dniOriginal;
        echo "<p>'actualizando cliente...espere infinito...</p>";
        print_r($_SESSION);
        $operacionExistosa = Cliente::UpdateCliente($dni, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol, $noPsswrd);

        if($operacionExistosa){
            $_SESSION['GoodUpdateCliente']= true;
        }
        include_once("OperacionesSession.php");
        $rolAdmin = AuthYRolAdmin();
        if($rolAdmin == true) {
            header("Location: ../Views/TablaClientes.php");
            exit;
        } else {
            header("Location: ../Views/ClienteEDITAR.php?dni=$dni");
            exit;
        }

    }else if( isset($_SESSION["nuevoCliente"]) && $_SESSION["nuevoCliente"] == "true" ){
        $_SESSION["dni"]=$dniNuevo;
        echo "<p>'insertando cliente...espere infinito...</p>";
        $operacionExistosa = Cliente::InsertCliente($dni, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol);

        if($operacionExistosa){
            $_SESSION['GoodInsertCliente']= true;
        }

        include_once("OperacionesSession.php");
        $rolAdmin = AuthYRolAdmin();
        if($rolAdmin == true) {
            header("Location: ../Views/TablaClientes.php");
            exit;
        } else {
            header("Location: ../Views/ClienteALTA.php?dni=$dni");
            exit;
        }
    };

    ///FUNCIONES PARA QUE ESTÉ TODO EN UN MISMO ARCHIVO PHP

    /**
  * @param $dni . (String) con el dni a comprobar (8 numeros y 1 letra, da igual minus o mayus)
  * @return bool devuelve true si la letra se corresponde a las 8 cifras introducidas. Devuelve false si la letra no es correcta, si no cumple regex de ser 8 numeros y 1 letra (permitimos que el input esté en minusculas), también devuelve false si dni es null.
  */
Function ValidaDni($dni){
    if($dni == null){
        return false;
    };
    if(preg_match("/^\d{8}\w{1}$/", $dni) == true ){
        $numerosString=substr($dni,0,8);
        $letra=strtoupper(substr($dni,8,9));
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
?>
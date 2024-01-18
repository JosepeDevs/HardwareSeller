<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//NO PUEDO PROTEGER ESTO PORQUE POR AQUÍ PASA AÑADIR CLIENTE NUEVO QUE REQUIERE QUE NO HAYA LOGIN

include_once("conectarBD.php");
include_once("Cliente.php");

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

/*
 //Ejemplo de uso
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
*/

?>
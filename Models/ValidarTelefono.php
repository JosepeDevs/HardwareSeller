<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

//NO PUEDO PROTEGER ESTO PORQUE POR AQUÍ PASA AÑADIR CLIENTE NUEVO QUE REQUIERE QUE NO HAYA LOGIN

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

//tests
/*
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
*/
?>
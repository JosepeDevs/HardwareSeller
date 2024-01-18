<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//NO PUEDO PROTEGER ESTO PORQUE POR AQUÍ PASA AÑADIR CLIENTE NUEVO QUE REQUIERE QUE NO HAYA LOGIN

include_once("conectarBD.php");
include_once("Cliente.php");

/**
 * @param string $string String a comprobar.
 * @param int $longitud longitud máxima.
 * @return bool devuelve true si NO supera la longitud especificada, devuelve false si es más largo de lo especificado.
 */
Function ComprobarLongitud($nombreAtributo, $longitud) {
    if(strlen($nombreAtributo) > $longitud) {
        return false;
    }
    return true;
}

/*
//tests
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
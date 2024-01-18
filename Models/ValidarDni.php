<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//NO PUEDO PROTEGER ESTO PORQUE POR AQUÍ PASA AÑADIR CLIENTE NUEVO QUE REQUIERE QUE NO HAYA LOGIN

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


//tests
/*
$dni1="13123122A";
if(ValidaDni($dni1)){echo"good";}else{echo"bad";} //test OK si ==bad -->  OK!
$dni2="13123122N";
if(ValidaDni($dni2)){echo"good";}else{echo"bad";} //test OK si ==good -->  OK!
$dninull=null;
if(ValidaDni($dni3)){echo"good";}else{echo"bad";} //test OK si ==good -->  OK!
*/

?>
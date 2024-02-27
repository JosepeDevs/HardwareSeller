<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "Pedido dice: no está user en session";
    header("Location: /index.php");
}
include_once("../config/conectarBD.php");
include_once("../Controllers/Directorio.php");
//todo poner a todas cosas que puedan llegar por get htmlspecialchars para que no intenten meternos M.... y revisar que todas las funciones que devuelven pedido tengan
class Pedido {



    
}
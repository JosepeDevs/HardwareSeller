
<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "Directorio dice: no está user en session";
    header("Location: index.php");
}

//include_once("ProtegerDatos.php");
//if(!ComprobarAuth()){ session_destroy();header("location:index.php");}
/**
 * Crea en la carpeta actual una carpeta llamada "hemeroteca", si no existe la crea(permisos 777). Si ya existe devuelve su PATH. A esto hay que añadirle al final el nombre del archivo para guardarlo en el sitio y con el nombre deseado.
 * @return string string con el PATH a la carpeta HemerotecaBD, la cual se crea justo debajo de donde está el archivo que llama a esta función.
 */
function PrepararDirectorio() {
    $actualPath = __DIR__;
    $rutaCarpeta = $actualPath . '/hemerotecaBD/';
    if (!file_exists($rutaCarpeta)) {
        mkdir($rutaCarpeta, 0777, true);
    }
    return $rutaCarpeta;
}

// $directorio= PrepararDirectorio();
//target: hemerotecaBD\tacocat--a-cat-made-of-taco-body.png
//$imagen = "../hemerotecaBD/1.jpg";
//El ../ al principio de la ruta significa "subir un nivel en la jerarquía de carpetas"

?>
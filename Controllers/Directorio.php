
<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//si protejo esto los usuarios no pueden ver las imágenes del catálogo

/**
 * Crea en la carpeta actual una carpeta llamada "hemeroteca", si no existe la crea(permisos 777). Si ya existe devuelve su PATH. A esto hay que añadirle al final el nombre del archivo para guardarlo en el sitio y con el nombre deseado.
 * @return string string con el PATH a la carpeta HemerotecaBD, la cual se crea justo debajo de donde está el archivo que llama a esta función.
 */
function PrepararDirectorio() {
    $actualPath = __DIR__;//esto es controllers
    $parentDirectory = dirname($actualPath);//parent directory es algo asi /home/vol8_4/infinityfree.com/if0_35787488/htdocs/
    $rutaCarpeta =$parentDirectory.'/Reports/ImagenesArticulos/';
    if (!file_exists($rutaCarpeta)) {//si no existe el directorio lo crea con permisos totales
        mkdir($rutaCarpeta, 0777, true);
    }
    return $rutaCarpeta;
}

function DirectorioInformes() {
    $rutaCarpeta = '/Reports/';
    if (!file_exists($rutaCarpeta)) {//si no existe el directorio lo crea con permisos totales
        mkdir($rutaCarpeta, 0777, true);
    }
    return $rutaCarpeta;
}


?>
<?php
//crear metodo en ARTICULO QUE VALIDE TODO
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ValidaImagens dice: no está user en session";
    header("Location: index.php");
}
include_once("Directorio.php");
/** Función que DEBE ser llamada tras un formulario (p.e. un POST), ya que consulta >>>>>>>>>>>>$_FILES['imagen']<<<<<<<<<<<<. Comprueba si la imagen subida cumple los criterios especificados.
 * @return bool Devulve true si archivo cumple: 1 ser jpg, png, jpeg o gif ; 2 pesar menos de 300Kb ; 3 dimensiones máximas 200 x 200 px ; 4 no existe ya una imagen con ese nombre. Si CUALQUIER de las anteriores condiciones no se cumple devuelve false.
 */
function ValidaImagen(){

    if (isset($_FILES['imagen'])) {
        if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

        $nombreArchivo = $_FILES['imagen']['name'];//este es el nombre con el que se sube el archivo (como lo nombra el usuario)
        //@ delante impide mensajes de error (ya lo estamos controlando con mensajes en el sistema)
        $arrayInfoImagen = @getimagesize($_FILES['imagen']['tmp_name']);//con esto no nos colarán cosas que no sean imágenes.
        if($arrayInfoImagen == false) {
            $_SESSION['FileBadFormat']= true;
        }

        $formatoImagen = preg_match("/\.(jpg|gif|png|jpeg)$/", $nombreArchivo);
        if ($formatoImagen == false) {
            $_SESSION['FileBadFormat']= true;
        }

        if($arrayInfoImagen[0] > 200 || $arrayInfoImagen[1] > 200) { //en el indice 0 tenemos el ancho y en el indice 1 tenemos el alto
            echo"<br>ValidaImagenes says: el ancho es $arrayInfoImagen[0] y el alto es $arrayInfoImagen[1]";
            $_SESSION['ImagenGrande'] = true;
        }

        $directorio=PrepararDirectorio();
        $directorioDestino = $directorio ."/". $nombreArchivo;

        if (file_exists($directorioDestino)) {
            $_SESSION['FileAlreadyExists']= true;
        }
        $tamaño = $_FILES['imagen']['size'];
        if($_FILES['imagen']['size'] > 300 * 1024) { //el 1024 es para pasar los Bits a Kilobits
            echo"<br>ValidaImagenes says: el tamaño del archivo es $tamaño";
            $_SESSION['ImagenPesada'] = true;
        }

        if(
            isset($_SESSION['ImagenPesada']) && $_SESSION['ImagenPesada'] == true ||
            isset( $_SESSION['FileAlreadyExists']) && $_SESSION['FileAlreadyExists']== true ||
            isset( $_SESSION['ImagenGrande']) && $_SESSION['ImagenGrande']== true ||
            isset( $_SESSION['FileBadFormat']) && $_SESSION['FileBadFormat']== true
        ){
            return false;
        } else{
            return true;
        }

    } else{
        return false;
    }
}
?>
<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "UpdateArticulo: dice: shit no está user en session";
    header("Location: index.php");
}

include_once("conectarBD.php");
include_once("Articulo.php");
include_once("Directorio.php");

//una vez aquí dentro hay que "reiniciar" el valor de "editando"
$_SESSION["editandoArticulo"]="false";
$con = contectarBbddPDO();

//rescatamos de session los datos subidos por ValidarDatos
$nombre = ( isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : null );
$codigoOriginal = ( isset($_SESSION["codigo"]) ? $_SESSION["codigo"] : null );//por session llega el código ORIGINAL
$descripcion = ( isset($_SESSION["descripcion"]) ? $_SESSION["descripcion"] : null );
$categoria = ( isset($_SESSION["categoria"]) ? $_SESSION["categoria"] : null );
$precio = ( isset($_SESSION["precio"]) ? $_SESSION["precio"] : null );
$imagen = ( isset($_SESSION["imagen"]) ? $_SESSION["imagen"] : null ); //nueva imagen o la imagen vieja
$imagenReciclada = ( isset($_SESSION["imagenReciclada"]) ? $_SESSION["imagenReciclada"] : null ); //nueva imagen

$codigo = ( isset($_GET["codigo"]) ? $_GET["codigo"] : null ); //por la URL llega el código NUEVO

$mantienenCodigo = ($codigo == $codigoOriginal || $codigo == null);

try{
    $conPDO = contectarBbddPDO();
    if( $mantienenCodigo){
        $sqlQuery = " UPDATE `articulos`
                  SET `nombre` = :nombre, `codigo` = :codigoOriginal, `descripcion` = :descripcion, `categoria` = :categoria, `precio` = :precio, `imagen` = :imagen
                  WHERE `codigo` = :codigoOriginal "
        ;
    } else{
        $sqlQuery = " UPDATE `articulos`
                  SET `nombre` = :nombre, `codigo` = :codigo, `descripcion` = :descripcion, `categoria` = :categoria, `precio` = :precio, `imagen` = :imagen
                  WHERE `codigo` = :codigoOriginal "
        ;
    }
    echo "<br>UpdateArticulo says: codigo nuevo: $codigo"." y codigo original: ".$codigoOriginal."<br>";
    echo "<br>UpdateArticulo says:".$sqlQuery;
    $statement= $conPDO->prepare($sqlQuery);
    $statement->bindParam(':nombre', $nombre);

    if($mantienenCodigo){
        $statement->bindParam(':codigoOriginal', $codigoOriginal);
    }else{
        $statement->bindParam(':codigo', $codigo);
        $statement->bindParam(':codigoOriginal', $codigoOriginal);
    }

    $statement->bindParam(':descripcion', $descripcion);
    $statement->bindParam(':categoria', $categoria);
    $statement->bindParam(':precio', $precio);

    $estamosReciclandoImagen = ( isset($_SESSION["imagenReciclada"]) && !empty($_SESSION["imagenReciclada"]) );
    if( $estamosReciclandoImagen ){
        $statement->bindParam(':imagen', $imagenReciclada);
    } else{
        $statement->bindParam(':imagen', $imagen);
    }

    $operacionRealizada = $statement->execute();

    if($operacionRealizada == false && $statement->rowCount() <= 0 && !$estamosReciclandoImagen){
        //si SQL no se ejecuta, hay que deshacer lo hecho (solo queremos borrar si estamos subiendo imagen nueva, la que ya tenía no hay que borrarla)
        if (file_exists($imagen)) {//si llegó imagen cmo aquí ya se había movido la borramos
            $_SESSION['BadUpdateArticulo']= true;
            echo "<br>nos cargamos la imagen.";
            unlink($imagen);
        }
    } else{
        $_SESSION['GoodUpdateArticulo']= true;
    }
    header("Location: ArticulosLISTAR.php");
    exit;
} catch(PDOException $e) {
    $_SESSION['OperationFailed'] = true;
   // header("Location: ArticulosLISTAR.php");
};
?>
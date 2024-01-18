<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "InsertarArticulo dice:  no está user en session";
    header("Location: index.php");
}
include_once("conectarBD.php");
include_once("Articulo.php");
include_once("Directorio.php");


$_SESSION["nuevoArticulo"]=false;

//rescatamos de session los datos subidos por ValidarDatos
$nombre = ( isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : null );
$codigo = ( isset($_SESSION["codigo"]) ? $_SESSION["codigo"] : null );
$descripcion = ( isset($_SESSION["descripcion"]) ? $_SESSION["descripcion"] : null );
$categoria = ( isset($_SESSION["categoria"]) ? $_SESSION["categoria"] : null );
$precio = ( isset($_SESSION["precio"]) ? $_SESSION["precio"] : null );
$imagen = ( isset($_SESSION["imagen"]) ? $_SESSION["imagen"] : null );

try{
        $con = contectarBbddPDO();
        $sqlQuery="INSERT INTO `articulos` (`codigo`, `nombre`, `descripcion`, `categoria`, `precio`, `imagen`)
                                    VALUES (:codigo, :nombre, :descripcion, :categoria, :precio, :imagen);";
        $statement=$con->prepare($sqlQuery);
        $statement->bindParam(':codigo', $codigo);
        $statement->bindParam(':nombre', $nombre);
        $statement->bindParam(':descripcion', $descripcion);
        $statement->bindParam(':categoria', $categoria);
        $statement->bindParam(':precio', $precio);
        $statement->bindParam(':imagen', $imagen);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
        $resultado = $statement->fetch();

        if ($resultado !== false && $resultado->rowCount() == 0) {
            $_SESSION['BadInsertArticulo']= true;
            if (file_exists($imagen)) {//si no se realiza la operación borramos la imagen (aquí ya se había movido)
                unlink($imagen);
            }
        } else {
            $_SESSION['GoodInsertArticulo']= true;
        }
        header("Location: ArticulosLISTAR.php");
        exit;
} catch(PDOException $e) {
    $_SESSION['BadInsertArticulo']= true;
    header("Location: ArticuloALTA.php");
    exit;
};
?>
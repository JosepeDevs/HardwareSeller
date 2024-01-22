<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticulosLISTARMensajes dice: no está user en session";
    header("Location: index.php");
}
include_once("conectarBD.php");
include_once("Articulo.php");

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Borrado de Artículo</title>
        <link rel="stylesheet" href="estilosTabla.css">
    </head>
    <body>
        <?php


$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : null;

//Leemos de la URL la confirmación para borrarlo o no
if(isset($_GET['confirmacion']) && $_GET['confirmacion'] ==  'false' ){
    $codigo=$_GET["codigo"];
    $_SESSION['BorradoArticuloCancelado'] = true;
    header("Location: ArticulosLISTAR.php");
    exit;
}else if(isset($_GET['codigo']) && isset($_GET['confirmacion']) && $_GET['confirmacion']== 'true') {
    try {
        $codigo=$_GET["codigo"];

        $conPDO=contectarBbddPDO();
        $query=("select * from articulos WHERE codigo='$codigo'");
        $statement= $conPDO->prepare($query);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Articulo');
        $cliente= $statement->fetch();
        $imagen = $cliente->getImagen();

        if (file_exists($imagen)) {//si llegó imagen cmo aquí ya se había movido la borramos
            $imagenBorrada = true;
            echo "<br>nos cargamos la imagen.";
            unlink($imagen);
        }

        $sqlQuery = "DELETE FROM `articulos` WHERE `codigo`=:codigo";
        $statement= $conPDO->prepare($sqlQuery);
        $statement->bindParam(':codigo', $codigo);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,'Articulo');
        if($statement->rowCount() > 0 && $imagenBorrada) {
            $_SESSION['ExitoBorrandoArticulo'] = true;
        } else {
            $_SESSION['ExitoBorrandoArticulo'] = false;
        }

        header("Location: ArticulosLISTAR.php");
        exit;
    } catch(PDOException $e) {
        $_SESSION['BadOperation'] = true;
        header("Location: ArticulosLISTAR.php");
    };
} else {
    //entonces es la primera vez que entran a la página (no hay que hacer nada, que se lea el html y cuando se recarge ya se verá que se hace)
}
?>
        <h1>¿Está seguro de que desea eliminar este artículo?</h1>
        <div class="finForm">
            <h2><a href="ArticuloBORRAR.php?codigo=<?php echo $codigo;?>&confirmacion=true">Sí, borrar el artículo de la base de datos.</a></h2>
            <h2><a href="ArticuloBORRAR.php?codigo=<?php echo $codigo;?>&confirmacion=false">Cancelar borrado.</a></h2>
        </div>
    </body>
</html>

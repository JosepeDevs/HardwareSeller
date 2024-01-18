<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cliente borrado</title>
    </head>
    <body>
        <?php
include_once("conectarBD.php");
include_once("Cliente.php");
include_once("CheckRol.php");
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "borrarcliente dice: shit no está user en session";
    header("Location: index.php");
}

$dni = isset($_GET['dni']) ? $_GET['dni'] : null;


//Leemos de la URL la confirmación para borrarlo o no
if(isset($_GET['confirmacion']) && $_GET['confirmacion'] ==  'false' ){
    $dni=$_GET["dni"];
    $_SESSION['BorradoClienteCancelado'] = true;
    if(AuthYRolAdmin() == true){
        header("Location: TablaClientes.php");
        exit;
    } else {
        header("Location: editarcliente.php?dni=$dni");
        exit;
    }
}else if(isset($_GET['dni']) && isset($_GET['confirmacion']) && $_GET['confirmacion']== 'true') {
    try {
        $dni=$_GET["dni"];
        $sqlQuery = "DELETE FROM `clientes` WHERE `dni`=:dni";
        $conPDO=contectarBbddPDO();
        $statement= $conPDO->prepare($sqlQuery);
        $statement->bindParam(':dni', $dni);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,'Cliente');
        if($statement->rowCount() > 0) {
            $_SESSION['ExitoBorrandoCliente'] = true;
        } else {
            $_SESSION['ExitoBorrandoCliente'] = false;
            header("Location: borrarcliente.php");
        }

        if($_SESSION['rol'] == "admin"){
            header("Location: TablaClientes.php");
            exit;
        } else {
            header("Location: index.php");
            exit;
        }
    } catch(PDOException $e) {
        $_SESSION['BadOperation'] = true;
        header("Location: index.php");
    };
} else {
    //entonces es la primera vez que entran a la página (no hay que hacer nada, que se lea el html y cuando se recarge ya se verá que se hace)
}
?>
        <h1>¿Está seguro de que desea eliminar este cliente?</h1>
        <div class="finForm">
            <h2><a href="borrarcliente.php?dni=<?php echo $dni; ?>&confirmacion=true">Sí, borrar la cuenta y datos.</a></h2>
            <h2><a href="borrarcliente.php?dni=<?php echo $dni; ?>&confirmacion=false">Cancelar borrado.</a></h2>
        </div>
    </body>
</html>

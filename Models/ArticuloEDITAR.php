<!DOCTYPE html>
<html>
<head>
    <title>Editar artículos</title>
    <link rel="stylesheet" type="text/css" href="estilosTabla.css">
</head>
<body>
    <h1>Modificar artículo</h1>
    <?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticuloEDITAR dice: no está user en session";
    header("Location: index.php");
}


include_once("conectarBD.php");
include_once("Articulo.php");
echo("<h2>Bienvenido</h2>");
//ponemos "editando" en true para que cuando lo mandemos a ValidarDatos lo trate como update
$_SESSION["editandoArticulo"]="true";
try {
    $conPDO=contectarBbddPDO();
    //el DNI ha llegado por la url (es el dni original, lo guardamos como dni)
    $codigoOriginal=$_GET["codigo"];
    $_SESSION["codigo"] = $codigoOriginal;
    $verQuery=("select * from articulos WHERE codigo=:codigoOriginal");
    $statement= $conPDO->prepare($verQuery);
    $statement->bindValue(':codigoOriginal', $codigoOriginal);
    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,'Articulo');
    //ENCABEZADOS
    $reflejo = new ReflectionClass('Articulo');
    $arrayAtributos = $reflejo->getProperties(ReflectionProperty::IS_PRIVATE);//como hemos puesto todos private vamos a meter esos en un array
    $rol4consulta = isset($_GET['rol4consulta'])? $_GET['rol4consulta'] : null;
    echo"<table>";
    echo"<tr><th>Atributos:</th>";
    foreach ($arrayAtributos as $index => $atributo) {
        $nombreAtributo = $atributo->getName();
        echo "<th>$nombreAtributo</th>";
    }
    echo "</tr>";
    echo"<tr><th>Datos actuales:</th>";
    //datos ACTUALES OBJETO
    while($fila=$statement->fetch()){
        $codigoOriginal=$fila->getCodigo();
        $nombre=$fila->getNombre();
        $descripcion=$fila->getDescripcion();
        $categoria=$fila->getCategoria();
        $precio=$fila->getPrecio();
        $imagen=$fila->getImagen();
        echo "
        <td>$codigoOriginal</td>
        <td>$nombre</td>
        <td>$descripcion</td>
        <td>$categoria</td>
        <td>$precio</td>
        <td><img class='imagenes' src='{$imagen}' width='200' height='200'/></td>";
    }
    $_SESSION['codigo'] = $codigoOriginal;

    echo '<form action="ArticuloVALIDAR.php" method="POST" enctype="multipart/form-data">';//ENVIAREMOS MEDIANTE $_POST EL NUEVO (SI LO HA EDITADO)
    echo"<tr><th>Nuevos datos</th>";
    //creamos un campo por cada atributo
    foreach ($arrayAtributos as $index => $atributo) {
        $nombreAtributo = $atributo->getName();
            if( $nombreAtributo == "precio") {
                echo "<td><input type='number' id='$nombreAtributo' name='$nombreAtributo' required ></td>";
            } else if( $nombreAtributo == "imagen") {
                echo "<td><input type='file' id='$nombreAtributo' name='$nombreAtributo' accept='.jpg,.jpeg,.png,.gif' ></td>";
            } else if( $nombreAtributo == "codigo") {
                echo "<td><input type='text' id='$nombreAtributo' name='$nombreAtributo'></td>";
            } else {
                echo "<td><input type='text' id='$nombreAtributo' name='$nombreAtributo' required></td>";
            };
    }
    echo "</tr>
          <tr>
            <th>Consejos:</th>
            <td colspan=6>Puede escribir el mismo código o cambiarlo (si el nuevo no está en uso). Si deja código en blanco se mantendrá el código actual.
            <br>Si no se sube una imagen mantiene la que tenía subida.
            </td>
          </tr>";
    echo "</table>";
    echo "<div class='finForm'><h2><input type='submit' value='Guardar'></h2>";
    echo "<h2><input type='reset' value='Reinicar'></h2></div>";
    echo "</form>";
} catch(PDOException $e) {
    $_SESSION["BadUpdateArticulo"]=true;
    header("Location: ArticuloEDITAR.php");
};

include_once("ArticuloEDITARMensajes.php");
$arrayMensajes=getArrayMensajesArticulos();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};


echo("<h2><a class='cerrar' href='ArticulosLISTAR.php?editandoArticulo=false'>Volver al listado de productos</a></h2><br>");
echo("<h2><a class='cerrar' href='index.php?editandoArticulo=false'> cerrar sesión</a></h2><br><br><br>");
echo("<h2><a class='cerrar' a href='ArticuloBORRAR.php?codigo=$codigoOriginal'>BORRAR ARTÍCULO</a></h2>");
?>
</body>
</html>
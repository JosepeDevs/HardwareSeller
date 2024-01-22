<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticuloEDITAR dice: no está user en session";
    header("Location: index.php");
}


include_once("header.php");
print("<h1>Modificar artículo</h1>");
include_once("/../Controllers/ArticuloEDITARController.php");
$codigoOriginal=$_GET["codigo"];    //el DNI ha llegado por la url
$_SESSION["codigo"] = $codigoOriginal;
$arrayAtributos = getArrayAtributos();

echo("<h2>Bienvenido</h2>");
//ponemos "editando" en true para que cuando lo mandemos a ValidarDatos lo trate como update
$_SESSION["editandoArticulo"]="true";
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
$arrayArticulos = getAllArticulos();

for ($i = 0; $i < count($arrayArticulos); $i++) {
    $codigoOriginal = $arrayArticulos[$i]->getCodigo();
    $nombre = $arrayArticulos[$i]->getNombre();
    $descripcion = $arrayArticulos[$i]->getDescripcion();
    $categoria = $arrayArticulos[$i]->getCategoria();
    $precio = $arrayArticulos[$i]->getPrecio();
    $imagen = $arrayArticulos[$i]->getImagen();
    echo "
    <td>$codigoOriginal</td>
    <td>$nombre</td>
    <td>$descripcion</td>
    <td>$categoria</td>
    <td>$precio</td>
    <td><img class='imagenes' src='{$imagen}' width='200' height='200'/></td>";
    }
    $_SESSION['codigo'] = $codigoOriginal;

    echo '<form action="/../Controllers/ArticuloVALIDAR.php" method="POST" enctype="multipart/form-data">';//ENVIAREMOS MEDIANTE $_POST EL NUEVO (SI LO HA EDITADO)
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

include_once("/../Controllers/ArticuloEDITARMensajes.php");
$arrayMensajes=getArrayMensajesArticulos();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};

echo("<h2><a class='cerrar' href='ArticulosLISTAR.php?editandoArticulo=false'>Volver al listado de productos</a></h2><br>");
echo("<h2><a class='cerrar' href='index.php?editandoArticulo=false'> cerrar sesión</a></h2>");
echo("<h2><a class='cerrar' a href='ArticuloBORRAR.php?codigo=$codigoOriginal'>BORRAR ARTÍCULO</a></h2>");
include_once("footer.php");
?>
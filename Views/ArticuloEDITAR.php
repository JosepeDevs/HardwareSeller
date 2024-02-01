<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticuloEDITAR dice: no está user en session";
    header("Location: index.php");
}


include_once("header.php");
print("<h1>Modificar artículo</h1>");

include_once("../Controllers/ArticuloEDITARController.php");
$codigoOriginal=$_GET["codigo"];    //el codigo ha llegado por la url
$_SESSION["codigo"] = $codigoOriginal;
$arrayAtributos = getArrayAtributosArticulo();

echo("<h2>Bienvenido</h2>");
//ponemos "editando" en true para que cuando lo mandemos a ValidarDatos lo trate como update
$_SESSION["editandoArticulo"]="true";
$rol4consulta = isset($_GET['rol4consulta'])? $_GET['rol4consulta'] : null;
echo"<table>";
        echo"<tr><th>Atributos:</th>";
                foreach ($arrayAtributos as $index => $atributo) {
                    $nombreAtributo = $atributo;
                    echo "<th>$nombreAtributo</th>";
                }
        echo "</tr>";

        //datos ACTUALES OBJETO (estaticos, para que se vean siempre los actuales)
        echo"<tr>
                <th>Datos actuales:</th>";
                    $articulo = getArticuloByCodigo($codigoOriginal);
                    //imprimimos los valores
                    foreach ($arrayAtributos as $atributo) {
                        $getter = 'get' . ucfirst($atributo);
                        $valor = $articulo->$getter();
                        if($atributo == "imagen" ){
                            $directorio = "/Resources/ImagenesArticulos/";
                            $rutaAbsoluta = $directorio . $valor;
                            echo"<td><img class='imagenes' src='{$rutaAbsoluta}' width='200' height='200'/><br>$valor</td>";
                        } else if($atributo == "activo"){
                            if($valor == 1){
                                echo "<td>Activo ($valor)</td>";
                            }else{
                                echo "<td>Inactivo ($valor)</td>";
                            }
                        }else{
                            echo "<td>$valor</td>";
                        }
                    }
                //FORMULARIO para EDITAR PRERELLENADO para que se mantengan los datos si no cambia nada
                    echo '<form action="../Controllers/ArticuloVALIDAR.php" method="POST" enctype="multipart/form-data">';//ENVIAREMOS MEDIANTE $_POST EL NUEVO (SI LO HA EDITADO)
                    echo"<tr><th>Nuevos datos</th>";
                    foreach ($arrayAtributos as $atributo) {
                        $getter = 'get' . ucfirst($atributo);
                        $nombreAtributo = $atributo;
                        $valor = $articulo->$getter();
                        if( $nombreAtributo == "descuento" || $nombreAtributo == "precio") {
                            echo "<td><input type='number' accept='^(\d+\.\d+|\d+)$'step='0.01' id='$nombreAtributo' name='$nombreAtributo' value='$valor'></td>";
                        } else if( $nombreAtributo == "imagen") {
                            echo "<td>
                                     <input type='file' name='imagen' accept='.jpg,.jpeg,.png,.gif'>
                                </td>";
                        } else{
                            echo "<td><input type='text' id='$nombreAtributo' name='$nombreAtributo' value='$valor'></td>";
                        }
                    }
        echo "</tr>
   </table>";
    echo "<div class='finForm'><h2><input type='submit' value='Guardar'></h2>";
    echo "</form>";

include_once("../Controllers/ArticuloEDITARMensajes.php");
$arrayMensajes=getArrayMensajesArticulos();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};

echo("<h2><a class='cerrar' href='ArticulosLISTAR.php?editandoArticulo=false'>Volver al listado de productos</a></h2>");
echo("<h2><a class='cerrar' href='/index.php?editandoArticulo=false'> cerrar sesión</a></h2>");
echo("<h2><a class='cerrar' a href='ArticuloBORRAR.php?codigo=$codigoOriginal'>BORRAR ARTÍCULO</a></h2>");
include_once("footer.php");
?>
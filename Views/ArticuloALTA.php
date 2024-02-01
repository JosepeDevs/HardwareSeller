
<?php
//require controller y nada más, dependencia de fuera hacia dentro
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "Articulos alta dice: no está user en session";
    header("Location: /index.php");
}

include("header.php");
echo"<h1>Alta de artículo</h1>";


$_SESSION["nuevoArticulo"]="true";//ponemos esto a true para que cuando vaya a validar datos lo trate como un insert
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;
//ENCABEZADOS
?>
    <form action="../Controllers/ArticuloVALIDAR.php" method="post" enctype="multipart/form-data" >
        <table>
            <tr>
                <th>Atributos:</th>
                <th><label for="nombre">Nombre</label></th>
                <th><label for="codigo">Código* (3 letras, 5 números)</label></th>
                <th><label for="descripcion">Descripción</label></th>
                <th><label for="categoria">Categoría</label></th>
                <th><label for="precio">Precio (€)</label></th>
                <th><label for="imagen">Imagen</label></th>
                <th><label for="descuento">Descuento</label></th>
                <th><label for="activo">Activo</label></th>
            </tr>
            <tr>
                <th>Datos del artículo nuevo:</th>
                <td><input type="text" name="nombre" id="nombre" required><br><br></td>
                <td><input type="text" name="codigo" id="codigo" required><br><br></td>
                <td><input type="text" name="descripcion" id="descripcion" required ><br><br></td>
                <td><input type="text" name="categoria" id="categoria" required><br><br>
                <td><input type="number" name="precio" id="precio" required><br><br>
                <td><input type="file" name="imagen" accept=".jpg,.jpeg,.png,.gif" required><br><br></td>
                <td><input type="number" name="descuento" id="descuento" required><br><br></td>
                    <td><select name="activo" id="activo" required>
                        <option value="0">Desactivado</option>
                        <option value="1">Activado</option>
                    </td></select>
            </tr>
            <tr>
                <th>
                    Consejos:
                </th>
                <td colspan=8>
                    Código debe estar formado por 3 letras (OBLIGATORIO) y algún número inferior a 99999.<br>
                    Las letras pueden ser minus o mayus (el sistema las pasa a MAYUS).<br>
                    Puede escribir el código como "cat1", "inf253", el sistema añadirá los 0s a la izquierda necesarios hasta que hallan 5 números.
                </td>
            </tr>
        </table>
        <div class="finForm">
            <h2><input type="submit" value="Guardar"></h2><br><br><br>
            <h2><input type="reset" value="Reiniciar formulario"></h2>
        </div>
    </form>
    <br><br>
<?php

include_once("../Controllers/ArticuloALTAMensajes.php");
$arrayMensajes=getArrayMensajesAltaArticulos();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};

echo"<h2><a class='cerrar'  href='ArticulosLISTAR.php'>Volver a la tabla de artículos.</a></h2>";

include("footer.php");
?>

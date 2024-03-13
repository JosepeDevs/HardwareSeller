
<?php
//require controller y nada más, dependencia de fuera hacia dentro
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "Categorias alta dice: no está user en session";
    header("Location: /index.php");
    exit;

}
$rol = GetRolDeSession();
if( $rol == "admin" || $rol == "empleado" || $rol == "editor"){
} else{
    session_destroy();
    print "Articulos alta dice: no está user en session";
    header("Location: /index.php");
    exit;

}


include("header.php");
print"<h1>Alta de Categoría</h1>";
//print_r($_SESSION);;

$_SESSION["nuevoCategoria"]="true";//ponemos esto a true para que cuando vaya a validar datos lo trate como un insert
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;
//ENCABEZADOS
?>
    <form action="../Controllers/CategoriaVALIDAR.php" method="post" enctype="multipart/form-data" >
        <table>
            <tr>
                <th>Atributos:</th>
                <th><label for="nombre">Nombre</label></th>
                <th><label for="codigo">Código numerico</label></th>
                <th><label for="activo">Activo</label></th>
                <th><label for="codCategoriaPadre">Codigo de la categoría padre</label></th>
            </tr>
            <tr>
                <th>Datos del Categoria nuevo:</th>
                <td><input type="text" name="nombre" id="nombre" required><br><br></td>
                <td><input type="text" name="codigo" id="codigo" required><br><br></td>
                <td><select name="activo" id="activo" required>
                    <option value="0">Desactivado</option>
                    <option value="1">Activado</option>
                </td></select>
                <td><input type="text" name="codCategoriaPadre" id="codCategoriaPadre" required><br><br></td>
            </tr>
        </table>
        <div class="finForm">
            <h2><input type="submit" value="Guardar"></h2><br><br><br>
            <h2><input type="reset" value="Reiniciar formulario"></h2>
        </div>
    </form>
    <br><br>
<?php

include_once("../Controllers/CategoriaMensajes.php");
$arrayMensajes=getArrayMensajesCategorias();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        print "<h3>$mensaje</h3>";
    }
};

print"<h2><a class='cerrar'  href='CategoriasLISTAR.php'>Volver a la tabla de Categorias.</a></h2>";

include("footer.php");
?>

<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticulosLISTAR dice: no está user en session";
    header("Location: index.php");
}

include_once("header.php");
print("<h1>Gestionar artículos</h1>");

include_once("/../Controllers/ExtraeDeSession.php");
if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin" ){
    echo"<h2><a class='enlace' href='ArticuloALTA.php'><img src='addAr.png' alt='añadir' /> Nuevo artículo (solo admin y editores)</h2></a>";
}
if(GetRolDeSession() == "admin" ){
    echo"<h2><a class='enlace' href='TablaClientes.php'><img src='search.png' alt='añadir' /> Ver clientes</h2></a></a>";
} else {
    include_once("/../Controllers/GetEmailByDniController.php");
    $email = GetEmailDeSession();
    $dni=GetDniByEmail($email);
    if($dni == null ){
        $_SESSION['OperationFailed'] = true;
        echo"<h2><a class='enlace' href='BuscarCliente.php'><img src='search.png' alt='añadir' /> Buscar cliente </h2></a></a>";
    } else{
        echo"<h2><a class='enlace' href='EditarCliente.php?dni=$dni'><img src='search.png' alt='añadir' /> Editar mis datos de usuario $email </h2></a></a>";
    }
}
?>
<h2><a class='enlace' href='ArticulosLISTAR.php'><img src='refresh.png' alt='refrescar' /> Recargar tabla (Quita ordenación y reinicia paginación)</h2></a>
<h2><a class='enlace' href='ArticuloBUSCAR.php'><img src="buscaAr.png" alt="recraft icon"/> Buscar artículo</h2></a>
<table>
    <tr>
        <th>Imagen</th>
        <th>Codigo</th>
        <th>
            Nombre <br><br>Ordenar:<br>
            <a class='ordenar' href='/../OrdenarArticulosController.php?ordenNombres=ASC'>A->Z</a>
            <a class='ordenar' href='/../OrdenarArticulosController.php?ordenNombres=DESC'>Z->A</a>
        </th>
        <th>Descripción</th>
        <th>Categoría</th>
        <th>Precio</th>
<?php
                /* //estos includes creo que no hacen falta
                if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
                include_once("/../Controllers/ResetSession.php");
                include_once("/../Controllers/ArticulosLISTARMensajes.php");
*/
                include_once("/../Controllers/ExtraeDeSession.php");//get rol
                if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin" ){
                    echo"
        <th>Editar</th>
        <th>Borrar</th>";
                }
echo"</tr>";

            //PREPARA/OBTEN DATOS DE LOS OBJETOS
            $orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
            include_once("/../Controllers/OrdenarArticulosController.php");
            $arrayArticulos = getArrayArticulosOrdenados($orden);

            //PAGINACIÓN
            $arrayAImpimir=[];
            $filasTotales = count($arrayArticulos);
            $numPagPredeterminado=3;
            $filasAMostrar = isset($_GET['numpag'])? $_GET['numpag'] : $numPagPredeterminado;
            $paginaActual = isset($_GET['pag'])? $_GET['pag'] : 0;
            if(is_numeric($paginaActual)){
                $ultimoRegistroMostrado = $paginaActual * $filasAMostrar;
            }

            if(is_numeric($paginaActual)){
                $ultimoRegistroMostrado = $paginaActual * $filasAMostrar;
                $finalRegistro = min($ultimoRegistroMostrado + $filasAMostrar, $filasTotales);
                for($i=$ultimoRegistroMostrado ; $i < $finalRegistro; $i++){
                    $codigo = $arrayArticulos[$i]->getCodigo();
                    $nombre = $arrayArticulos[$i]->getNombre();
                    $descripcion = $arrayArticulos[$i]->getDescripcion();
                    $categoria = $arrayArticulos[$i]->getCategoria();
                    $precio = $arrayArticulos[$i]->getPrecio();
                    $imagen = $arrayArticulos[$i]->getImagen();
                    $articulo = new Articulo($codigo, $nombre, $descripcion, $categoria, $precio, $imagen);
                    $arrayAImpimir[]=$articulo;
                }
            }
            if($paginaActual == "X"){
                $arrayAImpimir=$arrayArticulos;
            }

            //DATOS de los OBJETOS
            foreach($arrayAImpimir as $articulo){
                $imagen = $articulo->getImagen();//esta es la ruta relativa al recurso
                $codigo = $articulo->getCodigo();
                $nombre = $articulo->getNombre();
                $descripcion = $articulo->getDescripcion();
                $categoria = $articulo->getCategoria();
                $precio = $articulo->getPrecio();
                echo "
        <tr>
            <td><img class='imagenes' src='{$imagen}' width='200' height='200'/></td>
            <td>$codigo</td>
            <td>$nombre</td>
            <td>$descripcion</td>
            <td>$categoria</td>
            <td>$precio</td>";
                if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin"){
            echo"
            <td><a class='icon' href='ArticuloEDITAR.php?codigo=$codigo'><img src='editAr.png' alt='Editar artículo' /></td>
            <td><a class='icon' href='ArticuloBORRAR.php?codigo=$codigo'><img src='minusAr.png' alt='Borrar artículo' /></td>";
                }
            }
echo " </tr>
    </table>";


//PAGINACIÓN
echo "<div class='paginacion'>";
$paginasTotales = ceil($filasTotales / $filasAMostrar);
if(is_numeric($paginaActual) && is_numeric($filasAMostrar)){
    //estamos viendo los registros paginados
    for ($p = 0; $p < $paginasTotales; $p++) {
        if($p == $paginaActual){
            echo "<b>$p</b>";
        }else{
            echo "<a href='ArticulosLISTAR.php?pag=$p&ordenNombres=$orden&numpag=$filasAMostrar'>$p</a>";
        }
    }
} else{
    //estamos viendo todos los registros en una página
    for ($p = 0; $p < $paginasTotales; $p++) {
        echo "<a href='ArticulosLISTAR.php?pag=$p&ordenNombres=$orden&numpag=$filasAMostrar'>$p</a>";
    }
}
$opcionesNumPag=[3,4,5];

echo "<a href='ArticulosLISTAR.php?pag=X&ordenNombres=$orden'>Ver todos</a>
<form action='ArticulosLISTAR.php' method='GET'>
<label for='numpag'>Registros/página</label><br>
<select id='numpag' name='numpag' onchange='this.form.submit()' required>
    <option value='$filasAMostrar'>$filasAMostrar</option>";
    for ($i = 0; $i < count($opcionesNumPag); $i++) {
        if( $opcionesNumPag[$i] == $filasAMostrar){
            continue;
        } else{
            echo "<option value='$opcionesNumPag[$i]'>$opcionesNumPag[$i]</option>";
        }
    }
echo
"</select><br>
</form>
</div>";

//SECCION DE IMPRIMIR MENSAJE DE ERROR/CONFIRMACIÓN
            $arrayMensajes=getArrayMensajesArticulos();
            if(is_array($arrayMensajes)){
                foreach($arrayMensajes as $mensaje) {
                    echo "<h3>$mensaje</h3>";
                }
            };
//tras los mensajes de error confirmación "reseteamos" session
            ResetSession();

?>
<h2><a class="cerrar"  href='index.php'>Cerrar sesión</a></h2>
<?php
include_once("footer.php");
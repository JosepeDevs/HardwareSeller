<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticulosLISTAR dice: no está user en session";
    header("Location: index.php");
}
//HEADER Y TITULO
include_once("header.php");
print("<h1>Gestionar artículos</h1>");

//NAVEGACION
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

<?php
//TABLA LISTANDO ARTICULOS
echo"<table>";
        echo"<tr>
                <th>Atributos:</th>";
            //ENCABEZADOS
            include_once("/../Controllers/ArticulosLISTARController.php");
            $arrayAtributos = getArrayAtributos();
            foreach ($arrayAtributos as $atributo) {
                $nombreAtributo = $atributo->getName();
                echo "<th>$nombreAtributo</th>";
            }
            include_once("/../Controllers/ExtraeDeSession.php");//get rol
            if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin" ){
                echo"
                <th>Editar</th>
                <th>Borrar</th>";
            }
        echo"</tr>";

        //PREPARA/OBTEN DATOS DE LOS OBJETOS
        $orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
        $numPagPredeterminado=3;
        $filasAMostrar = isset($_GET['numpag'])? $_GET['numpag'] : $numPagPredeterminado;
        $paginaActual = isset($_GET['pag'])? $_GET['pag'] : 0;

        include_once("/../Controllers/OrdenarArticulosController.php");
        $arrayArticulos = getArrayArticulosOrdenados($orden);

        //PAGINACIÓN
        include_once("/../Controllers/ArticulosLISTARController.php");
        $arrayAImprimir = getArrayPaginado($arrayArticulos, $filasAMostrar, $paginaActual);

        //DATOS de los OBJETOS
        //llamamos dinámicamente los getters de la clase habiendo guardado previamente el array con los nombresd de los atributos
        //hay que recorrer todos los atributos en todos los objetos
        foreach($arrayAImpimir as $articulo){
            echo("<tr>");
            foreach ($arrayAtributos as $atributo) {
                $nombreAtributo = $atributo->getName();//p.e. codigo, nombre...
                $nombreMetodo = 'get' . ucfirst($nombreAtributo); //montamos el nombre del método a llamar
                $valor = call_user_func([$articulo, $nombreMetodo]);
                if($nombreAtributo == "codigo"){
                    $codigo = $articulo->getCodigo();//guardamos el código para que esté disponible fuerra de este bucle
                } else if($nombreAtributo == "imagen"){
                    echo"<td><img class='imagenes' src='{$imagen}' width='200' height='200'/></td>";
                }else{
                    echo "<td>$valor</td>";
                }
            }
            if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin"){
                echo"
                <td><a class='icon' href='ArticuloEDITAR.php?codigo=$codigo'><img src='editAr.png' alt='Editar artículo' /></td>
                <td><a class='icon' href='ArticuloBORRAR.php?codigo=$codigo'><img src='minusAr.png' alt='Borrar artículo' /></td>";
            }
        }
        echo("</tr>
    </table>");

//IMPRIMIR PAGINACIÓN
echo "<div class='paginacion'>";
$filasTotales = count($arrayArticulos);
$paginasTotales = ceil($filasTotales / $filasAMostrar);
$numPagPredeterminado=3;
if(is_numeric($paginaActual) && is_numeric($filasAMostrar)){
    //estamos viendo los registros paginados
    for ($p = 1; $p <= $paginasTotales; $p++) {
        if($p == 1){
            echo "<p>Anterior</p>"; //en la primera página esto no debe ser un enlace
        } else{
            echo "<a href='ArticulosLISTAR.php?pag=".($p-1)."&ordenNombres=$orden&numpag=$filasAMostrar'>Anterior</a>";
        }
        if($p == $paginaActual + 1){
            echo "<b>$p</b>";//la página actual no es un enlace
        }else{
            echo "<a href='ArticulosLISTAR.php?pag=$p&ordenNombres=$orden&numpag=$filasAMostrar'>$p</a>"; //el resto de páginas (en la que no estamos actualmente serán enlaces)
        }
    }
    //estamos al final de la lista, además de lo anterior también imprimiremos "siguiente"
    echo "<a href='ArticulosLISTAR.php?pag=".($paginaActual+1)."&ordenNombres=$orden&numpag=$filasAMostrar'>Siguiente</a>";
} else{
    //estamos viendo todos los registros en una página
    for ($p = 0; $p < $paginasTotales; $p++) {
        echo "<a href='ArticulosLISTAR.php?pag=$p&ordenNombres=$orden&numpag=$filasAMostrar'>$p</a>";
    }
}
$opcionesNumPag=[3,4,5];

//FORMULARIO PIE DE PÁGINA PARA ELEGIR LA PÁGINA A VER, Nº registros/pág
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
include_once("/../Controllers/ArticulosLISTARMensajes.php");
            $arrayMensajes=getArrayMensajesArticulos();
            if(is_array($arrayMensajes)){
                foreach($arrayMensajes as $mensaje) {
                    echo "<h3>$mensaje</h3>";
                }
            };
//tras printear los mensajes de error/confirmación "reseteamos" session
include_once("/../Controllers/ResetSession.php");
            ResetSession();

?>
<h2><a class="cerrar"  href='index.php'>Cerrar sesión</a></h2>
<?php
include_once("footer.php");
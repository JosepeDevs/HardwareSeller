<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticulosLISTAR dice: no está user en session";
    header("Location: ..//index.php");
}
//HEADER Y TITULO
include_once("header.php");
print("<h1>Gestionar artículos</h1>");

//NAVEGACION
include_once("../Controllers/OperacionesSession.php");
if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin" ){
    echo"
    <div id='EnlacesArriba'>
        <h2>
            <a href='ArticuloALTA.php'>
                <img class='iconArribaTabla' src='../Resources/addAr.png' alt='añadir' /> Nuevo artículo (solo admin y editores)
            </a>
        </h2>";
}
if(GetRolDeSession() == "admin" ){
    echo"<h2>
            <a href='TablaClientes.php'>
                <img class='iconArribaTabla' src='../Resources/search.png' alt='añadir' /> Ver clientes
            </a>
        </h2>";
} else {
    include_once("../Controllers/GetEmailByDniController.php");
    $email = GetEmailDeSession();
    $dni=GetDniByEmail($email);
    if($dni == null ){
        $_SESSION['OperationFailed'] = true;
        echo"<h2>
                <a href='ClienteBUSCAR.php'>
                    <img class='iconArribaTabla' src='search.png' alt='añadir' /> Buscar cliente
                </a>
            </h2>";
    } else{
        echo"<h2>
                <a href='ClienteEDITAR.php?dni=$dni'>
                    <img class='iconArribaTabla' src='search.png' alt='añadir' /> Editar mis datos de usuario $email
                </a>
            </h2>";
    }
}
?>
<h2>
    <a href='ArticulosLISTAR.php'>
        <img  class='iconArribaTabla' src='../Resources/refresh.png' alt='refrescar' /> Recargar tabla (Quita ordenación y reinicia paginación)
    </a>
</h2>
<h2>
    <a href='ArticuloBUSCAR.php'>
        <img class='iconArribaTabla'  src="../Resources/buscaAr.png" alt="recraft icon"/> Buscar artículo
    </a>
</h2>
</div>
<?php
//TABLA LISTANDO ARTICULOS
echo"<table>";
        echo"<tr>";
            //ENCABEZADOS
            include_once("../Controllers/ArticulosLISTARController.php");
            $arrayAtributos = getArrayAtributosArticulo();
            if($arrayAtributos == false){
                echo"</tr><tr><td>Sin articulos</td></tr>";
            } else{
                foreach ($arrayAtributos as $atributo) {
                    $nombreAtributo = $atributo;
                    echo "<th>$nombreAtributo</th>";
                }
                include_once("../Controllers/OperacionesSession.php");//get rol
                if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin" ){
                    echo"
                    <th>Editar</th>
                    <th>Borrar</th>";
                }
                echo"</tr>";
            }

        //PREPARA PAGINACIÓN Y ARRAY DE OBJETOS
        $orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
        $numPagPredeterminado=3;
        $filasAMostrar = isset($_GET['numpag'])? $_GET['numpag'] : $numPagPredeterminado;
        if(! isset($_GET['pag'])){
            $paginaActual = 0;
        }else{
            if( is_numeric($_GET['pag'])){
                $paginaActual = $_GET['pag'] - 1 ;
            } else if ($_GET['pag'] == "X" ){
                $paginaActual = "X";
            }
        }

        include_once("../Controllers/OrdenarArticulosController.php");
        $arrayArticulos = getArrayArticulosOrdenados($orden);

        include_once("../Controllers/ArticulosLISTARController.php");
        $arrayAImprimir = getArrayPaginadoArticulos($arrayArticulos, $filasAMostrar, $paginaActual);

        //DATOS DE LOS OBJETOS
        //llamamos dinámicamente los getters de la clase habiendo guardado previamente el array con los nombresd de los atributos
        //hay que recorrer todos los atributos en todos los objetos
        foreach($arrayAImprimir as $articulo){
            echo("<tr>");
            foreach ($arrayAtributos as $atributo) {
                $nombreAtributo = $atributo;//p.e. codigo, nombre...
                $nombreMetodo = 'get' . ucfirst($nombreAtributo); //montamos el nombre del método a llamar
                $valor = call_user_func([$articulo, $nombreMetodo]);
                if($nombreAtributo == "codigo"){
                    $codigo = $articulo->getCodigo();//guardamos el código para que esté disponible fuerra de este bucle
                    echo "<td>$valor</td>";
                } else if($nombreAtributo == "imagen"){
                    include_once("../Controllers/Directorio.php");
                    $directorio = "/Resources/ImagenesArticulos/";
                    $rutaAbsoluta = $directorio . $valor;
                    echo"<td><img class='imagenes' src='{$rutaAbsoluta}' width='200' height='200'/></td>";
                }else{
                    echo "<td>$valor</td>";
                }
            }
            if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin"){
                echo"
                <td><a class='icon' href='ArticuloEDITAR.php?codigo=$codigo'><img src='../Resources/editAr.png' alt='Editar artículo' /></td>
                <td><a class='icon' href='ArticuloBORRAR.php?codigo=$codigo'><img src='../Resources/minusAr.png' alt='Borrar artículo' /></td>";
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
    for ($numeroIndicePaginacion = 1; $numeroIndicePaginacion <= $paginasTotales; $numeroIndicePaginacion++) {
        if($numeroIndicePaginacion == 1){
            echo "<p>Anterior</p>"; //en la primera página esto no debe ser un enlace
        } else{
            echo "<a href='ArticulosLISTAR.php?pag=".($numeroIndicePaginacion-1)."&ordenNombres=$orden&numpag=$filasAMostrar'>Anterior</a>";
        }
        if($numeroIndicePaginacion == $paginaActual + 1){
            echo "<b>$numeroIndicePaginacion</b>";//la página actual no es un enlace
        }else{
            echo "<a href='ArticulosLISTAR.php?pag=$numeroIndicePaginacion&ordenNombres=$orden&numpag=$filasAMostrar'>$numeroIndicePaginacion</a>"; //el resto de páginas (en la que no estamos actualmente serán enlaces)
        }
    }
    //estamos al final de la lista, además de lo anterior también imprimiremos "siguiente"
    echo "<a href='ArticulosLISTAR.php?pag=".($paginaActual+1)."&ordenNombres=$orden&numpag=$filasAMostrar'>Siguiente</a>";
} else{
    //estamos viendo todos los registros en una página
    for ($numeroIndicePaginacion = 0; $numeroIndicePaginacion < $paginasTotales; $numeroIndicePaginacion++) {
        echo "<a href='ArticulosLISTAR.php?pag=$numeroIndicePaginacion&ordenNombres=$orden&numpag=$filasAMostrar'>$numeroIndicePaginacion</a>";
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
include_once("../Controllers/ArticulosLISTARMensajes.php");
            $arrayMensajes=getArrayMensajesArticulos();
            if(is_array($arrayMensajes)){
                foreach($arrayMensajes as $mensaje) {
                    echo "<h3>$mensaje</h3>";
                }
            };
//tras printear los mensajes de error/confirmación "reseteamos" session
include_once("../Controllers/OperacionesSession.php");
ResetearSesion();

?>
<h2><a class="cerrar"  href='/index.php'>Cerrar sesión</a></h2>
<?php
include_once("footer.php");
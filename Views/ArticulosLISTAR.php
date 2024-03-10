<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
//////print_r($_SESSION);;

$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "ArticulosLISTAR dice: no está user en session";
    header("Location: ../index.php");
    exit;

}

$rol = GetRolDeSession();
if( $rol == "admin" || $rol == "empleado" ){
} else{
    session_destroy();
    print "Articulos alta dice: no está user en session";
    header("Location: /index.php");
    exit;

}



//HEADER Y TITULO
include_once("header.php");
print("<h1>Gestionar artículos</h1>");

//NAVEGACION
include_once("../Controllers/OperacionesSession.php");
if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin" ||GetRolDeSession()=="empleado" ){
    print"
    <div id='EnlacesArriba'>
        <h2>
            <a href='ArticuloALTA.php'>
                <img class='iconArribaTabla' src='../Resources/addAr.png' alt='añadir' /> Nuevo artículo (solo admin y editores y empleados)
            </a>
        </h2>";
}
if(GetRolDeSession() == "admin" ){
    print"<h2>
            <a href='CategoriasLISTAR.php'>
                <img class='iconArribaTabla' src='../Resources/buscaAr.png' alt='añadir' /> Ver categorías
            </a>
        </h2>";
} else {
    include_once("../Controllers/GetEmailByDniController.php");
    $email = GetEmailDeSession();
    $dni=GetDniByEmail($email);
    if($dni == null ){
        $_SESSION['OperationFailed'] = true;
        print"<h2>
                <a href='ClienteBUSCAR.php'>
                    <img class='iconArribaTabla' src='../Resources/search.png' alt='añadir' /> Buscar cliente
                </a>
            </h2>";
    } else{
        print"<h2>
                <a href='ClienteEDITAR.php?dni=$dni'>
                    <img class='iconArribaTabla' src='../Resources/search.png' alt='añadir' /> Editar mis datos de usuario $email
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
print"<table>";
        print"<tr>";
            //ENCABEZADOS
            include_once("../Controllers/ArticulosLISTARController.php");
            $arrayAtributos = getArrayAtributosArticulo();
            if($arrayAtributos == false){
                print"</tr><tr><td>Sin articulos</td></tr>";
            } else{
                foreach ($arrayAtributos as $atributo) {
                    $nombreAtributo = $atributo;
                    print "<th>$nombreAtributo</th>";
                }
                include_once("../Controllers/OperacionesSession.php");//get rol
                if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin" || GetRolDeSession() == "empleado"){
                    print"
                    <th>Editar</th>
                    <th>Desactivar</th>";
                }
                print"</tr>";
            }

        //PREPARAR ARRAYS CON OBJETOS
        $orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
        include_once("../Controllers/OrdenarArticulosController.php");
        $arrayArticulos = getArrayArticulosOrdenados($orden);
        $itemXpagPredeterminado=3;
        $filasAMostrar = isset($_GET['itemXpag'])? $_GET['itemXpag'] : $itemXpagPredeterminado;
        if(! isset($_GET['pag'])){
            $paginaActual = 0;
        }else{
            if( is_numeric($_GET['pag'])){
                $paginaActual = $_GET['pag'] - 1 ;
            } else if ($_GET['pag'] == "X" ){
                $paginaActual = "X";
            }
        }

        include_once("../Controllers/ArticulosLISTARController.php");
        $arrayAImprimir = getArrayPaginadoArticulos($arrayArticulos, $filasAMostrar, $paginaActual);

        //DATOS DE LOS OBJETOS
        //llamamos dinámicamente los getters de la clase habiendo guardado previamente el array con los nombresd de los atributos
        //hay que recorrer todos los atributos en todos los objetos
        foreach($arrayAImprimir as $articulo){
            print("<tr>");
            foreach ($arrayAtributos as $atributo) {
                $nombreAtributo = $atributo;//p.e. codigo, nombre...
                $nombreMetodo = 'get' . ucfirst($nombreAtributo); //montamos el nombre del método a llamar
                $valor = call_user_func([$articulo, $nombreMetodo]);
                if($nombreAtributo == "codigo"){
                    $codigo = $articulo->getCodigo();//guardamos el código para que esté disponible fuerra de este bucle
                    print "<td>$valor</td>";
                } else if($nombreAtributo == "imagen"){
                    include_once("../Controllers/Directorio.php");
                    $directorio = "/Resources/ImagenesArticulos/";
                    $rutaAbsoluta = $directorio . $valor;
                    print"<td><img class='imagenes' src='{$rutaAbsoluta}' width='200' height='200'/></td>";
                }else{
                    print "<td>$valor</td>";
                }
            }
            if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin" || GetRolDeSession() == "empleado"){
                print"
                <td><a href='ArticuloEDITAR.php?codigo=$codigo'><img class='icon' src='../Resources/editAr.png' alt='Editar artículo' /></td>
                <td><a href='ArticuloBORRAR.php?codigo=$codigo'><img class='icon' src='../Resources/minusAr.png' alt='Borrar artículo' /></td>";
            }
        }
        print("</tr>
    </table>");

   //PAGINACIÓN
   print "<div class='paginacion'>";
   $filasTotales = count($arrayArticulos);
   $paginasTotales = ceil($filasTotales / $filasAMostrar);
   if(is_numeric($paginaActual) && is_numeric($filasAMostrar)){
       //estamos viendo los registros paginados
       //estamos al principio de la lista, además de lo anterior también imprimiremos "anterior"
       if($paginaActual == 0 ){
           print "<p>Anterior</p>"; //en la primera página esto no debe ser un enlace
       } else{
           print "<a href='ArticulosLISTAR.php?pag=".($paginaActual)."&ordenNombres=$orden&itemXpag=$filasAMostrar'>Anterior</a>";
       }
       for ($numeroIndicePaginacion = 1; $numeroIndicePaginacion <= $paginasTotales; $numeroIndicePaginacion++) {
           if($numeroIndicePaginacion == $paginaActual + 1 ){
               print "<b>$numeroIndicePaginacion</b>";
           }else{
               print "<a href='ArticulosLISTAR.php?pag=$numeroIndicePaginacion&ordenNombres=$orden&itemXpag=$filasAMostrar'>$numeroIndicePaginacion</a>";
           }
           if($paginaActual +1 == $paginasTotales && $numeroIndicePaginacion == $paginasTotales){
               print "<p>Siguiente</p>"; //en la primera página esto no debe ser un enlace
           }else if($numeroIndicePaginacion == $paginasTotales){
               print "<a href='ArticulosLISTAR.php?pag=".($paginaActual+2)."&ordenNombres=$orden&itemXpag=$filasAMostrar'>Siguiente</a>";
           } else{
               print "";//no printear nada
           }
       }
   } else{
       //estamos viendo todos los registros en una página
       for ($numeroIndicePaginacion = 1; $numeroIndicePaginacion <= $paginasTotales; $numeroIndicePaginacion++) {
           print "<a href='ArticulosLISTAR.php?pag=$numeroIndicePaginacion&ordenNombres=$orden&itemXpag=$filasAMostrar'>$numeroIndicePaginacion</a>";
       }
   }

   //FORMULARIO PIE DE PÁGINA PARA ELEGIR LA PÁGINA A VER, Nº registros/pág
   $opcionesitemXpag=[3,4,5];
   if (isset($_GET['pag']) && ( $_GET['pag'] == "X" ) ){
       print "<b>Ver todos</b>";
   } else{
       print "<a href='ArticulosLISTAR.php?pag=X&ordenNombres=$orden'>Ver todos</a>";
   }
   print "
   <form action='ArticulosLISTAR.php' method='GET'>
   <label for='itemXpag'>Registros/página</label><br>
   <select id='itemXpag' name='itemXpag' onchange='this.form.submit()' required>
       <option value='$filasAMostrar'>$filasAMostrar</option>";//mostrar la opción actual seleccionada
       for ($i = 0; $i < count($opcionesitemXpag); $i++) {
           if( $opcionesitemXpag[$i] == $filasAMostrar){
               continue;//no queremos imprimir de nuevo la opción que ya tienen seleccionada
           } else{
               print "<option value='$opcionesitemXpag[$i]'>$opcionesitemXpag[$i]</option>";
           }
       }
   print
   "</select><br>
   </form>
   </div>";

//SECCION DE IMPRIMIR MENSAJE DE ERROR/CONFIRMACIÓN
include_once("../Controllers/ArticulosMensajes.php");
$arrayMensajes=getArrayMensajesArticulos();
            if(is_array($arrayMensajes)){
                foreach($arrayMensajes as $mensaje) {
                    print "<h3>$mensaje</h3>";
                }
            };
//tras printear los mensajes de error/confirmación "reseteamos" session
include_once("../Controllers/OperacionesSession.php");
ResetearSesion();

?>
<h2><a class="cerrar"  href='/index.php'>Cerrar sesión</a></h2>
<?php
include_once("footer.php");
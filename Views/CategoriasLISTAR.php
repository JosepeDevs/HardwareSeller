<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
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



//HEADER Y TITULO
include_once("header.php");
print("<h1>Administrar categorias</h1>");

//NAVEGACION
include_once("../Controllers/OperacionesSession.php");
if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin" ||GetRolDeSession() == "empleado"){
    print"
    <div id='EnlacesArriba'>
        <h2>
            <a href='CategoriaALTA.php'>
                <img class='iconArribaTabla' src='../Resources/addAr.png' alt='añadir' /> Nueva categoría (solo admin y editores y empleados)
            </a>
        </h2>";
}
if(GetRolDeSession() == "admin" ){
    print"<h2>
            <a href='ArticulosLISTAR.php'>
                <img class='iconArribaTabla' src='../Resources/buscaAr.png' alt='añadir' /> Administrar artículos
            </a>
        </h2>";
} else {
    //solo entrará aquí si es editor o empleado
    print"<h2>
            <a href='CategoriaALTA.php'>
                <img class='iconArribaTabla' src='../Resources/addAr.png' alt='añadir' /> Crear categoría nueva
            </a>
        </h2>";
}
?>
<h2>
    <a href='CategoriasLISTAR.php'>
        <img  class='iconArribaTabla' src='../Resources/refresh.png' alt='refrescar' /> Recargar tabla (Quita ordenación y reinicia paginación)
    </a>
</h2>
<h2>
    <a href='CategoriaBUSCAR.php'>
        <img class='iconArribaTabla'  src="../Resources/buscaAr.png" alt="recraft icon"/> Buscar Categoria
    </a>
</h2>
</div>
<?php
//TABLA LISTANDO CategoriaS
print"<table>";
        print"<tr>";
            //ENCABEZADOS
            include_once("../Controllers/CategoriasLISTARController.php");
            $arrayAtributos = getArrayAtributosCategoria();
            if($arrayAtributos == false){
                print"</tr><tr><td>Sin Categorias</td></tr>";
            } else{
                foreach ($arrayAtributos as $atributo) {
                    $nombreAtributo = $atributo;
                    print "<th>$nombreAtributo</th>";
                }
                include_once("../Controllers/OperacionesSession.php");//get rol
                if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin"||GetRolDeSession() == "empleado" ){
                    print"
                    <th>Editar</th>
                    <th>Desactivar</th>";
                }
                print"</tr>";
            }

        //PREPARAR ARRAYS CON OBJETOS
        $orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
        include_once("../Controllers/OrdenarCategoriasController.php");
        $arrayCategorias = getArrayCategoriasOrdenados($orden);
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

        include_once("../Controllers/CategoriasLISTARController.php");
        $arrayAImprimir = getArrayPaginadoCategorias($arrayCategorias, $filasAMostrar, $paginaActual);

        //DATOS DE LOS OBJETOS
        //llamamos dinámicamente los getters de la clase habiendo guardado previamente el array con los nombresd de los atributos
        //hay que recorrer todos los atributos en todos los objetos
        foreach($arrayAImprimir as $Categoria) {
            print"<tr>";
            foreach ($arrayAtributos as $index => $atributo) {
                $nombreAtributo = $atributo;
                $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                $valor = $Categoria->$getter();//lo llamamos para obtener el valor
                if($nombreAtributo == "activo"){
                    if($valor == 1){
                        print "<td>Activo (1)</td>";
                    } else{
                        print "<td>Inactivo (0)</td>";
                    }
                } else if($nombreAtributo == "codigo"){
                    $codigo=$valor;
                    print "<td>$valor</td>";
                } else {
                    print "<td>$valor</td>";
                }
            }
            if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin" ||GetRolDeSession() == "empleado"){
                print"
                <td>
                    <a  href='CategoriaEDITAR.php?codigo=$codigo'>
                    <img class='icon' src='../Resources/editAr.png' alt='Editar Categoria' />
                </td>
                <td>
                    <a  href='CategoriaBORRAR.php?codigo=$codigo'>
                    <img class='icon' src='../Resources/minusAr.png' alt='Desactivar Categoria' />
                </td>";
            }
            print "</tr>";
        }
        print("</table>");

   //PAGINACIÓN
   print "<div class='paginacion'>";
   $filasTotales = count($arrayCategorias);
   $paginasTotales = ceil($filasTotales / $filasAMostrar);
   if(is_numeric($paginaActual) && is_numeric($filasAMostrar)){
       //estamos viendo los registros paginados
       //estamos al principio de la lista, además de lo anterior también imprimiremos "anterior"
       if($paginaActual == 0 ){
           print "<p>Anterior</p>"; //en la primera página esto no debe ser un enlace
       } else{
           print "<a href='CategoriasLISTAR.php?pag=".($paginaActual)."&ordenNombres=$orden&itemXpag=$filasAMostrar'>Anterior</a>";
       }
       for ($numeroIndicePaginacion = 1; $numeroIndicePaginacion <= $paginasTotales; $numeroIndicePaginacion++) {
           if($numeroIndicePaginacion == $paginaActual + 1 ){
               print "<b>$numeroIndicePaginacion</b>";
           }else{
               print "<a href='CategoriasLISTAR.php?pag=$numeroIndicePaginacion&ordenNombres=$orden&itemXpag=$filasAMostrar'>$numeroIndicePaginacion</a>";
           }
           if($paginaActual +1 == $paginasTotales && $numeroIndicePaginacion == $paginasTotales){
               print "<p>Siguiente</p>"; //en la primera página esto no debe ser un enlace
           }else if($numeroIndicePaginacion == $paginasTotales){
               print "<a href='CategoriasLISTAR.php?pag=".($paginaActual+2)."&ordenNombres=$orden&itemXpag=$filasAMostrar'>Siguiente</a>";
           } else{
               print "";//no printear nada
           }
       }
   } else{
       //estamos viendo todos los registros en una página
       for ($numeroIndicePaginacion = 1; $numeroIndicePaginacion <= $paginasTotales; $numeroIndicePaginacion++) {
           print "<a href='CategoriasLISTAR.php?pag=$numeroIndicePaginacion&ordenNombres=$orden&itemXpag=$filasAMostrar'>$numeroIndicePaginacion</a>";
       }
   }

   //FORMULARIO PIE DE PÁGINA PARA ELEGIR LA PÁGINA A VER, Nº registros/pág
   $opcionesitemXpag=[3,4,5];
   if (isset($_GET['pag']) && ( $_GET['pag'] == "X" ) ){
       print "<b>Ver todos</b>";
   } else{
       print "<a href='CategoriasLISTAR.php?pag=X&ordenNombres=$orden'>Ver todos</a>";
   }
   print "
   <form action='CategoriasLISTAR.php' method='GET'>
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
include_once("../Controllers/CategoriaMensajes.php");
            $arrayMensajes=getArrayMensajesCategorias();
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
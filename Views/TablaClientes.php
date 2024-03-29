<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}

include_once("../Controllers/OperacionesSession.php");

$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "TablaClientes dice: no está user en session";
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
$rol=GetRolDeSession();

include("header.php");

print("
    <h1>Gestionar clientes</h1>
        <div id='EnlacesArriba'>
            <h2><a class='enlace' href='ClienteALTA.php'><img class='iconArribaTabla' src='../Resources/add.png' alt='add user' /> Nuevo cliente</h2></a>
            <h2><a class='enlace' href='TablaClientes.php'><img class='iconArribaTabla' src='../Resources/refresh.png' alt='refresh' /> Recargar tabla (resetea filtros y paginación)</h2></a>
            <h2><a class='enlace' href='ClienteBUSCAR.php'><img class='iconArribaTabla' src='../Resources/search.png' alt='search user'/> Buscar cliente</h2></a>
            <h2><a class='enlace' href='AreaCliente.php'><img class='iconArribaTabla' src='../Resources/buscaAr.png' alt='view products'/> Volver al área de cliente</h2></a>
        </div>
");

    //TABLA DE CLIENTES

    print"<table>
    <tr>";
    //ENCABEZADOS TABLA
        include_once("../Controllers/TablaClientesController.php");
        $arrayAtributos = getArrayAtributosCliente();
        foreach ($arrayAtributos as $nombreAtributo) {
            if($nombreAtributo == "nombre"){
            print"<th>
                    Nombre <br>Ordenar:<br>
                    <a class='ordenar' href='?ordenNombres=ASC'>A->Z</a>
                    <a class='ordenar' href='?ordenNombres=DESC'>Z->A</a>
                </th>";
            }else{
                print"<th>$nombreAtributo</th>";
            }
        }
        print"<th>Editar</th>
            <th>Desactivar</th>
    </tr>";

        //PREPARAR ARRAYS CON OBJETOS
        $orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
        $arrayClientes = getArrayClientesOrdenados($orden);
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

        include_once("../Controllers/TablaClientesController.php");
        $arrayAImprimir = getArrayPaginado($arrayClientes,$filasAMostrar,$paginaActual);

        //IMPRIMIR DATOS OBJETOS
        foreach($arrayAImprimir as $cliente){
            print("<tr>");
            foreach ($arrayAtributos as $atributo) {
                $nombreMetodo = 'get' . ucfirst($atributo); //montamos el nombre del método a llamar
                $valor = call_user_func([$cliente, $nombreMetodo]);
                if($atributo == "psswrd"){
                    print"<td>****</td>";
                }else if($atributo == "dni"){
                    print "<td>$valor</td>";
                    $dni=$valor;
                }else if($atributo == "activo"){
                    if($valor == 0){
                        print "<td>Inactivo</td>";
                    }
                    if($valor == 1){
                        print "<td>Activo</td>";
                    }
                } else {
                    print "<td>$valor</td>";
                }
                if(next($arrayAtributos) == false && ( $rol == "empleado" || $rol == "admin" )){ //si ya no quedan más atributos que imprimir y el rol es correcto
                print"
                <td><a class='icon' href='ClienteEDITAR.php?dni=$dni&rol4consulta=administradormaestro'><img src='../Resources/edit.png' alt='Editar cliente' /></td>
                <td><a class='icon' href='ClienteBORRAR.php?dni=$dni'><img src='../Resources/delete.png' alt='Borrar cliente' /></td>";
                }
            }
        }
        print("</tr>
    </table>");

        //PAGINACIÓN
        print "<div class='paginacion'>";
        $filasTotales = count($arrayClientes);
        $paginasTotales = ceil($filasTotales / $filasAMostrar);
        if(is_numeric($paginaActual) && is_numeric($filasAMostrar)){
            //estamos viendo los registros paginados
            //estamos al principio de la lista, además de lo anterior también imprimiremos "anterior"
            if($paginaActual == 0 ){
                print "<p>Anterior</p>"; //en la primera página esto no debe ser un enlace
            } else{
                print "<a href='?pag=".($paginaActual)."&ordenNombres=$orden&itemXpag=$filasAMostrar'>Anterior</a>";
            }
            for ($numeroIndicePaginacion = 1; $numeroIndicePaginacion <= $paginasTotales; $numeroIndicePaginacion++) {
                if($numeroIndicePaginacion == $paginaActual + 1 ){
                    print "<b>$numeroIndicePaginacion</b>";
                }else{
                    print "<a href='?pag=$numeroIndicePaginacion&ordenNombres=$orden&itemXpag=$filasAMostrar'>$numeroIndicePaginacion</a>";
                }
                if($paginaActual +1 == $paginasTotales && $numeroIndicePaginacion == $paginasTotales){
                    print "<p>Siguiente</p>"; //en la primera página esto no debe ser un enlace
                }else if($numeroIndicePaginacion == $paginasTotales){
                    print "<a href='?pag=".($paginaActual+2)."&ordenNombres=$orden&itemXpag=$filasAMostrar'>Siguiente</a>";
                } else{
                    print "";//no printear nada
                }
            }
        } else{
            //estamos viendo todos los registros en una página
            for ($numeroIndicePaginacion = 1; $numeroIndicePaginacion <= $paginasTotales; $numeroIndicePaginacion++) {
                print "<a href='?pag=$numeroIndicePaginacion&ordenNombres=$orden&itemXpag=$filasAMostrar'>$numeroIndicePaginacion</a>";
            }
        }

        //FORMULARIO PIE DE PÁGINA PARA ELEGIR LA PÁGINA A VER, Nº registros/pág
        $opcionesitemXpag=[3,4,5];
        if (isset($_GET['pag']) && ( $_GET['pag'] == "X" ) ){
            print "<b>Ver todos</b>";
        } else{
            print "<a href='?pag=X&ordenNombres=$orden'>Ver todos</a>";
        }
        print "
        <form action='TablaClientes.php' method='GET'>
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
        include_once("../Controllers/TablaClientesMensajes.php");//para ver mensajes

        $arrayMensajes=getArrayMensajesTabla();
        if(is_array($arrayMensajes)){
            foreach($arrayMensajes as $mensaje) {
                print "<h3>$mensaje</h3>";
            }
        };

        //tras printear los mensajes de error/confirmación "reseteamos" session
       // ResetearSesion();

        ?>
<h2><a class="cerrar"  href='/index.php'>Cerrar sesión</a></h2>
<?php
include("footer.php");
?>
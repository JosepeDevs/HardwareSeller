<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "TablaClientes dice:  no está user en session";
    header("Location: index.php");
}

include("header.php");
include_once("/../Controllers/UserSession.php");


include_once("/../Controllers/CheckRol.php");
if( AuthYRolAdmin() == false){
    session_destroy();
    echo " el rol no era adecuado";
    header("Location: index.php");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Listar clientes</title>
    <link rel="stylesheet" type="text/css" href="estilosTabla.css">
</head>
<body>
    <h1>Gestionar clientes</h1>
        <div id="EnlacesArriba">
            <h2><a class='enlace' href='clientenuevo.php'><img class='iconArribaTabla' src='add.png' alt='add user' /> Nuevo cliente</h2></a>
            <h2><a class='enlace' href='TablaClientes.php'><img class='iconArribaTabla' src='refresh.png' alt='refresh' /> Recargar tabla (super útil, no te lo creerás)</h2></a>
            <h2><a class='enlace' href='BuscarCliente.php'><img class='iconArribaTabla' src="search.png" alt="search user"/> Buscar cliente</h2></a>
            <h2><a class='enlace' href='ArticulosLISTAR.php'><img class='iconArribaTabla' src="buscaAr.png" alt="view products"/> Ver listado de productos</h2></a>
        </div>

<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}

    //TABLA DE CLIENTES

    echo"<table>
    <tr>";
    //ENCABEZADOS TABLA
        include_once("/../Controllers/TablaClientesController.php");
        $arrayAtributos = getArrayAtributos();
        foreach ($arrayAtributos as $atributo) {
            $nombreAtributo = $atributo->getName();
            if($nombreAtributo == "nombre"){
            echo"<th>
                    Nombre <br>Ordenar:<br>
                    <a class='ordenar' href='TablaClientes.php?ordenNombres=ASC'>A->Z</a>
                    <a class='ordenar' href='TablaClientes.php?ordenNombres=DESC'>Z->A</a>
                </th>";
            }else{
                echo"<th>$nombreAtributo</th>";
            }
            echo"<th>Editar</th>
                <th>Borrar</th>";
        }
            echo"
    </tr>";

        //PREPARAR ARRAYS CON OBJETOS
        $orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
        $arrayClientes = getArrayClientesOrdenados($orden);
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

        //include_once("/../Controllers/TablaClientesController.php");
        $arrayAImprimir = getArrayPaginado($arrayClientes,$filasAMostrar,$paginaActual);

        //IMPRIMIR DATOS OBJETOS
        foreach($arrayAImpimir as $cliente){
            echo("<tr>");
            foreach ($arrayAtributos as $atributo) {
                $nombreAtributo = $atributo->getName();//p.e. dni, nombre...
                $nombreMetodo = 'get' . ucfirst($nombreAtributo); //montamos el nombre del método a llamar
                $valor = call_user_func([$cliente, $nombreMetodo]);
                if($nombreAtributo == "psswrd"){
                    echo"<p>****</p>";
                }else{
                    echo "<td>$valor</td>";
                }
            }
            if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin"){
                echo"
                <td><a class='icon' href='editarcliente.php?dni=$dni&rol4consulta=administradormaestro'><img src='edit.png' alt='Editar cliente' /></td>
                <td><a class='icon' href='borrarcliente.php?dni=$dni'><img src='delete.png' alt='Borrar cliente' /></td>";
            }
        }
        echo("</tr>
    </table>");

        //PAGINACIÓN
        echo "<div class='paginacion'>";
        $filasTotales = count($arrayClientes);
        $paginasTotales = ceil($filasTotales / $filasAMostrar);
        if(is_numeric($paginaActual) && is_numeric($filasAMostrar)){
            //estamos viendo los registros paginados
            //estamos al principio de la lista, además de lo anterior también imprimiremos "anterior"
            if($paginaActual == 1){
                echo "<p>Anterior</p>"; //en la primera página esto no debe ser un enlace
            } else{
                echo "<a href='ArticulosLISTAR.php?pag=".($paginaActual-1)."&ordenNombres=$orden&numpag=$filasAMostrar'>Anterior</a>";
            }
            for ($p = 1; $p <= $paginasTotales; $p++) {
                if($p == $paginaActual +1 ){
                    echo "<b>$p</b>";
                }else{
                    echo "<a href='TablaClientes.php?pag=$p&ordenNombres=$orden&numpag=$filasAMostrar'>$p</a>";
                }
                if($p == $paginasTotales && $paginaActual == $p){ //si hemos llegado al final del bucle ahora NO habría que imprimir "siguiente" como enlace si la página actual  es la última
                    echo "<p>Siguiente</p>"; //hemos llegado al final de la lista de páginas y estamos en la última página.
                } else{
                    echo "<a href='ArticulosLISTAR.php?pag=".($paginaActual+1)."&ordenNombres=$orden&numpag=$filasAMostrar'>Siguiente</a>";
                }
            }
        } else{
            //estamos viendo todos los registros en una página
            for ($p = 1; $p <= $paginasTotales; $p++) {
                echo "<a href='TablaClientes.php?pag=$p&ordenNombres=$orden&numpag=$filasAMostrar'>$p</a>";
            }
        }

        //FORMULARIO PIE DE PÁGINA PARA ELEGIR LA PÁGINA A VER, Nº registros/pág
        $opcionesNumPag=[3,4,5];
        if (isset($_GET['pag']) && ( $_GET['pag'] == "X" ) ){
            echo "<b>Ver todos</b>";
        } else{
            echo "<a href='TablaClientes.php?pag=X&ordenNombres=$orden'>Ver todos</a>";
        }
        echo "
        <form action='TablaClientes.php' method='GET'>
        <label for='numpag'>Registros/página</label><br>
        <select id='numpag' name='numpag' onchange='this.form.submit()' required>
            <option value='$filasAMostrar'>$filasAMostrar</option>";
            for ($i = 1; $i < count($opcionesNumPag); $i++) {
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
        include_once("/.../Controllers/tablaClientesMensajes.php");//para ver mensajes

        $arrayMensajes=getArrayMensajesTabla();
        if(is_array($arrayMensajes)){
            foreach($arrayMensajes as $mensaje) {
                echo "<h3>$mensaje</h3>";
            }
        };

        //tras printear los mensajes de error/confirmación "reseteamos" session
        include_once("/../Controllers/ResetSession.php");//para el reset de session
            ResetSession();
include("footer.php");
            ?>

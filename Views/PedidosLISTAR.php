<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "PedidosLISTAR dice: no está user en session";
    header("Location: ../index.php");
}
//HEADER Y TITULO
include_once("header.php");
print("<h1>Lista de Pedidos</h1>");
$rol = GetRolDeSession();
$dni = GetDniByEmail($_SESSION['user']);
//NAVEGACION
echo"<div id='EnlacesArriba'>";
if(GetRolDeSession() == "empleado" || GetRolDeSession() == "admin" ){
    ?>

    <h2>
        <a href='TablaClientes.php'>
            <img class='iconArribaTabla' src='../Resources/search.png' alt='añadir' /> Ver Clientes
        </a>
    </h2>
    <?
}
?>
    <h2>
        <a href='PedidosLISTAR.php'>
            <img  class='iconArribaTabla' src='../Resources/refresh.png' alt='refrescar' /> Limpiar filtros
        </a>
    </h2>
    <h2>
        <a href='PedidoBUSCAR.php'>
            <img class='iconArribaTabla'  src="../Resources/buscaAr.png" alt="recraft icon"/> Buscar Pedido
        </a>
    </h2>
<?
echo"</div>";
//TABLA LISTANDO Pedidos
echo"<table>";
        echo"<tr>";
            //ENCABEZADOS
            include_once("../Controllers/PedidosLISTARController.php");
            $arrayAtributos = getArrayAtributosPedido();
            if($arrayAtributos == false){
                echo"</tr><tr><td>Sin Pedidos</td></tr>";
            } else{
                foreach ($arrayAtributos as $atributo) {
                    $nombreAtributo = $atributo;
                    if($nombreAtributo == "estado"){
                        echo"<th>
                                $nombreAtributo 
                                <br>Ordenar por este atributo:<br>
                                <a class='ordenar' href='PedidosLISTAR.php?orden=ASC&atributo=$nombreAtributo'>ASC</a>
                                <a class='ordenar' href='PedidosLISTAR.php?orden=DESC&atributo=$nombreAtributo'>DESC</a>
                            </th>";
                    } else if(( $rol !== "admin" || $rol !== "empleado" ) && ( $nombreAtributo == "activo" ||$nombreAtributo == "codUsuario" ) ){
                        echo'';//si no es admin o empleado tanto el atributo activo como coduusuario no se muestran a rol=user
                    } else{
                        echo"<th>
                        $nombreAtributo <br>Ordenar por este atributo:<br>
                        <a class='ordenar' href='PedidosLISTAR.php?orden=ASC&atributo=$nombreAtributo'>ASC</a>
                        <a class='ordenar' href='PedidosLISTAR.php?orden=DESC&atributo=$nombreAtributo'>DESC</a>
                        </th>";

                    }
                }
                include_once("../Controllers/OperacionesSession.php");//get rol
                if(GetRolDeSession() == "editor" || GetRolDeSession() == "admin" ){
                    echo"
                    <th>Ver contenido</th>
                    <th>Editar</th>
                    <th>Desactivar</th>";
                }
                echo"</tr>";
            }

        //PREPARAR ARRAYS CON OBJETOS
        $orden = isset($_GET['orden']) ? $_GET['orden']:null;
        $atributoElegido = isset($_GET["atributo"])?$_GET["atributo"]:"idPedido";
        include_once("../Controllers/OrdenarPedidosController.php");

        if( $rol == "admin" || $rol == "empleado" ){
            $arrayPedidos = getArrayPedidosOrdenadosByAtributo($orden,$atributoElegido);
        } else{
            $arrayPedidos = getArrayPedidosOrdenadosByAtributo($orden,$atributoElegido, $dni);
        }

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

        include_once("../Controllers/PedidosLISTARController.php");
        $arrayAImprimir = getArrayPaginadoPedidos($arrayPedidos, $filasAMostrar, $paginaActual);

        //DATOS DE LOS OBJETOS
        //llamamos dinámicamente los getters de la clase habiendo guardado previamente el array con los nombresd de los atributos
        //hay que recorrer todos los atributos en todos los objetos
        foreach($arrayAImprimir as $Pedido){
            echo("<tr>");
            foreach ($arrayAtributos as $atributo) {
                $nombreAtributo = $atributo;//p.e. codigo, nombre...
                $nombreMetodo = 'get' . ucfirst($nombreAtributo); //montamos el nombre del método a llamar
                $valor = call_user_func([$Pedido, $nombreMetodo]);
                if($nombreAtributo == "idPedido"){
                    $idPedido = $Pedido->getidPedido();//guardamos el código para que esté disponible fuera de este bucle
                    echo "<td><a href='ContenidoPedidoBUSCAR.php?numPedido=".$idPedido.">".$valor."</a></td>";
                } else if( ( $rol !== "admin" || $rol !== "empleado" ) && ( $nombreAtributo == "activo" ||$nombreAtributo == "codUsuario" ) ){
                    echo'';//si no es admin o empleado tanto el atributo activo como coduusuario no se muestran a rol=user
                }else{
                    echo "<td><a href='ContenidoPedidoBUSCAR.php?numPedido=".$idPedido.">".$valor."</a></td>";
                }
            }
            if(GetRolDeSession() == "admin"){
                echo"
                <td><a href='ContenidoPedidoEDITAR.php?idPedido=$idPedido'><img class='icon' src='../Resources/editAr.png' alt='Editar artículo' /></td>
                <td><a href='PedidoBORRAR.php?idPedido=$idPedido'><img class='icon' src='../Resources/minusAr.png' alt='Borrar artículo' /></td>";
            }
        }
        echo("</tr>
        <tr>
            <td colspan='5'>  El estado puede tener más de 1 digito, por ejemplo: 1 es que esta en el carrito y nada más, 235 es que el pedio es en firme pagará por transferencia y 
                ya hemos recibido el dinero pero aun no se ha enviado. 2356 mismo caso que el anterior pero este sí se ha enviado ya (no ha llegado aun). Todos los pedidos idealmente acabarán en 8 (235678)
                <br>
                (0=cancelado)(1=pedido en carrito)(2=pedido realizado)(3=pago por transferencia)(4= pago por tarjeta)(5=pago confirmado)
                (6=pedido enviado)(7=pedido recibido)(8=finalizado)(9=finalizado con incidencia)                            
            </td>
        </tr>
    </table>");

   //PAGINACIÓN
   print "<div class='paginacion'>";
   $filasTotales = count($arrayPedidos);
   $paginasTotales = ceil($filasTotales / $filasAMostrar);
   if(is_numeric($paginaActual) && is_numeric($filasAMostrar)){
       //estamos viendo los registros paginados
       //estamos al principio de la lista, además de lo anterior también imprimiremos "anterior"
       if($paginaActual == 0 ){
           print "<p>Anterior</p>"; //en la primera página esto no debe ser un enlace
       } else{
           print "<a href='PedidosLISTAR.php?pag=".($paginaActual)."&ordenNombres=$orden&itemXpag=$filasAMostrar'>Anterior</a>";
       }
       for ($numeroIndicePaginacion = 1; $numeroIndicePaginacion <= $paginasTotales; $numeroIndicePaginacion++) {
           if($numeroIndicePaginacion == $paginaActual + 1 ){
               print "<b>$numeroIndicePaginacion</b>";
           }else{
               print "<a href='PedidosLISTAR.php?pag=$numeroIndicePaginacion&ordenNombres=$orden&itemXpag=$filasAMostrar'>$numeroIndicePaginacion</a>";
           }
           if($paginaActual +1 == $paginasTotales && $numeroIndicePaginacion == $paginasTotales){
               print "<p>Siguiente</p>"; //en la primera página esto no debe ser un enlace
           }else if($numeroIndicePaginacion == $paginasTotales){
               print "<a href='PedidosLISTAR.php?pag=".($paginaActual+2)."&ordenNombres=$orden&itemXpag=$filasAMostrar'>Siguiente</a>";
           } else{
               print "";//no printear nada
           }
       }
   } else{
       //estamos viendo todos los registros en una página
       for ($numeroIndicePaginacion = 1; $numeroIndicePaginacion <= $paginasTotales; $numeroIndicePaginacion++) {
           print "<a href='PedidosLISTAR.php?pag=$numeroIndicePaginacion&ordenNombres=$orden&itemXpag=$filasAMostrar'>$numeroIndicePaginacion</a>";
       }
   }

   //FORMULARIO PIE DE PÁGINA PARA ELEGIR LA PÁGINA A VER, Nº registros/pág
   $opcionesitemXpag=[3,4,5];
   if (isset($_GET['pag']) && ( $_GET['pag'] == "X" ) ){
       print "<b>Ver todos</b>";
   } else{
       print "<a href='PedidosLISTAR.php?pag=X&ordenNombres=$orden'>Ver todos</a>";
   }
   print "
   <form action='PedidosLISTAR.php' method='GET'>
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
include_once("../Controllers/PedidosMensajes.php");
            $arrayMensajes=getArrayMensajesPedidos();
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
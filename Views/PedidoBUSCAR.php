<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
include_once("../Controllers/GetDniByEmailController.php");
$rol = GetRolDeSession();
//las funciones ya filtran para que las busquedas solo les devuelvan sus propios datos, con proteger para que solo users entren basta
//NO PROTEGER ESTO o si no las compras realizadas sin registrarse no tendrán la vista de confirmación

include_once("header.php");
//BREADCRUMBS AREA CLIENTE
include_once("BreadCrumbsAreaCliente.php");
print('<br>');
//DISTINTAS VISTAS SEGÚN ENLACE
if(isset($_GET["PedidoConfirmado"])){
    //llegan de hacer un pedido, vamos a mostrarles los datos del pedido y darles instrucciones para próximos pasos
    print'
    <br><br>
    <h1>
        Pedido recibido
    </h1>
    <br><br>
        <h2>Imporante, al hacer la transferencia indique el número de Pedido: "'.$_GET["idPedido"].'". ¡Gracias!</h2>
        <br>
        <h3>Nuestros datos bancarios son: ES0000-1111-2222-3333 ; Titular= HardWare Seller, S.I. (Sociedad Inexistente)</h3>
        <br>
        <h4> Desde el momento de la transferencia tardará algunos días que se actualice el estado del pedido como pago recibido, si transcurren más de 3 días
        y no cambiara el estado, por favor, contáctenos para revisarlo.
        </h4>
        <h4>Puede encontrar estos mismos datos bancarios en nuestra página "sobre nosotros"</h4>
        <br>
        <p> Nuestra dirección: Calle existente nº infinito, avenida de la indeterminación/0 , CP 00000, Elche, Alicante, España, Europa, Tierra, Universo #3 </p>
        <br>
        <a href="PedidosLISTAR.php">Ver  mis pedidos</a>
        ';

} else if(!empty($_GET) && !isset($_GET["PedidoConfirmado"])) {
    //han buscado un pedido, dar opción de buscar otro
    print'
    <h1>
        Datos del pedido
    </h1>';
    print'<br><h2><a class="finForm" href="PedidoBUSCAR.php?"><img src="../Resources/arrow.png" alt="listar ContenidoPedido" />Buscar otro pedido</a></h2>
    <br>';
}else{
    print'
    <h1>
        Buscar pedido ...
    </h1>
    <form action="PedidoBUSCAR.php" method="POST">
        <table>
            <tr>
                <th><h2><label for="idPedido">Id pedido</label></h2></th>
                <th><h2><label for="fecha">Fecha inicio</label></h2></th>
                <th><h2><label for="fecha">Fecha fin</label></h2></th>
                <th><h2><label for="estado">Busqueda por estado<br> Estado del pedido:(0=pedido en carrito (pendiente implementación)(1=envío a direccion dew cliente)(2=pedido realizado)(3=pago por transferencia)(4= pago por tarjeta)(5=pago y recogida en tienda)
                (6=pago confirmado)(7=pedido enviado)(8=pedido recibido)(9=finalizado o cancelado)</label></h2></th>';
            if($rol == "admin" || $rol == "empleado"){
                print'<th><h2><label for="codUsuario">Codigo usuario (DNI)</label></h2></th>';
            }
            print'</tr>
            <tr>
                <td><input type="text" name="idPedido" ><br><br></td>
                <td><input type="date" name="fechaInicio" autofocus><br><br></td>
                <td><input type="date" name="fechaFin" ><br><br></td>
                <td><input type="text" name="estado"><br><br></td>';
                if($rol == "admin" || $rol == "empleado"){
                    print'<td><input type="text" name="codUsuario"><br><br></td>';
                }        
           print' </tr>
        </table>

        <br>
        <div>
            <h2><input type="submit" value="Consultar"></h2><br><br><br>
        </div>
    </form>

';
}
print'<br>';

//que consiga el dni de alguna de estas dos formas, por user (registrados y logeados) o de session dni (sin registrrarse)
if(isset($_SESSION['user'])){
    $dni = GetDniByEmail($_SESSION['user']);
}
if(isset($_SESSION['dni'])){
    $dni = $_SESSION['dni'];
} else if(isset($_SESSION['user'])){
    $email=$_SESSION['user'];
    $dni=GetDniByEmail($email);
}  else{
    $dni="dni no encontrado";
}

if(!isset($_GET['PedidoConfirmado']) && !isset($_SESSION['user']) && empty($_SESSION['user']) ){
    //si no están logeados, solo dejamos que vean la págian de PedidoConfirmado 
    print "<script>history.back();</script>";
    exit;
}


//////////////FORMAS DE BUSCAR PEDIDOS////////////////////////////
if( isset($_REQUEST["idPedido"]) || isset($_REQUEST["fechaInicio"]) ||isset($_REQUEST["fechaFin"]) || isset($_REQUEST["codUsuario"]) || isset($_REQUEST["estado"]) ) {
    $idPedido=null;//mejor null que sin declarar de todas formas lo voy a guardar dentro de nada con lo que tengamos en el GET
    $codUsuario=null;//mejor null que sin declarar de todas formas lo voy a guardar dentro de nada con lo que tengamos en el GET
    include_once("../Controllers/PedidoBUSCARController.php");

    //ya existe listar pedidos para ver todos los pedidos, así que si ambas fechas están vacias no se buscará por fecha
    if( isset($_REQUEST['fechaInicio']) && isset($_REQUEST['fechaFin']) && (!empty($_REQUEST["fechaInicio"]) || !empty( $_REQUEST["fechaFin"]) ) ) {
        $fechaInicioPredeterminada = "1990/01/01";
        $fechaFinPredeterminada = "2100/01/01";
        empty($_REQUEST["fechaInicio"]) ? $fechaInicio = $fechaInicioPredeterminada : $fechaInicio = $_REQUEST['fechaInicio'] ; 
        empty($_REQUEST["fechaFin"]) ? $fechaFin = $fechaFinPredeterminada : $fechaFin = $_REQUEST['fechaFin'] ; 
        if( $rol == "admin" || $rol == "empleado" ){
            $arrayPedido = GetPedidosByRangoFecha($fechaInicio,$fechaFin);
        } else{
            $arrayPedido = GetPedidosByRangoFecha($fechaInicio,$fechaFin, $dni);
        }
        if($arrayPedido == false){
            $_SESSION['fechaNotFound'] = true;
        }
    } else if(!empty(($_REQUEST["idPedido"]))){
        $idPedido=$_REQUEST["idPedido"];
        if( $rol == "admin" || $rol == "empleado" ){
            $arrayPedido[] = getPedidoByIdPedido($idPedido);
        } else{
            $arrayPedido[] = getPedidoByIdPedido($idPedido, $dni);
        }
        if($arrayPedido == false){
            $_SESSION['idPedidoNotFound'] = true;
        }
    } else if(!empty(($_REQUEST["numPedido"]))){
        $numPedido=$_REQUEST["numPedido"];
        if( $rol == "admin" || $rol == "empleado" ){
            $arrayPedido[] = getPedidoByIdPedido($numPedido);
        } else{
            $arrayPedido[] = getPedidoByIdPedido($numPedido, $dni);
        }
        if($arrayPedido == false){
            $_SESSION['idPedidoNotFound'] = true;
        }
    } else if(!empty(($_REQUEST["codUsuario"]))){
        $codUsuario=$_REQUEST["codUsuario"];
        if( $rol == "admin" || $rol == "empleado" ){
            $arrayPedido = getPedidosByCodUsuario($codUsuario);
        } else{
            $arrayPedido = getPedidosByCodUsuario($codUsuario, $dni);
        }
        if($arrayPedido == false){
            $_SESSION['codUsuarioNotFound'] = true;
        }
    } else if(!empty( $_REQUEST['estado'])) {
        $estado=$_REQUEST["estado"];
        if( $rol == "admin" || $rol == "empleado" ){
            $arrayPedido = getPedidosByEstado($estado);
        } else{
            $arrayPedido = getPedidosByEstado($estado, $dni);
        }
        if($arrayPedido == false){
            $_SESSION['estadoNotFound'] = true;
        }
    } else{
        $arrayPedido = false;
    }
 
    if(isset($arrayPedido[0]) && ($arrayPedido[0] == "")){
        //si llega un array con el indice 0 pero dentro hay texto vacio es que no encontró pedidos, lo ponemos como false para que diga que no se encontraron pedidos
        $arrayPedido = false;
    }
 
    $arrayAtributos = getArrayAtributosPedido();
    if( $arrayPedido !== false ) {
        print"<table>";
        print"<tr>";
        //ENCABEZADOS
        foreach ($arrayAtributos as $atributo) {
             if(( $rol == "user") && ( $atributo == "activo" || $atributo == "codUsuario" ) ){
                print'';//si es user tanto el atributo "activo" como "coduusuario" no se muestran 
            }else{
                print "<th>$atributo</th>";
            }
        }
        print "</tr>";
        //DATOS DEL OBJETO O LOS OBJETOS
            foreach($arrayPedido as $Pedido){
                print "<tr>";
                foreach ($arrayAtributos as $atributo) {
                    $getter = 'get' . ucfirst($atributo);
                    $valor = $Pedido->$getter();
                    if($atributo == "idPedido"){
                        $idPedido = $Pedido->getIdPedido();//guardamos el código para que esté disponible fuera de este bucle
                        print "<td>".$valor."</td>";
                    } else if(( $rol == "user" ) && ( $atributo == "activo" ||$atributo == "codUsuario" ) ){
                        print'';//si es user tanto el atributo "activo" como "coduusuario" no se muestran a rol=user
                    }else{
                        print "<td>".$valor."</td>";
                    }
                }
                print "</tr>";
            }
        print "</table>";
    } else{
        print "<h3>No se encontraron pedidos con los datos proporcionados.</h3>";
    }

    include_once("../Controllers/PedidosMensajes.php");
    $arrayMensajes=getArrayMensajesPedidos();
    if(is_array($arrayMensajes)){
        foreach($arrayMensajes as $mensaje) {
            print "<h3>$mensaje</h3>";
        }
    };
}


if(isset($_REQUEST['idPedido']) && ( $rol == "admin" || $rol == "empleado") ){
    print'
        <h2><a class="finForm" href="ContenidoPedidoBUSCAR.php?numPedido='.$idPedido.'"><img src="../Resources/arrow.png" alt="listar Pedido" />Ver contenidos de este pedido</a></h2>
    ';
}

include_once("footer.php");
?>
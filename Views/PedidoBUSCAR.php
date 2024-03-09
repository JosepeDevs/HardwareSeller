<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
include_once("../Controllers/GetDniByEmailController.php");

//las funciones ya filtran para que las busquedas solo les devuelvan sus propios datos, con proteger para que solo users entren basta
//NO PROTEGER ESTO o si no las compras realizadas sin registrarse no tendrán la vista de confirmación

include_once("header.php");
//BREADCRUMBS AREA CLIENTE
include_once("BreadCrumbsAreaCliente.php");

print_r($_SESSION);

//DISTINTAS VISTAS SEGÚN ENLACE
if(isset($_GET["PedidoConfirmado"])){
    //llegan de hacer un pedido, vamos a mostrarles los datos del pedido y darles instrucciones para próximos pasos
    echo'
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
        <br><br>
        <a href="PedidosLISTAR.php">Ver  mis pedidos</a>
        ';

} else if(!empty($_GET) && !isset($_GET["PedidoConfirmado"])) {
    //han buscado un pedido, dar opción de buscar otro
    echo'
    <h1>
        Datos del pedido
    </h1>';
    echo'<br><h2><a class="finForm" href="PedidoBUSCAR.php?"><img src="../Resources/arrow.png" alt="listar ContenidoPedido" />Buscar otro pedido</a></h2>
    <br>';
}else{
    echo'
    <h1>
        Buscar pedido ...
    </h1>
    <form action="PedidoBUSCAR.php" method="POST">
        <table>
            <tr>
                <th><h2><label for="idPedido">Id pedido</label></h2></th>
                <th><h2><label for="fecha">Fecha inicio</label></h2></th>
                <th><h2><label for="fecha">Fecha fin</label></h2></th>
                <th><h2><label for="estado">Busqueda por estado<br> Estado del pedido:(0=envío a direccion)(1=pedido en carrito)(2=pedido realizado)(3=pago por transferencia)(4= pago por tarjeta)(5=pago y recogida en tienda)
                (6=pago confirmado)(7=pedido enviado)(8=pedido recibido)(9=finalizado o cancelado)</label></h2></th>
                <th><h2><label for="codUsuario">Codigo usuario (DNI)</label></h2></th>
            </tr>
            <tr>
                <td><input type="text" name="idPedido" ><br><br></td>
                <td><input type="date" name="fechaInicio" autofocus><br><br></td>
                <td><input type="date" name="fechaFin" ><br><br></td>
                <td><input type="number" name="estado"><br><br></td>
                <td><input type="text" name="codUsuario" ><br><br></td>
            </tr>
        </table>

        <br>
        <div>
            <h2><input type="submit" value="Consultar"></h2><br><br><br>
        </div>
    </form>

';
}
echo'<br>';
$rol = GetRolDeSession();
$dni = GetDniByEmail($_SESSION['user']);

if( isset($_REQUEST["idPedido"]) || isset($_REQUEST["fechaInicio"]) ||isset($_REQUEST["fechaFin"]) || isset($_REQUEST["codUsuario"]) ) {
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
            print_r($arrayPedido);
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


    $arrayAtributos = getArrayAtributosPedido();
    if( $arrayPedido !== false){
        print_r($arrayPedido);
        echo"<table>";
        echo"<tr>";
        //ENCABEZADOS
        foreach ($arrayAtributos as $atributo) {
             if(( $rol == "user") && ( $atributo == "activo" ||$atributo == "codUsuario" ) ){
                echo'';//si es user tanto el atributo "activo" como "coduusuario" no se muestran 
            }else{
                echo "<th>$atributo</th>";
            }
        }
        echo "</tr>";
        //DATOS DEL OBJETO O LOS OBJETOS
        foreach($arrayPedido as $Pedido){
            echo "<tr>";
            foreach ($arrayAtributos as $atributo) {
                $getter = 'get' . ucfirst($atributo);
                $valor = $Pedido->$getter();
                if($atributo == "idPedido"){
                    $idPedido = $Pedido->getIdPedido();//guardamos el código para que esté disponible fuera de este bucle
                    echo "<td>".$valor."</td>";
                } else if(( $rol == "user" ) && ( $atributo == "activo" ||$atributo == "codUsuario" ) ){
                    echo'';//si es user tanto el atributo "activo" como "coduusuario" no se muestran a rol=user
                }else{
                    echo "<td>".$valor."</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    } else{
        echo "<h3>No se encontraron coincidencias.</h3>";
    }

    include_once("../Controllers/PedidosMensajes.php");
    $arrayMensajes=getArrayMensajesPedidos();
    if(is_array($arrayMensajes)){
        foreach($arrayMensajes as $mensaje) {
            echo "<h3>$mensaje</h3>";
        }
    };
}
if(isset($_REQUEST['idPedido']) && ( $rol == "admin" || $rol == "empleado") ){
    echo'
        <h2><a class="finForm" href="ContenidoPedidoBUSCAR.php?numPedido='.$idPedido.'"><img src="../Resources/arrow.png" alt="listar Pedido" />Ver contenidos de este pedido</a></h2>
        <h2><a class="finForm" href="PedidoEDITAR.php?idPedido='.$idPedido.'""><img src="../Resources/arrow.png" alt="listar Pedido" />Editar pedido</a></h2>
    ';
}

include_once("footer.php");
?>
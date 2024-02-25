<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "PedidoBUSCAR dice: no está user en session";
    header("Location: /index.php");
}

include_once("header.php");

if(!empty($_GET)){
    echo'<br><h2><a class="finForm" href="PedidoBUSCAR.php?"><img src="../Resources/arrow.png" alt="listar ContenidoPedido" />Buscar otro pedido</a></h2>
    <br>';
} else{
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
                <th><h2><label for="estado">Busqueda por estado<br>(0=En Carrito, 1=Pedido Pago Pendiente, 2=Pedido Pago Aceptado, 3=Enviado, 4=Recibido, 5=Entrega con incidencia, 6=Completado)</label></h2></th>
                <th><h2><label for="codUsuario">Codigo usuario (DNI)</label></h2></th>
            </tr>
            <tr>
                <td><input type="text" name="idPedido" ><br><br></td>
                <td><input type="date" name="fechaInicio" autofocus><br><br></td>
                <td><input type="date" name="fechaFin" ><br><br></td>
                <td><input type="number" name="estado" min="0" max="5" ><br><br></td>
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
    $codArticulo=null;//mejor null que sin declarar de todas formas lo voy a guardar dentro de nada con lo que tengamos en el GET
    include_once("../Controllers/PedidoBUSCARController.php");
    if(!empty(($_REQUEST["idPedido"]))){
        $idPedido=$_REQUEST["idPedido"];
        if( $rol == "admin" || $rol == "empleado" ){
            $arrayPedido[] = getPedidoByIdPedido($idPedido);
        } else{
            $arrayPedido[] = getPedidoByIdPedido($idPedido, $dni);
        }
        if($arrayPedido == false){
            $_SESSION['idPedidoNotFound'] = true;
        }
    }

    if(!empty(($_REQUEST["numPedido"]))){
        $numPedido=$_REQUEST["numPedido"];
        if( $rol == "admin" || $rol == "empleado" ){
            $arrayPedido[] = getPedidoByIdPedido($numPedido);
        } else{
            $arrayPedido[] = getPedidoByIdPedido($numPedido, $dni);
        }
        if($arrayPedido == false){
            $_SESSION['idPedidoNotFound'] = true;
        }
    }
    
    if( isset($_REQUEST['fechaInicio']) && isset($_REQUEST['fechaFin'])) {
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
    }
    if(!empty(($_REQUEST["codUsuario"]))){
        $codUsuario=$_REQUEST["codUsuario"];
        if( $rol == "admin" || $rol == "empleado" ){
            $arrayPedido = getPedidosByCodUsuario($codUsuario);
        } else{
            $arrayPedido = getPedidosByCodUsuario($codUsuario, $dni);
        }
        if($arrayPedido == false){
            $_SESSION['codUsuarioNotFound'] = true;
        }
    }
    
    $arrayAtributos = getArrayAtributosPedido();
    if( $arrayPedido !== false){
        echo"<table>";
        echo"<tr><th>Atributos:</th>";
        //ENCABEZADOS

        foreach ($arrayAtributos as $atributo) {
            $nombreAtributo = $atributo;
             if(( $rol !== "admin" || $rol !== "empleado" ) && ( $nombreAtributo == "activo" ||$nombreAtributo == "codUsuario" ) ){
                echo'';//si no es admin o empleado tanto el atributo activo como coduusuario no se muestran a rol=user
            }else{
                echo "<th>$nombreAtributo</th>";
            }
        }
        echo "</tr>";
        //DATOS DEL OBJETO O LOS OBJETOS
        foreach($arrayPedido as $Pedido){
            foreach ($arrayAtributos as $atributo) {
                $getter = 'get' . ucfirst($atributo);
                $valor = $Pedido->$getter();
                if($atributo == "idPedido"){
                    $idPedido = $Pedido->getIdPedido();//guardamos el código para que esté disponible fuera de este bucle
                    echo "<td>".$valor."</td>";
                } else if(( $rol !== "admin" || $rol !== "empleado" ) && ( $nombreAtributo == "activo" ||$nombreAtributo == "codUsuario" ) ){
                    echo'';//si no es admin o empleado tanto el atributo activo como coduusuario no se muestran a rol=user
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

    include_once("../Controllers/PedidoMensajes.php");
    $arrayMensajes=getArrayMensajesPedido();
    if(is_array($arrayMensajes)){
        foreach($arrayMensajes as $mensaje) {
            echo "<h3>$mensaje</h3>";
        }
    };
}
if(isset($_REQUEST['idPedido'])){
    echo'
    <h2><a class="finForm" href="PedidoEDITAR.php?idPedido='.$idPedido.'""><img src="../Resources/arrow.png" alt="listar Pedido" />Editar pedido</a></h2>
    <h2><a class="finForm" href="ContenidoPedidoBUSCAR.php?numPedido='.$idPedido.'"><img src="../Resources/arrow.png" alt="listar Pedido" />Ver contenidos de este pedido</a></h2>
    ';
}
echo'
<h2><a class="finForm" href="PedidosLISTAR.php"><img src="../Resources/arrow.png" alt="listar Pedido" />Ver todos los pedidos</a></h2>
';

echo '<h2><a class="finForm"  href="AreaCliente.php">Ir a mi area personal</a></h2>';


include_once("footer.php");
?>
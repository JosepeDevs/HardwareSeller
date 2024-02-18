<?php
include_once("header.php");
?>
    <h1>
        Buscar contenido de un pedido ...
    </h1>
<form action="PedidoBUSCAR.php" method="POST">
    <table>
        <tr>
            <th><h2><label for="idPedido">Id pedido</label></h2></th>
            <th><h2><label for="fecha">Fecha inicio</label></h2></th>
            <th><h2><label for="fecha">Fecha fin</label></h2></th>
            <th><h2><label for="estado">Busqueda por estado<br>(0=En Carrito, 1=Pedido, 2=Enviado, 3=recibido, 4=Entrega fallida, 5=completado)</label></h2></th>
            <th><h2><label for="codUsuario">Codigo usuario (DNI)</label></h2></th>
        </tr>
        <tr>
            <td><input type="text" name="idPedido" ><br><br></td>
            <td><input type="date" name="fechaInicio" autofocus><br><br></td>
            <td><input type="date" name="fechaFin" ><br><br></td>
            <td><input type="text" name="codUsuario" ><br><br></td>
        </tr>
    </table>

    <br>
    <div>
        <h2><input type="submit" value="Consultar"></h2><br><br><br>
    </div>
</form>
<br><br><br>

<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "PedidoBUSCAR dice: no está user en session";
    header("Location: /index.php");
}

if(isset($_REQUEST["idPedido"]) || isset($_REQUEST["fechaInicio"]) ||isset($_REQUEST["fechaFin"]) || isset($_REQUEST["codUsuario"]) ) {
    $idPedido=null;//mejor null que sin declarar
    $codArticulo=null;//mejor null que sin declarar
    include_once("../Controllers/PedidoBUSCARController.php");
    if(!empty(($_REQUEST["idPedido"]))){
        $idPedido=$_REQUEST["idPedido"];
        $arrayPedido = getPedidoByIdPedido($idPedido);
        if($arrayPedido == false){
            $_SESSION['idPedidoNotFound'] = true;
        }
    }
    
    if( isset($_REQUEST['fechaInicio']) && isset($_REQUEST['fechaFin'])) {
        $fechaInicioPredeterminada = "1990/01/01";
        $fechaFinPredeterminada = "2100/01/01";
        empty($_REQUEST["fechaInicio"]) ? $fechaInicio = $fechaInicioPredeterminada : $fechaInicio = $_REQUEST['fechaInicio'] ; 
        empty($_REQUEST["fechaFin"]) ? $fechaFin = $fechaFinPredeterminada : $fechaFin = $_REQUEST['fechaFin'] ; 
        echo$fechaInicio .''.$fechaFin;
        $arrayPedido = GetPedidosByRangoFecha($fechaInicio,$fechaFin);
        if($arrayPedido == false){
            $_SESSION['fechaNotFound'] = true;
        }
    }
    if(!empty(($_REQUEST["codUsuario"]))){
        $codUsuario=$_REQUEST["codUsuario"];
        $arrayPedido = getPedidosByCodUsuario($codUsuario);
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
            echo "<th>$nombreAtributo</th>";
        }
        //DATOS DEL OBJETO O LOS OBJETOS
        echo "</tr>";

        //arrayPedido puede conntener de 0 a vete tu a saber cuantos Pedido
        foreach($arrayPedido as $Pedido) {
            echo"<tr><th>Datos del Pedido encontrado:</th>";
            foreach ($arrayAtributos as $index => $atributo) {
                $codArticuloAtributo = $atributo;
                $getter = 'get' . ucfirst($codArticuloAtributo);//montamos dinámicamente el getter
                $valor = $Pedido->$getter();//lo llamamos para obtener el valor
                echo "<td>$valor</td>";
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
$rol = GetRolDeSession();
if($rol == "admin" || $rol == "editor"){
    echo '<h2><a class="finForm"  href="TablaClientes.php">Ver usuarios</a></h2>';
} 

include_once("footer.php");
?>
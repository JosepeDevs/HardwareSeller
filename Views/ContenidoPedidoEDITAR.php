<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedidoEDITAR dice: no está user en session";
    header("Location: ../index.php");
}


include_once("header.php");
print("<h1>Modificar Contenido del Pedido</h1>");

include_once("../Controllers/ContenidoPedidoEDITARController.php");
$numPedidoOriginal=$_GET["numPedido"];    //el numPedido ha llegado por la url
$_SESSION["numPedido"] = $numPedidoOriginal;
$arrayAtributos = getArrayAtributosContenidoPedido();

//ponemos "editando" en true para que cuando lo mandemos a ValidarDatos lo trate como update
$_SESSION["editandoContenidoPedido"]="true";
$rol4consulta = isset($_GET['rol4consulta'])? $_GET['rol4consulta'] : null;
//ENCABEZADOS
echo"<table>";
        echo"<tr><th>Atributos:</th>";
                foreach ($arrayAtributos as $index => $atributo) {
                    $nombreAtributo = $atributo;
                    echo "<th>$nombreAtributo</th>";
                }
        echo "</tr>";

        //datos ACTUALES OBJETO (estaticos, para que se vean siempre los actuales) PUEDEN SER VARIAS LINEAS
                    $arrayContenidoPedido = GetContenidoPedidoByBusquedaNumPedido($numPedidoOriginal);
                    //arrayContenidoPedido puede conntener de 0 a vete tu a saber cuantos ContenidoPedido
                    foreach($arrayContenidoPedido as $numLinea) {
                        echo"<tr>
                        <th>Datos actuales:</th>";
                        foreach ($arrayAtributos as $index => $atributo) {
                            $nombreAtributo = $atributo;
                            $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                            $valor = $numLinea->$getter();//lo llamamos para obtener el valor
                            echo "<td>$valor</td>";
                        }
                        echo "</tr>";
                    }
                //FORMULARIO para EDITAR PRERELLENADO para que se mantengan los datos si no cambia nada
                    echo '<form action="../Controllers/ContenidoPedidoVALIDAR.php" method="POST">';//ENVIAREMOS MEDIANTE $_POST EL NUEVO (SI LO HA EDITADO)
                    foreach($arrayContenidoPedido as $numLinea) {
                        echo"<tr><th>Nuevos datos</th>";
                        foreach ($arrayAtributos as $atributo) {
                            $nombreAtributo = $atributo;
                            $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                            $valor = $numLinea->$getter();//lo llamamos para obtener el valor
                            if($nombreAtributo == "activo") {
                                echo "
                                    <td>
                                        <select id='activo' name='activo' required>";
                                        if($valor == 0){
                                            echo"
                                                <option value='0' selected>Inactivo</option>
                                                <option value='1' >Activo</option>
                                            </select>";
                                        } else{
                                            echo"
                                                <option value='0' >Inactivo</option>
                                                <option value='1' selected>Activo</option>
                                            </select>";
                                        }
                                    echo"</td>";
                            }else{
                                echo "<td><input type='text' id='$nombreAtributo' name='$nombreAtributo' value='$valor'></td>";
                            }
                        }
                        echo "</tr>";
                    }
        echo "</table>";
    echo "<div class='finForm'><h2><input type='submit' value='Guardar'></h2>";
    echo "</form>";

include_once("../Controllers/ContenidoPedidoMensajes.php");
$arrayMensajes=getArrayMensajesContenidoPedido();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};

echo("<h2><a class='cerrar' href='ContenidoPedidoLISTAR.php?editandoContenidoPedido=false'>Volver al listado de ContenidoPedido</a></h2>");
echo("<h2><a class='cerrar' href='/index.php?editandoContenidoPedido=false'> cerrar sesión</a></h2>");
echo("<h2><a class='cerrar' a href='ContenidoPedidoBORRAR.php?numPedido=$numPedidoOriginal'>BORRAR ContenidoPedido</a></h2>");
include_once("footer.php");
?>
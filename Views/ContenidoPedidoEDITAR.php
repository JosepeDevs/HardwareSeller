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
        foreach ($arrayAtributos as $index => $atributo) {
            $nombreAtributo = $atributo;
            if($nombreAtributo == "numPedido"){
                echo'<tr><th colspan="2"><label for="numPedido">Número de pedido <br> (todas las líneas sustituirán el contenido de este numero de pedido)</label></th>';
                echo'<td colspan="3"><input type="text" name="numPedido" id="numPedido" ></td>';
                echo'<th colspan="1">Número de pedido <br> (todas las líneas son de este numero de pedido)</th>';
                echo'<td colspan="1">'.$numPedidoOriginal.'</td></tr>';
            } else if( $index == 1) {
                echo"<tr><th>Atributos:</th>";
                echo "<th>$nombreAtributo</th>";
            } else {
                echo "<th>$nombreAtributo</th>";
            }
        }
        echo "</tr>";

        //datos ACTUALES OBJETO (estaticos, para que se vean siempre los actuales) PUEDEN SER VARIAS LINEAS
                    $arrayContenidoPedido = GetContenidoPedidoByBusquedaNumPedido($numPedidoOriginal);
                    //arrayContenidoPedido puede conntener de 0 a vete tu a saber cuantos ContenidoPedido
                    foreach($arrayContenidoPedido as $numLinea) {
                        echo"<tr>
                        <th>Datos actuales:</th>";
                        foreach ($arrayAtributos as $index => $atributo) {
                            if($nombreAtributo !== "numPedido"){
                                $nombreAtributo = $atributo;
                                $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                                $valor = $numLinea->$getter();//lo llamamos para obtener el valor
                                echo "<td>$valor</td>";
                            }
                        }
                        echo "</tr>";
                    }
                //FORMULARIO para EDITAR PRERELLENADO para que se mantengan los datos si no cambia nada
                    echo '<form action="../Controllers/ContenidoPedidoVALIDAR.php" method="POST">';//ENVIAREMOS MEDIANTE $_POST EL NUEVO (SI LO HA EDITADO)
                    foreach($arrayContenidoPedido as $index => $numLinea) {
                        echo"<tr><th>Nuevos datos:</th>";
                        foreach ($arrayAtributos as $atributo) {
                            $nombreAtributo = $atributo;
                            $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                            $valor = $numLinea->$getter();//lo llamamos para obtener el valor
                            if( $nombreAtributo == "numPedido"){
                                echo "";//no queremos mostrar nada porque numpedido aparece encima de la tabla
                            } else if($nombreAtributo == "activo") {
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
                            }else if($nombreAtributo == "cantidad"){
                                echo "<td><input type='number' id='$nombreAtributo' name='".$nombreAtributo.$index."' value='$valor'></td>";
                            }else if($nombreAtributo == "precio" || $nombreAtributo == "descuento"){
                                echo "<td><input type='number' step='0.01' id='$nombreAtributo' name='".$nombreAtributo.$index."' value='$valor'></td>";
                            } else {
                                echo "<td><input type='text' id='$nombreAtributo' name='".$nombreAtributo.$index."' value='$valor'></td>";
                            }
                        }
                        echo "</tr>";
                    }
        echo "</table>";
    echo "<div class='finForm'>";
    echo'<button type="button" onclick="addLineaPedidoTodoDisponible()">Añadir una fila al pedido</button>
    <button type="button" onclick="removeLineaPedido()">Quitar una fila al pedido</button><!--resulta que si no le ponemos type entenderá que es el botón de submit-->';
    echo"<h2><input type='submit' value='Guardar'></h2></div>";
    echo "</form>";
echo"<div>";
include_once("../Controllers/ContenidoPedidoMensajes.php");
$arrayMensajes=getArrayMensajesContenidoPedido();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};
echo"</div>";

echo"<div>";
    echo("<h2><a class='cerrar' href='ContenidoPedidoLISTAR.php?editandoContenidoPedido=false'>Volver al listado de ContenidoPedido</a></h2>");
    echo("<h2><a class='cerrar' href='/index.php?editandoContenidoPedido=false'> cerrar sesión</a></h2>");
    echo("<h2><a class='cerrar' a href='ContenidoPedidoBORRAR.php?numPedido=$numPedidoOriginal'>BORRAR ContenidoPedido</a></h2>");
echo"</div>";
include_once("footer.php");
?>

<script>
function addLineaPedidoTodoDisponible() {
    // pillamos la tabla
    var tabla = document.querySelector('table');
    
    // nos vamos a la que actualmente es la última fila de dicha tabla
    var ultimaFila = tabla.rows[tabla.rows.length -  1];
    
    // clonamos la última línea arrastra todos los atributos e hijos (tds, inputs, contenido...)
    var nuevaFila = ultimaFila.cloneNode(true);
    
    // guardams todos los inputs  
    var numLineaInput = nuevaFila.querySelector('input[name^="numLinea"]'); //hacemos que seleccione el input cuyo nombre empiece por numLinea
    
    // lo aumentamos en  1
    var nuevoNumLineaInput = parseInt(numLineaInput.value) +  1;
    numLineaInput.value = nuevoNumLineaInput;

    //cambiamos el nombre 
    numLineaInput.name = "numLinea" + nuevoNumLineaInput;

    // vaciamos los campos excepto el de numLinea
    var inputs = nuevaFila.querySelectorAll('input'); // cogemos TODOS los inputs, lo coge como array que podemos recorrer
    for (var i =  0; i < inputs.length; i++) {
        // si no es el de numLinea, lo vaciamos
        if (inputs[i].name !== 'numLinea') {
            inputs[i].value = '';
            inputs[i].removeAttribute('required'); // quitar atributos como clase, o en este caso, el required, es que si dejamos el required solo deja añadir  1 línea
            // modifica el valor del input "name" añadiendole el número de la línea para poder mandar varias lineas y cada dato tenga un identificador único
            var nombreBase = inputs[i].name.replace(/[0-9]+$/, ''); // quitamos los números y ponemos nada, para evitar fila1 y luego fila12 y luego fila123, etc.
            inputs[i].name = nombreBase + nuevoNumLineaInput;
        } 
    }
    numLineaInput.value = nuevoNumLineaInput;

    // añadimos la linea preparada al final
    var tbody = tabla.querySelector('tbody'); // Select the tbody element
    tbody.appendChild(nuevaFila); // Append the new row to the tbody
}
    function removeLineaPedido() {
        var tabla = document.querySelector('table');
        var ultimaFila = tabla.rows[tabla.rows.length - 1];
        var ultimoTh = ultimaFila.querySelector('th');
        if (ultimoTh && ultimoTh.textContent.trim() === "datos actuales:") {
            // no hacer nada
            return;
        } else{
            if (tabla.rows.length > 3) { 
                tabla.deleteRow(-1); // con -1 podemos decirle la última fila en lugar de tener que buscar el índice de la fila
            }
        }
    }
</script>

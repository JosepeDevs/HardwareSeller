<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
/// las funciones de contenido pedidos y pedidos ya bloquean para que los usuarios solo puedan ver lo suyo propio si no tienen un rol de admin o empleado

$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedidoEDITAR dice: no está user en session";
    header("Location: ../index.php");
}


include_once("header.php");
print("<h1>Modificar Pedido</h1>");

//ponemos "editando" en true para que cuando lo mandemos a ValidarDatos lo trate como update
$_SESSION["editandoPedidoYContenidoPedido"]="true";
$rol4consulta = isset($_GET['rol4consulta'])? $_GET['rol4consulta'] : null;

if(isset($_GET["idPedido"]) ){
    $numPedidoOriginal=$_GET["idPedido"];   
    $_SESSION["numPedido"] = $numPedidoOriginal; 
} else if(isset($_GET["numPedido"])){
    $numPedidoOriginal=$_GET["numPedido"];   
    $_SESSION["numPedido"] = $numPedidoOriginal; 
} else{
    $numPedidoOriginal=false;   
}

include_once("../Controllers/PedidoEDITARController.php");

$pedido = GetPedidoByBusquedaIdPedido($numPedidoOriginal);
$arrayAtributosPedido = getArrayAtributosPedido();

//ENCABEZADOS
echo '<form action="../Controllers/PedidoVALIDAR.php" method="POST">';//ENVIAREMOS MEDIANTE $_POST EL NUEVO (SI LO HA EDITADO)
echo"<table>";
echo"<tr><th>Atributos:</th>";
        foreach ($arrayAtributosPedido as $index => $atributo) {
            $nombreAtributo = $atributo;
            if($nombreAtributo == "activo"){
                echo "<th>$nombreAtributo<br> Esta cambio se aplicará tamibén a todas las lineas de contenido pedido, desactivando por completo el pedido</th>";
            } else {
                echo "<th>$nombreAtributo</th>";
            }
        }
        echo "</tr>";
                //datos ACTUALES OBJETO (estaticos, para que se vean siempre los actuales) 
                if($pedido == false){
                    //no había o no llegó numPedido por url u otr forma
                    echo'<p>Ocurrió un error</p>';
                } else {
                    echo"<tr><th>Datos actuales:</th>";
                    foreach ($arrayAtributosPedido as $atributo) {
                        $nombreAtributo = $atributo;
                        $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                        $valor = $pedido->$getter();//lo llamamos para obtener el valor
                        if($nombreAtributo == "activo") {
                            if($valor==0){
                                echo"<td>Desactivado</td>";
                            }else{
                                echo"<td>Activado</td>";
                            }
                        } else {
                            echo "<td>$valor</td>";
                        }
                    }
                    echo "</tr>";
                }
                //FORMULARIO para EDITAR PRERELLENADO para que se mantengan los datos si no cambia nada
                        echo"<tr><th>Nuevos datos:</th>";
                        foreach ($arrayAtributosPedido as $atributo) {
                            $nombreAtributo = $atributo;
                            $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                            $valor = $pedido->$getter();//lo llamamos para obtener el valor
                            if( $nombreAtributo == "estado" ){
                                echo "<td><input type='number' id='$nombreAtributo' name='".$nombreAtributo."' value='$valor'></td>";
                            }else if($nombreAtributo == "idPedido" ){
                                $_SESSION['idPedido'] = $valor;//lo subimos a session idpedido porque hace falta para identificar el pedido, pero no dejamos editarlo
                                echo "<td>$valor</td>";//idPedido lo genera la BBDD, no dejamos editarlo, igual que no dejeamos editar el codUsuario
                            }if($nombreAtributo == "total" ){
                                echo "<td><input type='number' step='0.01' id='$nombreAtributo' name='".$nombreAtributo."' value='$valor'></td>";
                            } else if($nombreAtributo == "fecha" ){
                                echo "<td><input type='date' id='$nombreAtributo' name='".$nombreAtributo."' value='$valor'></td>";
                            }else if($nombreAtributo == "codUsuario" ){
                                echo "<td>$valor</td>";
                            }else if($nombreAtributo == "activo") {
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
                            } else{
                                //de este no hay ninguno pero si alguna vez cambiara los atributos de pedido los vería con esta línea
                                echo "<td><input type='text' id='$nombreAtributo' name='".$nombreAtributo."' value='$valor'></td>";
                            }
                        }
                        echo "</tr>";
        echo "</table>";
        echo" <div class='finForm'><h2><input type='submit' value='Guardar datos pedido'></h2></div>";
    echo "</form>";


    ///////////////////////////PARTE DE  CONTENIDO PEDIDO

print("<br><br><h2>Modificar Contenido del Pedido</h2>");
include_once("../Controllers/ContenidoPedidoEDITARController.php");

$arrayAtributos = getArrayAtributosContenidoPedido();

//ENCABEZADOS
echo '<form action="../Controllers/ContenidoPedidoVALIDAR.php" method="POST">';//ENVIAREMOS MEDIANTE $_POST EL NUEVO (SI LO HA EDITADO)
echo"<table>";
echo"<tr><th>Atributos:</th>";
        foreach ($arrayAtributos as $index => $atributo) {
            $nombreAtributo = $atributo;
            if($nombreAtributo == "activo"){
                echo "<th>$nombreAtributo<br> Cambios en este atributo solo se aplicará a las líneas modificadas y no a todo el pedido</th>";
            } else {
                echo "<th>$nombreAtributo</th>";
            }
        }
        echo "</tr>";

        //datos ACTUALES OBJETO (estaticos, para que se vean siempre los actuales) PUEDEN SER VARIAS LINEAS
                $arrayContenidoPedido = GetContenidoPedidoByBusquedaNumPedido($numPedidoOriginal);

                if($arrayContenidoPedido == false){
                    //no había o no llegó numPedido por url u otr forma
                    echo'<p>Ocurrió un error</p>';
                } else {

                    //arrayContenidoPedido puede conntener de 0 a vete tu a saber cuantos ContenidoPedido
                    foreach($arrayContenidoPedido as $index => $numLinea) {
                        echo"<tr><th>Datos actuales:</th>";
                        foreach ($arrayAtributos as $atributo) {
                            $nombreAtributo = $atributo;
                            $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                            $valor = $numLinea->$getter();//lo llamamos para obtener el valor
                            if($nombreAtributo == "activo") {
                                if($valor==0){
                                    echo"<td>Desactivado</td>";
                                }else{
                                   echo"<td>Activado</td>";
                                }
                            } else {
                                echo "<td>$valor</td>";
                            }
                        }
                        echo "</tr>";
                    }
                //FORMULARIO para EDITAR PRERELLENADO para que se mantengan los datos si no cambia nada
                    foreach($arrayContenidoPedido as $index => $numLinea) {
                        echo"<tr><th>Nuevos datos:</th>";
                        foreach ($arrayAtributos as $atributo) {
                            $nombreAtributo = $atributo;
                            $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                            $valor = $numLinea->$getter();//lo llamamos para obtener el valor
                            if( $nombreAtributo == "numPedido"){
                                $_SESSION['numPedido'] = $valor;//lo subimos a session porque lo necsitamos para ubicar el pedio al que pertenece este contenido
                                echo "<td>$valor</td>";//no dejamos editar el  Numero Pedido
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
                            } else{
                                echo "<td><input type='text' id='$nombreAtributo' name='".$nombreAtributo.$index."' value='$valor'></td>";
                            }
                        }
                        echo "</tr>";
                    }
        echo "</table>";
    echo "<div class='finForm'>";
            echo'<button type="button" onclick="addLineaPedidoTodoDisponible()">Añadir una fila al pedido</button>
            <button type="button" onclick="removeLineaPedido()">Quitar una fila al pedido</button>';
            echo"<h2><input type='submit' value='Guardar'></h2>
        </div>";
    echo "</form>";

}
echo"<div id='errores'>";
include_once("../Controllers/ContenidoPedidoMensajes.php");
$arrayMensajes=getArrayMensajesContenidoPedido();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};
echo"</div>";

echo"<div>";
    echo("<h2><a class='cerrar' a href='AreaCliente.php'>Ir al área personal</a></h2>");
echo"</div>";
include_once("footer.php");
?>


<script>
function addLineaPedidoTodoDisponible() {

    document.getElementById("errores").innerHTML =  ""
    var tabla = document.querySelector('table');    // pillamos la tabla
    var ultimaFila = tabla.rows[tabla.rows.length -  1];    // nos vamos a la que actualmente es la última fila de dicha tabla
    var nuevaFila = ultimaFila.cloneNode(true);    // clonamos la última línea arrastra todos los atributos e hijos (tds, inputs, contenido...)
    var numLineaInput = nuevaFila.querySelector('input[name^="numLinea"]'); //hacemos que seleccione el input cuyo nombre empiece por numLinea
    
    var nuevoNumLineaInput = parseInt(numLineaInput.value) +  1;    // lo aumentamos en  1
    numLineaInput.value = nuevoNumLineaInput;

    numLineaInput.name = "numLinea" + nuevoNumLineaInput;    //cambiamos el nombre 
    numLineaInput.id = "numLinea" + nuevoNumLineaInput;    //cambiamos el id 

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
            inputs[i].id = nombreBase + nuevoNumLineaInput;
        } 
    }
    numLineaInput.value = nuevoNumLineaInput;

    // añadimos la linea preparada al final
    var tbody = tabla.querySelector('tbody'); // Select the tbody element
    tbody.appendChild(nuevaFila); // Append the new row to the tbody
}

    document.addEventListener("DOMContentLoaded", function() {
         initialRows = document.querySelector('table').rows.length; //al no ponerle const ni nada es global
    });

    function removeLineaPedido() {
        var table = document.querySelector('table');
        console.log("hola"+initialRows);
        if (table.rows.length -1 >= initialRows) {//no borrar más allá de lo que había inicialmente
            table.deleteRow(-1); // con -1 podemos decirle la última fila en lugar de tener que buscar el índice de la fila
        } else{
            document.getElementById("errores").innerHTML = "<h3>No se pueden borrar las líneas que actualmente contiene el pedido.</h3>";
        }
    }
</script>
 
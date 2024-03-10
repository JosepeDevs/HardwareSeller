
<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//esta página es para admins y empleados

include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "Pedido alta dice: no está user en session";
    header("Location: /index.php");
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

include("header.php");
print"<h1>Alta de Contenido de pedido</h1>";


$_SESSION["nuevoPedido"]="true";//ponemos esto a true para que cuando vaya a validar datos lo trate como un insert
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;
//ENCABEZADOS
?>
    <form action="../Controllers/PedidoVALIDAR.php" method="post" enctype="multipart/form-data" >
        <table>
            <tr>
                <th colspan="3"><label for="numPedido">Número de pedido <br> (todas las líneas serán para este numero de pedido)</label></th>
                <td colspan="4"><input type="text" name="numPedido" id="numPedido" required></td>
            </tr>
            <tr>
                <th>Atributos:</th>
                <th><label for="numLinea">Número de la línea</label></th>
                <th><label for="codArticulo">codArticulo</label></th>
                <th><label for="cantidad">cantidad</label></th>
                <th><label for="precio">precio</label></th>
                <th><label for="descuento">descuento</label></th>
                <th><label for="activo">Activo</label></th>
            </tr>
            <tr>
                <th>Artículos en esta linea:</th>
                <td><input type="text" name="numLinea" id="numLinea" value="1" disabled><br><br></td>
                <td><input type="text" name="codArticulo" id="codArticulo" required><br><br></td>
                <td><input type="number" name="cantidad" id="cantidad" required><br><br></td>
                <td><input type='number' accept='^(\d+\.\d+|\d+)$'step='0.01' id='precio' name='precio' required><br><br></td>
                <td><input type='number' accept='^(\d+\.\d+|\d+)$'step='0.01' id='descuento' name='descuento' required><br><br></td>
                <td><select name="activo" id="activo" required>
                        <option value="1">Activado</option>
                        <option value="0">Desactivado</option>
                </td></select>
            </tr>
        </table>
        <div class="finForm">
        <button type="button" onclick="addLineaPedido()">Añadir una fila al pedido</button>
        <button type="button" onclick="removeLineaPedido()">Quitar una fila al pedido</button><!--resulta que si no le ponemos type entenderá que es el botón de submit-->
            <h2><input type="submit" value="Guardar"></h2><br><br><br>
            <h2><input type="reset" value="Reiniciar formulario"></h2>
        </div>
    </form>
    <br><br>
<?php

include_once("../Controllers/PedidosMensajes.php");
$arrayMensajes=getArrayMensajesPedidos();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        print "<h3>$mensaje</h3>";
    }
};

print"<h2><a class='cerrar'  href='PedidoLISTAR.php'>Volver a la tabla de Pedido.</a></h2>";

include("footer.php");
?>

<script>
function addLineaPedido() {
    // pillamos la tabla
    var table = document.querySelector('table');
    
    // nos vamos a la que actualmente es la última fila de dicha tabla
    var lastRow = table.rows[table.rows.length -  1];
    
    // clonamos la última línea arrastra todos los atributos e hijos (tds, inputs, contenido...)
    var newRow = lastRow.cloneNode(true);
    
    // guardams todos los inputs  
    var numLineaInput = newRow.querySelector('input[name^="numLinea"]'); //hacemos que seleccione el input cuyo nombre empiece por numLinea
    
    // lo aumentamos en  1
    var nuevoNumLineaInput = parseInt(numLineaInput.value) +  1;
    numLineaInput.value = nuevoNumLineaInput;

    //cambiamos el nombre 
    numLineaInput.name = "numLinea" + nuevoNumLineaInput;

    // vaciamos los campos excepto el de numLinea
    var inputs = newRow.querySelectorAll('input'); // cogemos TODOS los inputs, lo coge como array que podemos recorrer
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
    var tbody = table.querySelector('tbody'); // Select the tbody element
    tbody.appendChild(newRow); // Append the new row to the tbody
}
function removeLineaPedido() {
    var table = document.querySelector('table');
    if (table.rows.length > 2) { //no borraremos la linea 1 ni los encabezados
        table.deleteRow(-1); // con -1 podemos decirle la última fila en lugar de tener que buscar el índice de la fila
    }
} 
</script>

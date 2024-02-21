<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//ESTA PÁGINA NO SE DEBE PROTEGER, ACCESIBLE A TODOS LOS NAVEGANTES

//HEADER Y TITULO
include_once("header.php");

?>
    <h1>Vista previa del pedido</h1>
    <?php include_once("aside.php");?>
    <table class="table">
    <thead>
        <tr>
            <th><label for="numLinea">Nº linea del pedido</label></th>
            <th><label for="codigo">Código del producto</label></th>
            <th><label for="nombre">Producto</label></th>
            <th><label for="precio">Precio (€)</label></th>
            <th><label for="descuento">Descuento (%)</label></th>
            <th><label for="cantidad">Cantidad </label></th>
            <th>Sub total (€)</th>
        </tr>
    </thead>
    <tbody>
        <form action="DireccionPedido.php" method="post">

        <?php
        include_once('../Controllers/ArticuloBUSCARController.php');
        if(isset($_SESSION['productos']) && count($_SESSION['productos']) > 0){
            $arrayItems = $_SESSION['productos'];//array asociativo con codigo del articulo y cantidad
            $indice=1;
            foreach($arrayItems as $codigo => $cantidad){//aquí los indices al ser asociativo son los propios codigos de artículo
                $articulo = getArticuloByCodigo($codigo);
                $arrayArticulos[] = $articulo;
                if($articulo !== false){
                    $precio=round($articulo->getPrecio(), 2);
                    $descuento=round($articulo->getDescuento(), 2);
                    $cantidad = $arrayItems[$codigo];
                    $subTotal=($precio*(1-($descuento/100)))*$cantidad;
                    echo'
                    <tr>
                        <td><input class="disabled" name="numLinea'.$indice.'" value="'.$indice.'" disabled></input></td>
                        <td><input class="disabled" name="codigo'.$indice.'" disabled value="'.$codigo.'"></input></td>
                        <td><input  class="disabled" name="nombre'.$indice.'" disabled value="'.$articulo->getNombre().'"></input></td>
                        <td><input class="precio disabled"  id= "precio'.$indice.'" name="precio'.$indice.'" disabled value="'.$precio.'"></input></td>
                        <td><input class="descuento disabled" id= "descuento'.$indice.'" name="descuento'.$indice.'" disabled value="'.$descuento.'"></input></td>
                        <td>
                            <div class="row">
                                <button class="reducir" type="button"><i class="lni lni-minus"></i></button>
                                <input class="cantidad disabled" type="number" id="cantidad'.$indice.'" name="cantidad'.$indice.'" value="'.$cantidad.'"/>
                                <button class="aumentar" type="button"><i class="lni lni-plus"></i></button>
                            </div>
                        </td>
                        <td class="subTotal">'.$subTotal.'</td>
                    </tr>
                    ';
                    $arraySubtotales [] = $subTotal;
                    $total = array_sum($arraySubtotales);
                    $indice+=1;
                } else{
                    $total=0;
                    echo '<tr><td colspan="5"><p>Carrito sin artículos que mostrar</p></td>';
                }
            } 
            echo'
            <tfoot>
                <tr>';
                    if(count($_SESSION['productos']) > 0){ 
                        echo'
                            <td colspan="3"><h4> TOTAL (€): </h4></td>
                            <td colspan="4" class="total" ><h2><b>'.$total.'</b></h2></td>
                         <br>';
                    } 
                echo'
                </tr>
            </tfoot>';
        }else{
            echo '<tr><td colspan="7"><p>Carrito sin artículos (carrito vacío)</p></td>';
        } 
    ?>
    </tbody>
    </table>
    <button type="button"><a href="../Views/Catalogo.php" class="btn btn-warning"><i class="lni lni-chevron-left"></i>Seguir navegando </a></button>
    <button type="submit" class="submit-carrito"><span>Proceder a DIRECCIÓN DE ENVÍO</span><i class="lni lni-chevron-right"></i></button> 
    </form>
<?php include_once("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".reducir").forEach(function(button) {//con esto seleccionamos todos los elements de la clase reducir
        button.addEventListener("click", ReducirCantidad); //añadir el listener a todos los botones
    });
    document.querySelectorAll(".aumentar").forEach(function(button) {
        button.addEventListener("click", AumentarCantidad);//añadir el listener a todos los botones
    });
    document.querySelectorAll(".cantidad").forEach(function(input) {//a cada uno de los elementos seleccionados aplicar↓
        input.addEventListener("change", CalcularTotales);//esto es como poner en el html "onchange"
    });

    const submitButton = document.querySelector(".submit-carrito"); 
    submitButton.addEventListener("click", HabilitarInputs);

    function ReducirCantidad() {
        var inputCantidad = this.parentElement.querySelector(".cantidad");//this es el botón , subimos al span, luego seleccionamos de la clase .cantidad un input
        var valorActual = parseInt(inputCantidad.value);
        if (valorActual >  0) {
            inputCantidad.value = valorActual -  1;
            CalcularTotales(); //llamamos a la función que actualiza total y subtotal
        }
    }

    function AumentarCantidad() {
        var inputCantidad = this.parentElement.querySelector(".cantidad");//se llama en el boton, subimos al span, luego seleccionamos de la clase .cantidad un input
        var valorActual = parseInt(inputCantidad.value);
        inputCantidad.value = valorActual +  1;
        CalcularTotales(); //llamamos a la función que actualiza total y subtotal
    }
    
    function CalcularTotales() {
        var subtotales = []; // Array donde guadaremos los subtotales
        var total =  0; 
        
        // Loop todas las filas
        var filas= document.querySelectorAll(".table tr")
        var filasArray = Array.from(filas); //convertimos el nodelist en array
        var filasSinCabeceras = filasArray.slice(1, filasArray.length -  1); // fragmenta array del indice indicado al indice indical (0 es el primer elemento)
        console.log(filasSinCabeceras);
        var indice = 1
        filasSinCabeceras.forEach(function(row) {//metemos todas las filas hermanas de todos los tbodys en un array 
            var cantidadInput = row.querySelector(".cantidad");//se llama en el boton, subimos al span, luego seleccionamos de la clase .cantidad un input
            var precioInput = row.querySelector(".precio");//se llama en el boton, subimos al span, luego seleccionamos de la clase .cantidad un input
            var descuentoInput = row.querySelector(".descuento");//se llama en el boton, subimos al span, luego seleccionamos de la clase .cantidad un input
            var cantidad = parseInt(cantidadInput.value);
            var precio = parseFloat(precioInput.value);
            var descuento = parseFloat(descuentoInput.value);
            var subTotalTd = row.querySelector(".subTotal")
            var subTotal = parseFloat(subTotalTd.textContent)
            subTotal = (precio * (1 - (descuento /  100))) * cantidad;
 
            subTotalTd.textContent = subTotal.toFixed(2);//cambiamos el subtotal y nos aseguramos que esté redondeado a 2 cifras decimales

            subtotales.push(subTotal);//metemos el subtotal en el array
        });
    
        //reduce un array a un único valor. aplica una función definida in situ a cada elemento del array y lo va añadiendo al total, que es lo que devuelve
        total = subtotales.reduce(function(a, b) { //aquí b) es subtotal y a) es el total o return final 
            return a + b;
        },  0);// este 0 es el acumulador, le dice por donde debe empezar, en este caso desde el índice 0 p'alante

        //metemos redondeado el total donde le coresponde
        document.querySelector(".total").textContent = total.toFixed(2);
    }

    function HabilitarInputs(event){
        event.preventDefault(); //no dejamos que se envie
        document.querySelectorAll(".disabled").forEach(function(input) {//a cada uno de los elementos seleccionados aplicar↓
        input.removeAttribute("disabled"); //quitamos el disabled para que se envíe
        });
        //console.log(new FormData(document.querySelector("form"))); // ver qué narices hay en el formulario
        const form = event.target.closest('form'); //elegimos el form más cercano al botón
        form.submit(); //ahora sí lo mandamos 
    }

});
</script>

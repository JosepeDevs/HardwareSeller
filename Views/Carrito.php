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
        if(count($_SESSION['productos']) > 0){
            $arrayItems = $_SESSION['productos'];//array asociativo con codigo del articulo y cantidad
            foreach($arrayItems as $codigo => $cantidad){//aquí los indices al ser asociativo son los propios codigos de artículo
                $indice=0;
                $articulo = getArticuloByCodigo($codigo);
                $arrayArticulos[] = $articulo;
                if($articulo !== false){
                    $precio=$articulo->getPrecio();
                    $descuento=$articulo->getDescuento();
                    $cantidad = $arrayItems[$codigo];
                    $subTotal=($precio*(1-($descuento/100)))*$cantidad;
                    echo'
                    <tr>
                        <td><input name="numLinea'.$indice.'" value="'.$indice.'" disabled></input></td>
                        <td><input name="codigo'.$indice.'" disabled value="'.$codigo.'"></input></td>
                        <td><input name="nombre'.$indice.'" disabled value="'.$articulo->getNombre().'"></input></td>
                        <td><input name="precio'.$indice.'" disabled value="'.$precio.'"></input></td>
                        <td><input name="descuento'.$indice.'" disabled value="'.$descuento.'"></input></td>
                        <td>
                            <div class="row">
                                <button class="reducir" type="button"><i class="lni lni-minus"></i></button>
                                <span class="cantidad">
                                    <input type="number" name="cantidad'.$indice.'" value="'.$cantidad.'"/>
                                </span>
                                <button class="aumentar" type="button"><i class="lni lni-plus"></i></button>
                            </div>
                        </td>
                        <td class="subTotal">'.$subTotal.'</td>
                    </tr>
                    ';
                    $arraySubtotales [] = $subTotal;
                    $total = array_sum($arraySubtotales);
                } else{
                    $total=0;
                    echo '<tr><td colspan="5"><p>Carrito sin artículos que mostrar</p></td>';
                }
                $indice+=1;
            } 
        }else{
            echo '<tr><td colspan="7"><p>Carrito sin artículos (carrito vacío)</p></td>';
        } 
    ?>
        </form>
    </tbody>
    <tfoot>
        <tr>
            <?php if(count($_SESSION['productos']) > 0){ ?>
                <td class="text-center total" colspan="7"><h2><b>Total (€) <?php echo $total ?></b></h2></td>
                <br>
            <?php } ?>
        </tr>
    </tfoot>
    </table>
    <button type="button"><a href="../Views/Catalogo.php" class="btn btn-warning"><i class="lni lni-chevron-left"></i>Seguir navegando </a></button>
    <button type="submit" class="submit-button"><span>Proceder a DIRECCIÓN DE ENVÍO</span><i class="lni lni-chevron-right"></i></button> 
   
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
    document.querySelectorAll(".cantidad input").forEach(function(input) {//a cada uno de los elementos seleccionados aplicar↓
        input.addEventListener("change", CalcularTotales);//esto es como poner en el html "onchange"
    });

    function ReducirCantidad() {
        var inputCantidad = this.parentElement.querySelector(".cantidad input");//se llama en el boton, subimos al span, luego seleccionamos de la clase .cantidad un input
        var valorActual = parseInt(inputCantidad.value);
        if (valorActual >  1) {
            inputCantidad.value = valorActual -  1;
            CalcularTotales(); //llamamos a la función que actualiza total y subtotal
        }
    }

    function AumentarCantidad() {
        var inputCantidad = this.parentElement.querySelector(".cantidad input");//se llama en el boton, subimos al span, luego seleccionamos de la clase .cantidad un input
        var valorActual = parseInt(inputCantidad.value);
        inputCantidad.value = valorActual +  1;
        CalcularTotales(); //llamamos a la función que actualiza total y subtotal
    }

    function CalcularTotales() {
        var subtotales = []; // Array donde guadaremos los subtotales
        var total =  0; 

        // Loop todas las filas
        var filas= document.querySelectorAll("tbody tr")
        filas.forEach(function(row) {//metemos todas las filas hermanas de todos los tbodys en un array 
            var cantidad = parseInt(row.querySelector("td div span input[name^='cantidad']").value);//cogems el input por el nombre de la varibale con un poco de regex
            console.log(row);
            console.log("deberia haber salido td div span");
            console.log(querySelector("td div span input[name^='cantidad']"));
            console.log("deberia haber salido td div span");
            var precio = parseFloat(row.querySelector("td div span input[name^='precio']").value); // ^=  es para seleccionar elementos que empiecen por lo que se indique
            var descuento = parseFloat(row.querySelector("td div span input[name^='descuento']").value); // así cogemos descuento1, descuento2, etc.

            var subtotal = (precio * (1 - (descuento /  100))) * cantidad;

            row.querySelector(".subTotal").textContent = subtotal.toFixed(2);//cambiamos el subtotal y nos aseguramos que esté redondeado a 2 cifras decimales

            subtotales.push(subtotal);//metemos el subtotal en el array
        });
        
        //reduce un array a un único valor. aplica una función definida in situ a cada elemento del array y lo va añadiendo al total, que es lo que devuelve
        total = subtotales.reduce(function(a, b) { //aquí b) es subtotal y a) es el total o return final 
            return a + b;
        },  0);// este 0 es el acumulador, le dice por donde debe empezar, en este caso desde el índice 0 p'alante

        //metemos redondeado el total donde le coresponde
        document.querySelector(".total").textContent = total.toFixed(2);
    }
});
</script>
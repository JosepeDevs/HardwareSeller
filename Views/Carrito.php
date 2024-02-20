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
                                <span class="cantidad"><input type="number" name="cantidad'.$indice.'" value="'.$cantidad.'"></input></span>
                                <button class="aumentar" type="button"><i class="lni lni-plus"></i></button>
                            </div>
                        </td>
                        <td class=subTotal>'.$subTotal.'</td>
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
            echo '<tr><td colspan="5"><p>Carrito sin artículos (carrito vacío)</p></td>';
        } 
    ?>
        </form>
    </tbody>
    <tfoot>
        <tr>
            <?php if(count($_SESSION['productos']) > 0){ ?>
                <td class="text-center total" colspan="5"><h2><b>Total (€) <?php echo $total ?></b></h2></td>
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
    document.querySelector(".reducir").addEventListener("click", ReducirCantidad);
    document.querySelector(".reducir").addEventListener("click", RecalcularTotal);
    document.querySelector(".aumentar").addEventListener("click", AumentarCantidad);
    document.querySelector(".aumentar").addEventListener("click", RecalcularTotal);
    
    function ReducirCantidad() {
        var elementos = document.querySelectorAll(".cantidad");
        elementos.forEach(function(elemento) {
            var valorActual = parseInt(elemento.textContent);
            elemento.textContent = valorActual - 1;
        });
    }
});
</script>
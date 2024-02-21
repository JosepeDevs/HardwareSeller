<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include("header.php");
if(isset($_GET["codigo"])){
    $codigo = $_GET["codigo"];
    include_once("../Controllers/ArticuloBUSCARController.php");
    $articulo = getArticuloByCodigo($codigo);
} 
$directorio = "/Resources/ImagenesArticulos/";

echo'<h1>Ficha artículo:'.$articulo->getNombre().'</h1>';

include_once("aside.php");
?>
<section class="imagen-ficha-articulo">
    <? echo' <a href="FichaArticulo.php?codigo='.$articulo->getCodigo().'">
                <img src="'.$directorio .$articulo->getImagen().'" class="img-fluid" alt="'.$articulo->getImagen().'">
            </a>'?>
</section>
<section class="Precio-carrito">
    <div id="precio">
    <h2>Precio: <? $articulo->getPrecio() ?> €</h2>
    </div>
    <div id="carrito">
        <a href="<? echo '?codigo='.$articulo->getCodigo() ?>">Añadir al carrito  <i class="lni lni-cart-full" alt="Añadir al carrito"></i></a>
    </div>  
</section>   
<section id="descripcion">
    <p>Descripción del producto:</p><br>
    <textarea readonly rows=5 cols=40 name="texto" ><?=$articulo->getDescripcion() ?></textarea><br><br>
</section>
<section id="relacionados">
    <table>
        <tr>
            <th colspan="12"><h2>Productos relacionados:</h2></th>
        </tr>
        <tr>
            <?
            echo'<td class="col-12 col-lg-3 col-sm-1">
            </td>';
            ?>
</section>



<?php
include("footer.php");
?>
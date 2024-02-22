<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include("header.php");
if(isset($_GET["codigo"])){
    $codigo = $_GET["codigo"];
    include_once("../Controllers/ArticuloBUSCARController.php");
    $articulo = getArticuloByCodigo($codigo);
} 
$directorio = "/Resources/ImagenesArticulos/";

$codigo = $articulo->getCodigo();
echo "codigo=";
echo $codigo;
$nombre = $articulo->getNombre();
$descripcion = $articulo->getDescripcion();
$precio = $articulo->getPrecio();
$imagen = $articulo->getImagen();
echo "precio=";
echo $precio;

echo'<h1>Ficha artículo:'.$nombre.'</h1>';

include_once("aside.php");
?>
<section class="imagen-ficha-articulo">
    <? echo' <a href="FichaArticulo.php?codigo='.$codigo.'">
                <img src="'.$directorio .$imagen.'" class="img-fluid" alt="'.$codigo." ".$imagen.'">
            </a>'?>
</section>
<section class="Precio-carrito">
    <div id="precio">
        <h2>Precio: <? echo$precio ?> €</h2>
    </div>
    <div id="carrito">
        <a href="<? echo '?codigo='.$codigo ?>"><i class="lni lni-cart-full" alt="Añadir al carrito"></i>Añadir al carrito </a>
    </div>  
</section>   
<br>
<br>
<section id="descripcion">
    <p>Descripción del producto:</p><br>
    <p><? echo$descripcion?></p><br><br>
</section>
<section id="relacionados">
    <table>
        <tr>
            <th colspan="12"><h2>Productos relacionados:</h2></th>
        </tr>
        <tr>
            <?
            $arrayArticulosRelacionados = GerArticulosRelacionadosByCodigo($articulo->getCodigo());
            if($arrayArticulosRelacionados !== false){
                foreach ($arrayArticulosRelacionados as $index => $articuloRelacionado) {
                    echo'<td class="col-12 col-lg-3 col-sm-1">';
                    echo'<a href=?codigo='.$articuloRelacionado->getCodigo().'">
                    <img src="'.$directorio.$articuloRelacionado->getRutaImagen().'" alt="'.$articuloRelacionado->getNombre().'"/>
                    <br>'.$articuloRelacionado->getNombre().'
                    </a>';
                    echo'</td>';
                }
            } else{
                echo'<td colspan="12">No se han encontrado productos relacionados:</td>';
            }
            ?>
        </tr>
</section>



<?php
include("footer.php");
?>
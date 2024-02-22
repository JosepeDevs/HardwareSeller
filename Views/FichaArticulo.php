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
$nombre = $articulo->getNombre();
$descripcion = $articulo->getDescripcion();
$precio = $articulo->getPrecio();
$imagen = $articulo->getImagen();
$categoria = $articulo->getCategoria();
$activo = $articulo->getActivo();

echo'<h1>Ficha artículo:'.$nombre.'</h1>';

include_once("aside.php");
?>
<section class="imagen-ficha-articulo">
    <? echo' <a href="FichaArticulo.php?codigo='.$codigo.'">
                <img src="'.$directorio .$imagen.'" class="img-fluid" alt="'.$codigo." ".$imagen.'">
            </a>'?>
</section>
<?if($activo==1){
echo'
    <section class="Precio-carrito">
        <div id="precio">
            <h2>Precio: '.$precio.' €</h2>
        </div>
    <br>
    <br>
    <div id="carrito">
        <a href="?codigo='.$codigo.'"><i class="lni lni-cart-full" alt="Añadir al carrito"></i>Añadir al carrito </a>
    </div>  
</section>';
} else{
    echo'<p>Prodcuto actualmente descatalogado</p>';
}?> 
<br>
<br>
<section id="descripcion">
    <p>Descripción del producto:<? echo$descripcion?></p><br><br>
</section>
<br>
<section id="categoria">
    <p>Categoria del producto:<? echo$categoria?></p><br><br>
</section>
<section id="relacionados">
    <table>
        <tr>
            <th colspan="12"><h2>Productos relacionados:</h2></th>
        </tr>
        <tr>
            <?
            $arrayArticulosRelacionados = GerArticulosRelacionadosByCodigo($articulo->getCodigo());
            if($arrayArticulosRelacionados == false){
                echo'<td colspan="12">No se han encontrado productos relacionados.</td>';
            }    
            if($arrayArticulosRelacionados !== false){
                //todo hacerlo carousel y poder meter más de los que caben en pantalla 
                for($i=0;$i<=6 && $articuloRelacionado[$i]->getCodigo() !== $codigo;$i++){  //no dejaremos que se muestre el propio item como relacionado
                    echo'<td class="col-12 col-lg-1 col-sm-1">';
                    echo'
                    <div>
                        <a href=?codigo='.$articuloRelacionado[$i]->getCodigo().'">
                            <img src="'.$directorio.$articuloRelacionado[$i]->getImagen().'" alt="'.$articuloRelacionado[$i]->getNombre().'"/>
                    </div>
                    <br>
                    <div>
                        <p>'.$articuloRelacionado[$i]->getNombre().'</p>
                    </div></a>
                    <br>
                    <div>
                        <p>Precio: '.$articuloRelacionado[$i]->getNombre().'</p>
                    </div>
                    ';
                    echo'</td>';
                }
            }
            ?>
        </tr>
</section>



<?php
include("footer.php");
?>
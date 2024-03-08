<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//NO PROTEGER
include("header.php");
if(isset($_GET["codigo"])){
    $codigo = $_GET["codigo"];
    include_once("../Controllers/ArticuloBUSCARController.php");
    $articulo = getArticuloByCodigo($codigo);
    $codigo = $articulo->getCodigo();
    $nombre = $articulo->getNombre();
    $descuento = $articulo->getDescuento();
    $descripcion = $articulo->getDescripcion();
    $precio = $articulo->getPrecio();
    $imagen = $articulo->getImagen();
    $categoriaInt = $articulo->getCategoria();
    $activo = $articulo->getActivo();
} 
$directorio = "/Resources/ImagenesArticulos/";

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
            <h4 style="text-decoration: line-through;">Precio: '.$precio.' € </h4>
            <h2>Descuento: '.$descuento.' €</h2>
            <h2>Precio: '.round($precio*(1-($descuento/100)),2).' €</h2>
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
    <p>Categoria del producto:<? echo$categoriaInt?></p><br><br>
</section>
<section>
    <button type='button'><a href='javascript:history.back()' class='btn btn-warning'><i class='lni lni-chevron-left'></i><i class='lni lni-chevron-left'></i>Seguir navegando</a></button>
</section>
<br>
<br>
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
                for($i=0;$i<min(count($arrayArticulosRelacionados),7);$i++){  //hasta que acabe el array o haya impreso 7 items que son los que caben en pantalla
                    if($arrayArticulosRelacionados[$i]->getCodigo() == $codigo){
                        continue;//no dejaremos que se muestre el propio item como relacionado
                    }
                    echo'<td class="col-12 col-lg-1 col-sm-1">';
                    echo'
                    <div>
                        <a href=?codigo='.$arrayArticulosRelacionados[$i]->getCodigo().'>
                            <img src="'.$directorio.$arrayArticulosRelacionados[$i]->getImagen().'" alt="'.$arrayArticulosRelacionados[$i]->getNombre().'"/>
                    </div>
                    <br>
                    <div>
                        <p>'.$arrayArticulosRelacionados[$i]->getNombre().'</p>
                    </div></a>
                    <br>
                    <div>
                        <p>Precio: '.$arrayArticulosRelacionados[$i]->getPrecio().' €</p>
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
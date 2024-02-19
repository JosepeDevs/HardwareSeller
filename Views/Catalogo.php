<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//ESTA PÁGINA NO SE DEBE PROTEGER, ACCESIBLE A TODOS LOS NAVEGANTES

//AÑADIR AL CARRITO
if(isset($_GET["codigo"])) {
    $codigoParaCarrito = $_GET["codigo"] ;
}

if(!array_key_exists('productos', $_SESSION)) {
    $_SESSION['productos'] = []; // declara que dentro de la key "productos" vamos a guardar un array
}

//mira si existe ya el producto, si ya existe añade 1 , si no existe, guarda 1
$_SESSION['productos'][$codigoParaCarrito] = array_key_exists($codigoParaCarrito, $_SESSION['productos']) ? $_SESSION['productos'][$codigoParaCarrito] + 1 : 1;



//HEADER Y TITULO
include_once("header.php");
print('<h1>Catálogo</h1>
    <div  class="col-lg-3 col-md-1 col-12">
        <aside>
            <a href="ArticuloBUSCAR.php">
                <img class="iconArribaTabla"  src="../Resources/buscaAr.png" alt="recraft icon"/> Buscar artículo
            </a>
            <a href="ArticulosLISTAR.php">
            <img  class="iconArribaTabla" src="../Resources/refresh.png" alt="refrescar" /> Recargar tabla (Quita ordenación y reinicia paginación)
        </a>
            <a href="?prebuilt">Pre-built computers</a>
            <a href="?Pantallas">Pantallas</a>
            <a href="?Graficas">Gráficas</a>
            <a href="?Mobo">Placas base</a>
            <a href="?RAM">RAM</a>
        </aside>
    <div>
');

//PREPARAR ARRAYS CON OBJETOS
$orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
include_once("../Controllers/OrdenarArticulosController.php");
$arrayArticulos = getArrayArticulosOrdenados($orden);
$itemXpagPredeterminado=9;
$articulosAMostrar = 9;
if(! isset($_GET['pag'])){
    $paginaActual = 0;
}else{
    if( is_numeric($_GET['pag'])){
        $paginaActual = $_GET['pag'] - 1 ;
    } else if ($_GET['pag'] == "X" ){
        $paginaActual = "X";
    }
}

$directorio = "/Resources/ImagenesArticulos/";

include_once("../Controllers/ArticulosLISTARController.php");
$arrayAImprimir = getArrayPaginadoArticulos($arrayArticulos, $articulosAMostrar, $paginaActual);

//TABLA LISTANDO ARTICULOS
echo"<div class='col-lg-9 col-md-11 col-12'>
        <table>";
            for( $i = 0; $i < count($arrayAImprimir); $i++ ){
                if($i==0 || $i==3 || $i==6 || $i==9 ){ //si es un múltiplo de 3 crear línea nueva
                    echo'<div class="container">
                        <tr>';
                }
                echo'
                            <td>
                                <div class="row">
                                    <div class="col-12 col-lg-6 col-sm-1">
                                        <img src="'.$directorio .$arrayAImprimir[$i]->getImagen().'" class="img-fluid" alt="'.$arrayAImprimir[$i]->getImagen().'">
                                        <br>
                                        <h2>Nombre: '.$arrayAImprimir[$i]->getNombre().'</h2>
                ';
                                        if($arrayAImprimir[$i]->getDescuento() == 0){
                                            echo'
                                                <h2>Precio: '.$arrayAImprimir[$i]->getPrecio().' € </h2>
                                            ';
                                        } else {
                                            echo'
                                                <h4 style="text-decoration: line-through;">Precio: '.$arrayAImprimir[$i]->getPrecio().' € </h4>
                                                <h2">Descuento: '.$arrayAImprimir[$i]->getDescuento().' % </h2>
                                            ';
                                        }
                                      echo'  <h2>Precio: '. round($arrayAImprimir[$i]->getPrecio() * (1 - ($arrayAImprimir[$i]->getDescuento()/100)), 2).' € </h2>
                                        <a href="?codigo='.$arrayAImprimir[$i]->getCodigo().'">Añadir al carrito  <i class="lni lni-cart-full" alt="Añadir al carrito"></i></a>
                                    </div>
                                </div>
                            <td>
                ';
                if($i==2 || $i==5 || $i==8 ){ //si es un múltiplo de 3 crear línea nueva
                        echo'</tr>
                        </div>';//fin del div row
                }
                if($i==count( $arrayAImprimir) -1){
                     echo '</table>';
                }
            }
   //PAGINACIÓN
   print "<div class='paginacion'>";
   $filasTotales = count($arrayArticulos)/3;
   $paginasTotales = ceil($filasTotales / $articulosAMostrar);
   if(is_numeric($paginaActual) && is_numeric($articulosAMostrar)){
       //estamos viendo los registros paginados
       //estamos al principio de la lista, además de lo anterior también imprimiremos "anterior"
       if($paginaActual == 0 ){
           print "<p>Anterior</p>"; //en la primera página esto no debe ser un enlace
       } else{
           print "<a href='?pag=".($paginaActual)."&ordenNombres=$orden'>Anterior</a>";
       }
       for ($numeroIndicePaginacion = 1; $numeroIndicePaginacion <= $paginasTotales; $numeroIndicePaginacion++) {
           if($numeroIndicePaginacion == $paginaActual + 1 ){
               print "<b>$numeroIndicePaginacion</b>";
           }else{
               print "<a href='?pag=$numeroIndicePaginacion&ordenNombres=$orden'>$numeroIndicePaginacion</a>";
           }
           if($paginaActual +1 == $paginasTotales && $numeroIndicePaginacion == $paginasTotales){
               print "<p>Siguiente</p>"; //en la primera página esto no debe ser un enlace
           }else if($numeroIndicePaginacion == $paginasTotales){
               print "<a href='?pag=".($paginaActual+2)."&ordenNombres=$orden'>Siguiente</a>";
           } else{
               print "";//no printear nada
           }
       }
   } else{
       //estamos viendo todos los registros en una página
       for ($numeroIndicePaginacion = 1; $numeroIndicePaginacion <= $paginasTotales; $numeroIndicePaginacion++) {
           print "<a href='?pag=$numeroIndicePaginacion&ordenNombres=$orden'>$numeroIndicePaginacion</a>";
       }
   }

   //FORMULARIO PIE DE PÁGINA PARA ELEGIR LA PÁGINA A VER, Nº registros/pág
   $opcionesitemXpag=[3,4,5];
   if (isset($_GET['pag']) && ( $_GET['pag'] == "X" ) ){
       print "<b>Ver todos</b>";
   } else{
       print "<a href='?pag=X&ordenNombres=$orden'>Ver todos</a>";
   }
   print "</div>";//final del div de paginación
?>
<?php
include_once("footer.php");
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

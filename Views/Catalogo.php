<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//ESTA PÁGINA NO SE DEBE PROTEGER, ACCESIBLE A TODOS LOS NAVEGANTES

//si no existe la key productos la crea en session (productos será un array asociativo)
if(!array_key_exists('productos', $_SESSION)) {
    $_SESSION['productos'] = []; 
}
//si no existe la key productos la crea en session (productos será un array asociativo)
if(!array_key_exists('precios', $_SESSION)) {
    $_SESSION['precios'] = []; 
}
include_once("../Controllers/ArticulosLISTARController.php");

//AÑADIR AL CARRITO
if(isset($_GET["codigo"])) {
    include_once("../Models/Articulo.php");
    //para que no puedan ver el precio de lo que quieran vamos a poner la comprobación antes del get para revisar que esté activo el producto, si no lo está no lo metemos.
   //no tengo tiempo de revisarlo, le pongo a todo nombres distintos a los que puede que haya usado más abajo.
    $codigoParaCheckActivo = isset($_GET['codigo']) ? $_GET['codigo']:null;
    $articuloParaCheckActivo = getArticuloByCodigo($codigoParaCheckActivo);
    $activoParaCheck = $articuloParaCheckActivo->getActivo();
    $precioParaCarrito = $articuloParaCheckActivo->getPrecio();
    $descuentoParaCarrito = $articuloParaCheckActivo->getDescuento();
    $precioParaCarrito = $precioParaCarrito * (1-($descuentoParaCarrito/100));
    if($activoParaCheck == 1){
        $codigoParaCarrito = $_GET["codigo"] ;
        //mira si existe ya el producto, si ya existe añade 1 , si no existe, guarda 1
        $_SESSION['productos']["$codigoParaCarrito"] = array_key_exists($codigoParaCarrito, $_SESSION['productos'])    ? $_SESSION['productos']["$codigoParaCarrito"] + 1 : 1;
        //mira en el array precios si ya existe el codigo del artículo, si no existe, añade el precio, si ya existe miramos el dato guardado y le sumamos lo que toque del articulo que acabamos de añadir
        $_SESSION['precios']["$codigoParaCarrito"] = array_key_exists($codigoParaCarrito, $_SESSION['precios'])    ? $_SESSION['precios']["$codigoParaCarrito"]+$precioParaCarrito : $precioParaCarrito;
    }
}

//HEADER Y TITULO
include_once("header.php");
print('<h1>Catálogo</h1>');
include_once("BreadCrumbsCatalogo.php");
print'<br>';
include_once("aside.php");


//ZONA FILTRADOS
$categoriaFiltrada = isset($_GET['categoria'])? $_GET['categoria'] : null;

if(! isset($_GET['pag'])){
    $paginaActual = 0;
}else{
    if( is_numeric($_GET['pag'])){
        $paginaActual = $_GET['pag'] - 1 ;
    } else if ($_GET['pag'] == "X" ){
        $paginaActual = "X";
    } else{
        $paginaActual = 0;
    }
}
print"<h3>Atributos para filtrar</h3>";
print'<div class="col-lg-9 col-md-11 col-12">';
    print"<table>";
            print"<tr>";
            //ENCABEZADOS
            $arrayAtributos = getArrayAtributosArticulo();
            if($arrayAtributos !== false){
                foreach ($arrayAtributos as $atributo) {
                    $nombreAtributo = $atributo;
                    if($atributo !==  "imagen" && $atributo !==  "descripcion"){
                        //la descripción en catálogo no se ve y filtrar por imagen es inutil, dejamos por código para los clientes que quieran revisar referencias (codigo) conocidas

                        print"<th>
                        $nombreAtributo <br>Ordenar por este atributo:<br>
                        <a class='ordenar' href='?orden=ASC&atributo=$nombreAtributo&categoria=".$categoriaFiltrada."&pag='.($paginaActual+1)'>ASC</a>
                        <a class='ordenar' href='?orden=DESC&atributo=$nombreAtributo&categoria=".$categoriaFiltrada."&pag='.($paginaActual+1)'>DESC</a>
                        </th>";
                    }
                }
            }
            print"</tr>";
    print"</table>";
print'</div>';

        
//PREPARAR ARRAYS CON OBJETOS
$orden = isset($_GET['orden']) ? $_GET['orden']:null;
$atributoElegido = isset($_GET["atributo"])?$_GET["atributo"]:"nombre";//si no hay ningun atributo ordena por nombre
include_once("../Controllers/OrdenarArticulosController.php");
$arrayArticulos = getArrayArticulosOrdenadosByAtributo($orden,$atributoElegido);

$arrayArticulos = getArrayArticulosFiltradosByCodigoCategoria($arrayArticulos, $categoriaFiltrada);

$itemXpagPredeterminado=9;
$articulosAMostrar = 9;


$directorio = "/Resources/ImagenesArticulos/";

include_once("../Controllers/ArticulosLISTARController.php");
$arrayAImprimir = getArrayPaginadoArticulos($arrayArticulos, $articulosAMostrar, $paginaActual);

//TABLA LISTANDO ARTICULOS
print"<div class='col-lg-9 col-md-11 col-12'>
        <table>";
            for( $i = 0; $i < count($arrayAImprimir); $i++ ){
                if($i==0 || $i==3 || $i==6 || $i==9 ){ //si es un múltiplo de 3 crear línea nueva
                    print'<div class="container">
                        <tr>';
                }
                print'
                            <td>
                                <div class="row">
                                    <div class="col-12 col-lg-6 col-sm-1">
                                        <a href="FichaArticulo.php?categoria='.$arrayAImprimir[$i]->getCategoria().'&codigo='.$arrayAImprimir[$i]->getCodigo().'"><img src="'.
                                        $directorio .$arrayAImprimir[$i]->getImagen().'" class="img-fluid" alt="'.$arrayAImprimir[$i]->getImagen().'"></a>
                                        <br>
                                        <h2>Nombre: '.$arrayAImprimir[$i]->getNombre().'</h2>
                ';
                                        if($arrayAImprimir[$i]->getActivo() == 0){
                                            print '<p>Articulo actualmente descatalogado o no disponible en este momento.</p>';
                                        } else{
                                            //si está activo mostrar precio, descuento y permitir añadirlo al carrito
                                            if($arrayAImprimir[$i]->getDescuento() !== 0){
                                                print'
                                                    <h4 style="text-decoration: line-through;">Precio: '.$arrayAImprimir[$i]->getPrecio().' € </h4>
                                                    <h2">Descuento: '.$arrayAImprimir[$i]->getDescuento().' % </h2>
                                                ';
                                            }
                                          print'  <h2>Precio: '. round($arrayAImprimir[$i]->getPrecio() * (1 - ($arrayAImprimir[$i]->getDescuento()/100)), 2).' € </h2>
                                            <button><a href="?codigo='.$arrayAImprimir[$i]->getCodigo().'&pag='.($paginaActual+1).'&orden='.$orden.'&atributo='.$nombreAtributo.'&categoria='.$categoriaFiltrada.'">Añadir al carrito  <i class="lni lni-cart-full" alt="Añadir al carrito"></i></a></button>
                                            ';
                                        }
                                        print'
                                    </div>
                                </div>
                            </td>
                ';
                if($i==2 || $i==5 || $i==8 ){ //si es un múltiplo de 3 crear línea nueva
                        print'</tr>
                        </div>';//fin del div row
                }
                if($i==count( $arrayAImprimir) -1){
                     print '</table>';
                }
            }
   //PAGINACIÓN
   print "<div class='paginacion'>";
   $paginasTotales = ceil(count($arrayArticulos) / $articulosAMostrar);//
   
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
           if($paginaActual +1 == $paginasTotales && $numeroIndicePaginacion == $paginasTotales){ //"siguiente" debe ser enlace o no? es lo que hacemos aquí
               print "<p>Siguiente</p>"; //en la ultima página esto NO debe ser un enlace
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
   /* ///DESHABILITADO, EN UNA TIENDA DE VERDAD HABRÍA QUE ESTUDIAR SI QUEREMOS MOSTRAR TODO O NO... PETACIÓN MUY POSIBLE
   //FORMULARIO PIE DE PÁGINA PARA ELEGIR LA PÁGINA A VER, Nº registros/pág
   $opcionesitemXpag=[3,4,5];
   if (isset($_GET['pag']) && ( $_GET['pag'] == "X" ) ){
       print "<b>Ver todos</b>";
   } else{
       print "<a href='?pag=X&ordenNombres=$orden'>Ver todos</a>";
   }
   print "</div>";//final del div de paginación
   */
?>
<?php
include_once("footer.php");
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

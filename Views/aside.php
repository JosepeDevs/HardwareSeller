<div class="col-lg-3 col-md-1 col-12">
    <aside>
        <a href="ArticuloBUSCAR.php">
            <img class="iconArribaTabla" src="../Resources/buscaAr.png" alt="recraft icon"/> Buscar artículo
        </a>
        <?php
        if(isset($_GET['categoria'])){
            print'        
            <a href="/Views/Catalogo.php">
                <i class="lni lni-eraser"></i> Limpiar filtros
            </a>';
        }
 
        // PREPARAR ARRAYS CON CATEGORIAS
        include_once("../Controllers/OrdenarCategoriasController.php");
        $orden="";
        $arrayCategorias = getArrayCategoriasOrdenados($orden);

        // DATOS DE LOS OBJETOS
        foreach($arrayCategorias as $categoria) {
            if($categoria->getActivo() ==  1){
                // Ver si hay subcategorias las que tengamos desactivadas existen pero no se muestran aquí
                $codigoCategoria = $categoria->getCodigo();
                $nombreCategoria = $categoria->getNombre();
                if(strlen($codigoCategoria)>=3){
                    //así solo se mostrarán categorias (2 digitos) y subcategorias (3 digitos)
                    continue;
                }
                if(strlen($codigoCategoria)==1){
                    //no vamos a mostrar los padres superiores
                    continue;
                }
                include_once("../Controllers/CategoriaBUSCARController.php");
                $subcategorias = $categoria->getSubCategorias($codigoCategoria); 
                if ($subcategorias !== false) {
                    // si hay categorías creamos un dropdown, solo mostraremos las categorias y subcategorias por ahora
                    print '<div class="dropdown">';
                    //atributo para bootstrap data-bs-toggle="dropdown" para decirle que el botón trigger el dropdown
                    //dropdown-toggle para que reconozca  lo de abajo como dropdown
                    print '<button class="btn btn-secondary dropdown-toggle revelador" type="button" data-bs-toggle="dropdown" >';
                    print $nombreCategoria.'<i class="lni lni-chevron-down"></i>';
                    print '</button>';
                    //la clase dropdown-menu es lo que da a una lista el formato de dropdown
                    print '<ul class="dropdown-menu">';
                    $ordenFiltrado = isset($_SESSION['orden'])? $_SESSION['orden'] : null;
                    $nombreAtributoFiltrado = isset($_SESSION['atributo'])? $_SESSION['atributo'] : null;
                    foreach ($subcategorias as $subcategoria) {
                        $codigoSubCategoria = $subcategoria->getCodigo();
                        $nombreSubCategoria = $subcategoria->getNombre();
                        if( $codigoSubCategoria !== $codigoCategoria){
                            //no queremos imprimir para la categoria RAM dentro de esta RAM otra vez, así que mientras no encuentre ese código que imprima subcategorias

                            print '<li class="oculto" >
                                    <a class="dropdown-item oculto" id="'.$codigoSubCategoria.'" href="Catalogo.php?categoria='.$codigoSubCategoria.'&orden='.$ordenFiltrado.'&atributo='.$nombreAtributoFiltrado.'">'.
                                    $nombreSubCategoria.'</a>
                                </li>';
                        }
                    }
                    print '</ul>';
                    print '</div>';
                } else {
                    $nombreCategoria = $categoria->getNombre();
                    $ordenFiltrado = isset($_SESSION['orden'])? $_SESSION['orden'] : null;
                    $nombreAtributoFiltrado = isset($_SESSION['atributo'])? $_SESSION['atributo'] : null;
                    //Si alguno no tiene subcategorias dejaremos un enlace con el nombre de la propia categoria, sin dropdown
                    print'<a  id="'.$codigoCategoria.'" href="Catalogo.php?categoria='.$codigoCategoria.'&orden='.$ordenFiltrado.'&atributo='.$nombreAtributoFiltrado.'">'.$nombreCategoria.'</a>';
                }
            }
        }  
        ?>
    </aside>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {

    document.querySelectorAll(".revelador").forEach(function(button) {//con esto seleccionamos todos los elements y aplicamos una funcion en cada item
       button.addEventListener("click", MostrarContenido); //añadir el listener a todos los botones
    });
    
    function MostrarContenido(){
        var botonClickeado = event.target;
        var ulCercana = botonClickeado.nextElementSibling;
        var listaLi = ulCercana.querySelectorAll('li');
        listaLi.forEach(function(li) {
            li.classList.toggle('oculto'); //toggle quita la clase si la tiene y la pone si no la tiene
            li.classList.toggle('visible'); 
            var enlace = li.querySelector('a');
            enlace.classList.toggle('oculto'); //toggle quita la clase si la tiene y la pone si no la tiene 
            enlace.classList.toggle('visible');  
        }        
    )};
});
</script>

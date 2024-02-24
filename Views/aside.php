<div class="col-lg-3 col-md-1 col-12">
    <aside>
        <a href="ArticuloBUSCAR.php">
            <img class="iconArribaTabla" src="../Resources/buscaAr.png" alt="recraft icon"/> Buscar artículo
        </a>
        <?php
        if(isset($_GET['categoria'])){
            echo'        
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
                if(strlen($codigoCategoria)>=3){
                    //así solo se mostrarán categorias (2 digitos) y subcategorias (3 digitos)
                    continue;
                }
                include_once("../Controllers/CategoriaBUSCARController.php");
                $subcategorias = $categoria->getSubCategorias($codigoCategoria); 
                if ($subcategorias !== false) {
                    // si hay categorías creamos un dropdown, solo mostraremos las categorias y subcategorias por ahora
                    //todo con ajax ir cargando subcategorias e ir haciendo un append para mejorar la navegación con el aside
                    echo '<div class="dropdown">';
                    //atributo para bootstrap data-bs-toggle="dropdown" para decirle que el botón trigger el dropdown
                    //dropdown-toggle para que reconozca  lo de abajo como dropdown
                    echo '<button class="btn btn-secondary dropdown-toggle revelador" type="button" data-bs-toggle="dropdown" >';
                    echo $categoria->getNombre().'<i class="lni lni-chevron-down"></i>';
                    echo '</button>';
                    //la clase dropdown-menu es lo que da a una lista el formato de dropdown
                    echo '<ul class="dropdown-menu">';
                    foreach ($subcategorias as $subcategoria) {
                        $codigoSubCategoria = $subcategoria->getCodigo();
                        $nombreSubCategoria = $subcategoria->getNombre();
                        if( $codigoSubCategoria !== $codigoCategoria){
                            //no queremos imprimir para la categoria RAM dentro de esta RAM otra vez, así que mientras no encuentre ese código que imprima subcategorias
                            echo '<li class="oculto" >
                                    <a class="dropdown-item oculto" id="'.$codigoSubCategoria.'" href="/Views/Catalogo.php?categoria='.$codigoSubCategoria.'">'.
                                    $nombreSubCategoria.'</a>
                                </li>';
                        }
                    }
                    echo '</ul>';
                    echo '</div>';
                } else {
                    //Si alguno no tiene subcategorias dejaremos un enlace con el nombre de la propia categoria, sin dropdown
                    echo '<a href="/Views/Catalogo.php?'.$codigoCategoria.'">'.$codigoCategoria.'</a>';
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

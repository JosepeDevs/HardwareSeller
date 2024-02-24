<div class="col-lg-3 col-md-1 col-12">
    <aside>
        <a href="ArticuloBUSCAR.php">
            <img class="iconArribaTabla" src="../Resources/buscaAr.png" alt="recraft icon"/> Buscar artículo
        </a>
        <?php
        // PREPARAR ARRAYS CON CATEGORIAS
        include_once("../Controllers/OrdenarCategoriasController.php");
        $orden="ASC";
        $arrayCategorias = getArrayCategoriasOrdenados($orden);

        // DATOS DE LOS OBJETOS
        foreach($arrayCategorias as $categoria) {
            if($categoria->getActivo() ==  1){
                // Ver si hay subcategorias
                include_once("../Controllers/CategoriaBUSCARController.php");
                $subcategorias = $categoria->getSubCategorias(); 
                if ($subcategorias !== false) {
                    // si hay categorías creamos un dropdown
                    echo '<div class="dropdown">';
                    echo '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" >';
                    echo $categoria->getNombre();
                    echo '</button>';
                    echo '<ul class="dropdown-menu">';
                    foreach ($subcategorias as $subcategoria) {
                        echo '<li><a class="dropdown-item" href="/Views/Catalogo.php?'.$subcategoria->getNombre().'">'.$subcategoria->getNombre().'</a></li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                } else {
                    //cuando no tenga más subcategorias solo hace falta un enlace
                    echo '<a href="/Views/Catalogo.php?'.$categoria->getNombre().'">'.$categoria->getNombre().'</a>';
                }
            }
        }  
        ?>
    </aside>
</div>
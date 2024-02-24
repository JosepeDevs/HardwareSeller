<div  class="col-lg-3 col-md-1 col-12">
        <aside>
            <a href="ArticuloBUSCAR.php">
                <img class="iconArribaTabla"  src="../Resources/buscaAr.png" alt="recraft icon"/> Buscar artículo
            </a>
            <a href="/Views/Catalogo.php?Pantallas">Pantallas</a>
            <?php
        //PREPARAR ARRAYS CON CATEGORIAS
        include_once("../Controllers/OrdenarCategoriasController.php");
        $orden="ASC";
        $arrayCategorias = getArrayCategoriasOrdenados($orden);

        //DATOS DE LOS OBJETOS
        foreach($arrayCategorias as $categoria) {
            if($categoria->getActivo() == 1){
                echo '<a href="/Views/Catalogo.php?'.$categoria->getNombre().'">'.$categoria->getNombre().'</a>';
            }
        } 
?>
        </aside>
    <div>
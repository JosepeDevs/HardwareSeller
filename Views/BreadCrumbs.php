<?php
print'
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Catalogo</a></li>';
    if(isset($_GET["categoria"])) {
        $codigoCategoria = $_GET["categoria"];
        include_once("../Controllers/CategoriaBUSCARController.php");
        $categoria = getCategoriaByCodigo($codigoCategoria);
        $nombreCategoria = $categoria->getNombre();
        echo'<li class="breadcrumb-item active"><a href="#">'.$nombreCategoria.'</a></li>
        ';
    }
 print' </ol>';
 ?>
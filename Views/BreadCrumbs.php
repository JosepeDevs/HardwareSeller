<?php
print'
  <div class="breadcrumb">
    <p class="breadcrumb-item"><a href="/Views/Catalogo.php"> HardWare Seller / </a></p>
    <p class="breadcrumb-item"><a href="/Views/Catalogo.php"> Catalogo / </a></p>';
    if(isset($_GET["categoria"])) {
        $codigoCategoria = $_GET["categoria"];
        include_once("../Controllers/CategoriaBUSCARController.php");
        $categoria = getCategoriaByCodigo($codigoCategoria);
        $nombreCategoria = $categoria->getNombre();
        echo'<p class="breadcrumb-item active"><a href="">'.$nombreCategoria.'</a></p>
        ';
    }
 print' </div>';
 ?>
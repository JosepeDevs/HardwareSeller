<?php
print'

  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 breadcrumb breadcrumbs">
    <p class="breadcrumb-item"><a href="/index.php"> HardWare Seller / </a></p>
    <p class="breadcrumb-item"><a href="/Views/Catalogo.php">  Catalogo / </a></p>';
    if(isset($_GET["categoria"])) {
      //estamos en la página de catálogo filtrada o no
        $codigoCategoria = $_GET["categoria"];
        include_once("../Controllers/CategoriaBUSCARController.php");
        $categoria = getCategoriaByCodigo($codigoCategoria);
        if($categoria !== false){
          $nombreCategoria = $categoria->getNombre();  
          print'<p class="breadcrumb-item active"><a href="Catalogo.php?categoria='.$codigoCategoria.'"> '.$nombreCategoria.' / </a></p>
          ';
        }else{
          print'<p class="breadcrumb-item active"><a href="Catalogo.php"> categoria_inexistente / </a></p>
          ';
        }
    }
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo']) ){
      //entonces estamos ya en ficha cliente
      print'
          <p><a class="breadcrumb-item " href="FichaArticulo.php?codigo='.$_REQUEST['codigo'].'">
              Artículo ='.$_REQUEST['codigo'].'  /
          </a></p>
      ';
  } 
 print' </div>';
 ?>
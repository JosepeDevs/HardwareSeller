<?php
echo '
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 breadcrumb breadcrumbs">
    <p class="breadcrumb-item"><a href="/index.php"> HardWare Seller / </a></p>
    <p><a class="breadcrumb-item"  href="AreaCliente.php">Area Personal / </a>
    ';
    if(isset($_REQUEST['idPedido']) && !empty($_REQUEST['idPedido']) ){
        echo'
            <a class="breadcrumb-item active" href="PedidoBUSCAR.php?idPedido='.$_REQUEST['idPedido'].'">
                Pedido id='.$_REQUEST['idPedido'].' 
            </a></p></div>
        ';
    } else{
        echo'<a class="breadcrumb-item active" href="PedidosLISTAR.php"> Mis pedidos  </a>
        </h2></div>';
    }

    ?>
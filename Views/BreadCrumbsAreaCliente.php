<?php
echo '
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 breadcrumb breadcrumbs">
    <p class="breadcrumb-item"><a href="/index.php"> HardWare Seller / </a></p>
    <p class="breadcrumb-item"><a href="AreaCliente.php">Area Personal / </a></p>
    ';
    if(isset($_REQUEST['idPedido']) && !empty($_REQUEST['idPedido']) ){
        //llegamos hasta pedido
        echo'<p class="breadcrumb-item "><a href="PedidosLISTAR.php"> Mis pedidos /</a></p>';
        echo'<p class="breadcrumb-item "><a href="PedidoBUSCAR.php?idPedido='.$_REQUEST['idPedido'].'">
                Pedido id='.$_REQUEST['idPedido'].' 
            </a></p>
        ';
    } else{
        echo'<p class="breadcrumb-item "><a  href="PedidosLISTAR.php"> Mis pedidos  </a></p>';
    }

    if(isset($_REQUEST['numPedido']) && !empty($_REQUEST['numPedido']) ){
        //entonces estamos ya en contenido pedido
        echo'<p class="breadcrumb-item "><a href="PedidosLISTAR.php"> Mis pedidos /</a></p>';
        echo'
            <p class="breadcrumb-item "><a href="PedidoBUSCAR.php?idPedido='.$_REQUEST['numPedido'].'">
                Pedido id='.$_REQUEST['numPedido'].' 
            </a></p>
        ';
        echo'
            <p class="breadcrumb-item "><a href="PedidoBUSCAR.php?numPedido='.$_REQUEST['numPedido'].'">
                Contenido del Pedido id='.$_REQUEST['numPedido'].' 
            </a></p>
        ';
    } 
echo'<div>';

    ?>
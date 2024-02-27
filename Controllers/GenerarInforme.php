<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

include_once("OperacionesSession.php");
$rolEsAdmin = AuthYRolAdmin();
if(!$rolEsAdmin) {
    session_destroy();
    echo "PedidoVALIDAR dice: no está user en session";
    header("Location: /index.php");
}

function EstadisticasUsuariosWeb(){

//nº activos
//nº inactivos

}




function EstadisticasArticulosWeb(){
//nº activos
//nº inactivos
//articulo más vendido

}
function EstadisticasPedidosTotales(){

//numero total de pedidos
//promedio gasto en pedidos
//total facturado

}

function EstadisticasPedidosRangoFechas(){

//numero total de pedidos
//promedio gasto en pedidos
//total facturado

}

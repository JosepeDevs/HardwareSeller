<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("header.php");

echo'<h1>Seleccione el método de pago</h1>';



//TODO llamaremos a un método para lo del pago recibiremos confirmación //
// revisamos aquí la respuesta si es JS hay que montar un header y body como si fuera el post de un formulario)
//si respuesta llega por php estará en session todo lo que hace falta (hay que llamar al controller de CrearPedido que a su vez llamara a AltaPedido
// este iniciará transaccion y creará pedido con los datos que necsite y luego hará el insert en contenidopedido y si todo ok fin de la transaccion)




include_once("header.php");
?>
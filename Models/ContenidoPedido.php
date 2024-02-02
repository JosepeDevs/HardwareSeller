<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedido dice: no está user en session";
    header("Location: /index.php");
}
include_once("../config/conectarBD.php");
include_once("../Controllers/Directorio.php");

class ContenidoPedido {

private $numPedido;
private $numLinea;
private $codArticulo;
private $cantidad;
private $precio;
private $descuento;

public function __construct($numPedido, $numLinea, $codArticulo, $cantidad, $precio, $descuento) {
    $this->numPedido = $numPedido;
    $this->numLinea = $numLinea;
    $this->codArticulo = $codArticulo;
    $this->cantidad = $cantidad;
    $this->precio = $precio;
    $this->descuento = $descuento;
}

public function getNumPedido() {
    return $this->numPedido;
}

public function setNumPedido($numPedido) {
    $this->numPedido = $numPedido;
}

public function getNumLinea() {
    return $this->numLinea;
}

public function setNumLinea($numLinea) {
    $this->numLinea = $numLinea;
}

public function getCodArticulo() {
    return $this->codArticulo;
}

public function setCodArticulo($codArticulo) {
    $this->codArticulo = $codArticulo;
}

public function getCantidad() {
    return $this->cantidad;
}

public function setCantidad($cantidad) {
    $this->cantidad = $cantidad;
}

public function getPrecio() {
    return $this->precio;
}

public function setPrecio($precio) {
    $this->precio = $precio;
}

public function getDescuento() {
    return $this->descuento;
}

public function setDescuento($descuento) {
    $this->descuento = $descuento;
}



}
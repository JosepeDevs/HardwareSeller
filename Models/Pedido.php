<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "Pedido dice: no está user en session";
    header("Location: /index.php");
}
include_once("../config/conectarBD.php");
include_once("../Controllers/Directorio.php");

class Pedido {

private $idPedido;
private $fecha;
private $total;
private $estado;
private $codUsuario;
private $activo;

public function __construct($idPedido=null, $fecha=null, $total=null, $estado=null, $codUsuario=null, $activo=null) {
    $this->idPedido = $idPedido;
    $this->fecha = $fecha;
    $this->total = $total;
    $this->estado = $estado;
    $this->codUsuario = $codUsuario;
    $this->activo = $activo;
}

public function getIdPedido() {
    return $this->idPedido;
}

public function setIdPedido($idPedido) {
    $this->idPedido = $idPedido;
}

public function getFecha() {
    return $this->fecha;
}

public function setFecha($fecha) {
    $this->fecha = $fecha;
}

public function getTotal() {
    return $this->total;
}

public function setTotal($total) {
    $this->total = $total;
}

public function getEstado() {
    return $this->estado;
}

public function setEstado($estado) {
    $this->estado = $estado;
}

public function getCodUsuario() {
    return $this->codUsuario;
}

public function setCodUsuario($codUsuario) {
    $this->codUsuario = $codUsuario;
}

public function getActivo() {
    return $this->activo;
}

public function setActivo($activo) {
    $this->activo = $activo;
}




}
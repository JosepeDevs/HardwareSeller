<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "Categoria dice: no está user en session";
    header("Location: /index.php");
}
include_once("../config/conectarBD.php");
include_once("../Controllers/Directorio.php");

class Categoria {

 private $codigo;
 private $nombre;
 private $activo;
 private $codCategoriaPadre;


 public function getCodigo() {
    return $this->codigo;
}

public function setCodigo($codigo) {
    $this->codigo = $codigo;
}

public function getNombre() {
    return $this->nombre;
}

public function setNombre($nombre) {
    $this->nombre = $nombre;
}

public function getActivo() {
    return $this->activo;
}

public function setActivo($activo) {
    $this->activo = $activo;
}

public function getCodCategoriaPadre() {
    return $this->codCategoriaPadre;
}

public function setCodCategoriaPadre($codCategoriaPadre) {
    $this->codCategoriaPadre = $codCategoriaPadre;
}


 public function __construct($codigo, $nombre, $activo, $coCategoriaPadre) {
    $this->codigo = $codigo;
    $this->nombre = $nombre;
    $this->activo = $activo;
    $this->coCategoriaPadre = $coCategoriaPadre;
}


}
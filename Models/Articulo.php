<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "Articulo dice: no está user en session";
    header("Location: index.php");
}
include_once("conectarBD.php");

class Articulo {

    private $codigo;
    private $nombre;
    private $descripcion;
    private $categoria;
    private $precio;
    private $imagen;

    public function getCodigo() { return $this->codigo; }
    public function setCodigo($codigo) { $this->codigo = $codigo; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }

    public function getDescripcion() { return $this->descripcion; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }

    public function getCategoria() { return $this->categoria; }
    public function setCategoria($categoria) { $this->categoria = $categoria; }

    public function getPrecio() { return $this->precio; }
    public function setPrecio($precio) { $this->precio = $precio; }

    public function getImagen() { return $this->imagen; }
    public function setImagen($imagen) { $this->imagen = $imagen; }

    public function __construct($codigo = null, $nombre = null, $descripcion = null, $categoria = null, $precio = null, $imagen = null) {
            $this->codigo = $codigo;
            $this->nombre = $nombre;
            $this->descripcion = $descripcion;
            $this->categoria = $categoria;
            $this->precio = $precio;
            $this->imagen = $imagen;
    }

    public static Function getAllArticulos(){
        try {
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `articulos`;";
            $statement=$con->prepare($sqlQuery);
            $statement->execute();
            $arrayArticulos=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
            return $arrayArticulos;
        } catch(PDOException $e) {
            $_SESSION['ErrorGetArticulos']= true;
        }
    }

    public static function getASCSortedArticulos() {
        try{
            $con= contectarBbddPDO();
            $sql="SELECT * FROM articulos ORDER BY nombre ASC";
            $statement=$con->prepare($sql);
            $statement->execute();
            $arrayArticulos=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
            return $arrayArticulos;
        }catch(PDOException $e) {
            $_SESSION['ErrorGetArticulos']= true;
        }
    }
    public static function getDESCSortedArticulos() {
        try {
            $con= contectarBbddPDO();
            $sql="SELECT * FROM articulos ORDER BY nombre DESC";
            $statement=$con->prepare($sql);
            $statement->execute();
            $arrayArticulos=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
            return $arrayArticulos;
        }catch(PDOException $e) {
            $_SESSION['ErrorGetArticulos']= true;
        }
    }
}
?>
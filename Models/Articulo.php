<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "Articulo dice: no está user en session";
    header("Location: index.php");
}
include_once("../config/conectarBD.php");

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

    public function GetArticuloByCodigo($codigo){
        try{
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `articulos` WHERE codigo=:codigo;";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':codigo', $codigo);
            $statement->execute();
            $articulo=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
            return $articulo;
        } catch(PDOException $e) {
            $_SESSION['ErrorGetArticulos']= true;
            return false;
        }
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

    public static function AltaArticulo($articulo){
        include_once("conectarBD.php");
        include_once("Directorio.php");
        $_SESSION["nuevoArticulo"]=false;

        //rescatamos de session los datos subidos por ValidarDatos
        $nombre = $articulo->getNombre();
        $codigo =  $articulo->getCodigo();
        $descripcion = $articulo->getDescripcion();
        $categoria = $articulo->getCategoria();
        $precio = $articulo->getPrecio();
        $imagen = $articulo->getImagen();

        try{
            $con = contectarBbddPDO();
            $sqlQuery="INSERT INTO `articulos` (`codigo`, `nombre`, `descripcion`, `categoria`, `precio`, `imagen`)
                                        VALUES (:codigo, :nombre, :descripcion, :categoria, :precio, :imagen);";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':codigo', $codigo);
            $statement->bindParam(':nombre', $nombre);
            $statement->bindParam(':descripcion', $descripcion);
            $statement->bindParam(':categoria', $categoria);
            $statement->bindParam(':precio', $precio);
            $statement->bindParam(':imagen', $imagen);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
            $resultado = $statement->fetch();

            if ($resultado !== false && $resultado->rowCount() == 0) {
                $_SESSION['BadInsertArticulo']= true;
                if (file_exists($imagen)) {//si no se realiza la operación borramos la imagen (aquí ya se había movido)
                    unlink($imagen);
                }
            } else {
                $_SESSION['GoodInsertArticulo']= true;
            }
            header("Location: ArticulosLISTAR.php");
            exit;
        } catch(PDOException $e) {
            $_SESSION['BadInsertArticulo']= true;
            header("Location: ArticuloALTA.php");
            exit;
        };
    }

    public function getArrayAtributos($codigoOriginal){
        include_once("/../Models/Articulo.php");
            $reflejo = new ReflectionClass('Articulo');
            $arrayAtributos = $reflejo->getProperties(ReflectionProperty::IS_PRIVATE);//como hemos puesto todos private vamos a meter esos en un array
            return $arrayAtributos;
    }

    public function borradoLogico($codigo){
        include_once("/../config/conectarBD.php");
        try {
            $conPDO=contectarBbddPDO();
            $query=("UPDATE articulos SET activo=false WHERE codigo=:codigo");
            $statement= $conPDO->prepare($query);
            $statement->bindParam(':codigo', $codigo);
            $operacionConfirmada = $statement->execute();
            /*
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Articulo');
            $operacionConfirmada= $statement->fetch();
            */
            if($operacionConfirmada){
                $_SESSION['ExitoBorrandoArticulo'] = false;
            } else {
                $_SESSION['ExitoBorrandoArticulo'] = true;
            }
            return $operacionConfirmada;
        } catch(PDOException $e) {
            $_SESSION['BadOperation'] = true;
            return false;
        };
    }



};
?>
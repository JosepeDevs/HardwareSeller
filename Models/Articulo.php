<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "Articulo dice: no está user en session";
    header("Location: ../index.php");
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

    /**
     * @return array|bool devuelve array de atributos  del artículo (propiedades privadas), devuelve false si no sale bien
     */
    public static function getArrayAtributos(){
        include_once("../Models/Articulo.php");
            $reflejo = new ReflectionClass('Articulo');
            if (!empty($arrayAtributos)) {
                return $arrayAtributos;
            } else {
                return false;
            }
    }

    public function borradoLogico($codigo){
        include_once("../config/conectarBD.php");
        try {
            $conPDO=contectarBbddPDO();
            $query=("UPDATE articulos SET activo=false WHERE codigo=:codigo");
            $statement= $conPDO->prepare($query);
            $statement->bindParam(':codigo', $codigo);
            $operacionConfirmada = $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Articulo');
            $operacionConfirmada= $statement->fetch();
            if($operacionConfirmada !== false){
                $_SESSION['ExitoBorrandoArticulo'] = true;
            } else {
                $_SESSION['ExitoBorrandoArticulo'] = false;
            }
            return $operacionConfirmada;
        } catch(PDOException $e) {
            $_SESSION['BadOperation'] = true;
            return false;
        };
    }

        /**
         * comprueba que el código generado sea adecuado: 8 caracteres, 3 de los primeros letras y los 5 últimos numeros.
         * @param string string con codigo identificador del articulo
         * @return bool true si cumple criterio de 3 letras (da igual minus, esta función lo pasa primero a mayus) y 5 números. si no se cumple este criterio se devuelve false.
         *
         *  */
        public function EsFormatoCodigoCorrecto($codigo){
            $codigo = strtoupper($codigo);
            $longitud = strlen($codigo);
            $regex = "/^[a-zA-Z]{3}[0-9]{5}$/";
            $formatoValido = preg_match($regex,$codigo);

            if( $longitud == 8 && $formatoValido){
                return true;
            }else{
                return false;
            }
        }

        /**
         * @param string recibe código y lo devuelve con 0s a la izquierda y en mayusculas para que cumpla 3 letras y 5 numeros.
         * @return string|bool si le pasas 3 letras y algun numero entre 0 y 99999 devuelve el código formateado. p.e. cat1 devuelve CAT00001, si le pasas CON12345 devuelve CON12345. Si no hay parte numerica o si el numero es demasiado grande devuelve false.
         */
        public function TransformarCodigo($codigo){
            $codigo = strtoupper($codigo);
            $letras=substr($codigo, 0, 3);//del 0 al 3 (las letras son obligatorias)
            $letras = strtoupper($letras);
            $numeros=substr($codigo,3);//de la posicion 3 al final

            if($numeros == "" || $numeros == null ){
                $_SESSION['SinNumero'] = true;
                return false;
            }

            if(intval($numeros) > 99999){
                $_SESSION['NumeroGrande'] = true;
                return false;
            }

            while( strlen($numeros) <= 4 ){
                $numeros = "0".$numeros;
            }
            $codigo=$letras.$numeros;
            return $codigo;
            }

    /**
     * Función que comprueba si el código está libre y devuelve bool con el resultado.
     * @param string codigo a comprobar 3 letras 5 numeros
     * @return bool devuelve false si el código ya está en uso, devuelve true si el código está libre (no hay resultados que ya estén usando ese código)
     */
    public function CodigoLibre($codigo){
        include_once("../config/conectarBD.php");
        try {
            $conPDO = contectarBbddPDO();
            $codigoCheck = "SELECT * FROM `articulos` WHERE codigo = :codigo";
            $statement = $conPDO->prepare($codigoCheck);
            $statement->bindParam(':codigo', $codigo);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,"Articulo");
            $resultado = $statement->fetch();
            if ( $resultado !== false) {
                return false;
            } else {
                return true;
            }
        } catch(PDOException $e) {
            $_SESSION['BadOperation']= true;
            return false;
        };
    }
}
 /*
        //tests, si aparece "1" junto a good/bad es que el test ha funcionado bien.
        $codigo1="INF00001";
        $codigo2="ca00002";
        $codigo3="cat0001";
        $codigo4="cat5634";
        $codigo5="cat16";


        $codigo1= TransformarCodigo($codigo1);
        $codigo2= TransformarCodigo($codigo2);
        $codigo3= TransformarCodigo($codigo3);
        $codigo4= TransformarCodigo($codigo4);
        $codigo5= TransformarCodigo($codigo5);

        if(EsFormatoCodigoCorrecto($codigo1)==true){echo "good1 ";}else{echo "bad ";};// test OK
        if(EsFormatoCodigoCorrecto($codigo2)==true){echo "good ";}else{echo "BAD1 ";};// test OK
        if(EsFormatoCodigoCorrecto($codigo3)==true){echo "good1 ";}else{echo "bad ";};// test OK
        if(EsFormatoCodigoCorrecto($codigo4)==true){echo "good1 ";}else{echo "bad ";};// test OK
        if(EsFormatoCodigoCorrecto($codigo5)==true){echo "good1 ";}else{echo "bad ";};// test OK


        if(CodigoLibre($codigo1)==true){echo "good1 ";}else{echo "bad ";};// test OK
        if(CodigoLibre($codigo2)==true){echo "good1 ";}else{echo "bad ";};// test OK
        if(CodigoLibre($codigo3)==true){echo "good ";}else{echo "BAD1 ";};// test OK
        if(CodigoLibre($codigo4)==true){echo "good1 ";}else{echo "bad ";};// test OK
        if(CodigoLibre($codigo5)==true){echo "good1 ";}else{echo "bad ";};// test OK

        $codigo6="cat";
        $codigo7="cat123456";
        $codigo6= TransformarCodigo($codigo6);
        $codigo7= TransformarCodigo($codigo7);
        if(EsFormatoCodigoCorrecto($codigo6)==false){echo "good1a ";}else{echo "bad ";};// test OK (NO SE EJECUTA)=OK
        if(EsFormatoCodigoCorrecto($codigo7)==false){echo "good1a ";}else{echo "bad ";};// test OK(NO SE EJECUTA)=OK
        if(CodigoLibre($codigo6)==true){echo "good1a ";}else{echo "bad ";};// test OK(NO SE EJECUTA)=OK
        if(CodigoLibre($codigo7)==true){echo "good1a ";}else{echo "bad ";};// test OK(NO SE EJECUTA)=OK
        */

?>
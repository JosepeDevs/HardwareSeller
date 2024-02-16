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
private $codContenidoPedido;
private $cantidad;
private $precio;
private $descuento;

public function __construct($numPedido=null, $numLinea=null, $codContenidoPedido=null, $cantidad=null, $precio=null, $descuento=null) {
    $this->numPedido = $numPedido;
    $this->numLinea = $numLinea;
    $this->codContenidoPedido = $codContenidoPedido;
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

public function getCodContenidoPedido() {
    return $this->codContenidoPedido;
}

public function setCodContenidoPedido($codContenidoPedido) {
    $this->codContenidoPedido = $codContenidoPedido;
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


    /**
     * @return bool|ContenidoPedido devuelve false si falla, devuelve el ContenidoPedido si lo encuentra consultando el código
     */
    public static function GetContenidoPedidoByNumPedido($numPedido){
        try{
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `contenidopedido` WHERE numPedido=:numPedido;";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':numPedido', $numPedido);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "ContenidoPedido");
            $ContenidoPedido = $statement->fetch();
            if(empty($ContenidoPedido)){
                $_SESSION['numPedidoNotFound'] = true;
                return false;
            }else{
                return $ContenidoPedido;
            }
        } catch(PDOException $e) {
            $_SESSION['ErrorGetContenidoPedidos']= true;
            return false;
        }
    }

        /**
     * @return bool|array devuelve false si falla, devuelve el ContenidoPedido o array de ContenidoPedidos si  encuentra 1 o más ContenidoPedidos que coincida el texto buscado (en codArticulo)
     */
    public static function GetContenidoPedidosByBusquedaCodArticulo($numPedido){
        try{
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `contenidopedido` WHERE CodArticulo =:CodArticulo";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':CodArticulo', $CodArticulo);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "ContenidoPedido");
            $arrayContenidoPedido = $statement->fetchAll();
            if(empty($arrayContenidoPedido)){
                $_SESSION['codArticuloNotFound'] = true;
                return false;
            }else{
                return $arrayContenidoPedido;
            }
        } catch(PDOException $e) {
            $_SESSION['ErrorGetContenidoPedidos']= true;
            return false;
        }
    }

    public static function ComprobarLongitud($string, $longitud) {
        if(strlen($string) > $longitud) {
            return false;
        }
        return true;
    }
    public static Function getAllContenidoPedidos(){
        try {
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `contenidopedido`;";
            $statement=$con->prepare($sqlQuery);
            $statement->execute();
            $arrayContenidoPedidos=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "ContenidoPedido");
            return $arrayContenidoPedidos;
        } catch(PDOException $e) {
            $_SESSION['ErrorGetContenidoPedidos']= true;
        }
    }

    public static function getASCSortedContenidoPedidos() {
        try{
            $con= contectarBbddPDO();
            $sql="SELECT * FROM contenidopedido ORDER BY codArticulo ASC";
            $statement=$con->prepare($sql);
            $statement->execute();
            $arrayContenidoPedidos=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "ContenidoPedido");
            return $arrayContenidoPedidos;
        }catch(PDOException $e) {
            $_SESSION['ErrorGetContenidoPedidos']= true;
        }
    }

    public static function getDESCSortedContenidoPedidos() {
        try {
            $con= contectarBbddPDO();
            $sql="SELECT * FROM contenidopedido ORDER BY codArticulo DESC";
            $statement=$con->prepare($sql);
            $statement->execute();
            $arrayContenidoPedidos=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "ContenidoPedido");
            return $arrayContenidoPedidos;
        }catch(PDOException $e) {
            $_SESSION['ErrorGetContenidoPedidos']= true;
        }
    }

    /**
     * @return bool returns true si la inserción es exitosa, si no, false
     */
    public static function AltaContenidoPedido($numPedido, $numLinea,$codArticulo, $cantidad, $precio, $descuento){
        $_SESSION["nuevoContenidoPedido"]=false;
        try{
            $con = contectarBbddPDO();
            $sqlQuery="INSERT INTO `ContenidoPedidos` (`numPedido`, `numLinea`, `codArticulo`, `cantidad`, `precio`, `descuento`)
                                        VALUES (:numPedido, :numLinea, :codArticulo, :cantidad, :precio, :descuento);";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':numPedido', $numPedido);
            $statement->bindParam(':numLinea', $numLinea);
            $statement->bindParam(':codArticulo', $codArticulo);
            $statement->bindParam(':cantidad', $cantidad);
            $statement->bindParam(':precio', $precio);
            $statement->bindParam(':descuento', $descuento);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "ContenidoPedido");
            $resultado = $statement->fetch();

            if ($resultado !== false && $resultado->rowCount() == 0) {
                $_SESSION['BadInsertContenidoPedido']= true;
                return false;
            } else {
                $_SESSION['GoodInsertContenidoPedido']= true;
                return true;
            }
        } catch(PDOException $e) {
            $_SESSION['BadInsertContenidoPedido']= true;
           // $_SESSION['error_message'] = $e->getMessage();
            //$error= $_SESSION['error_message'];
            return false;
        };
    }

    /**
     * @return array devuelve array de atributos  del artículo (propiedades privadas)
     */
    public static function getArrayAtributosContenidoPedido() {
        $reflector = new ReflectionClass('ContenidoPedido');
        $atributos = $reflector->getProperties(ReflectionProperty::IS_PRIVATE);
        $arrayAtributosContenidoPedido = array();
        foreach ($atributos as $propiedad) {
            if ($propiedad->isPrivate()) {
                $arrayAtributosContenidoPedido[] = $propiedad->getName();
            }
        }
        return $arrayAtributosContenidoPedido;
    }


    public function borradoLogico($numPedido){
        try {
            $conPDO=contectarBbddPDO();
            $query=("UPDATE ContenidoPedidos SET activo=false WHERE numPedido=:numPedido");
            $statement= $conPDO->prepare($query);
            $statement->bindParam(':numPedido', $numPedido);
            $operacionConfirmada = $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'ContenidoPedido');
            $operacionConfirmada= $statement->fetch();
            if($operacionConfirmada !== false){
                $_SESSION['ExitoBorrandoContenidoPedido'] = true;
            } else {
                $_SESSION['ExitoBorrandoContenidoPedido'] = false;
            }
            return $operacionConfirmada;
        } catch(PDOException $e) {
            $_SESSION['BadOperation'] = true;
            return false;
        };
    }

    /**
     * @return bool true si hay éxito, false si no es el caso.
     */
    public function updateContenidoPedido($numPedido, $numPedidoOriginal, $numLinea, $codArticulo, $cantidad, $precio, $descuento){
    //una vez aquí dentro hay que "reiniciar" el valor de "editando"
    $_SESSION["editandoContenidoPedido"]="false";
    $conPDO = contectarBbddPDO();

    $mantienennumPedido = ($numPedido == $numPedidoOriginal || $numPedido == null);

    try{
        $conPDO = contectarBbddPDO();
        if( $mantienennumPedido){
            $sqlQuery = " UPDATE `ContenidoPedidos`
                    SET `numPedido` = :numPedidoOriginal, `numLinea` = :numLinea, `codArticulo` = :codArticulo, `cantidad` = :cantidad, `precio` = :precio,`descuento` = :descuento
                    WHERE `numPedido` = :numPedidoOriginal "
            ;
        } else{
            $sqlQuery = " UPDATE `ContenidoPedidos`
                    SET `numPedido` = :numPedido, `numLinea` = :numLinea,  `codArticulo` = :codArticulo, `cantidad` = :cantidad, `precio` = :precio, `descuento` = :descuento
                    WHERE `numPedido` = :numPedidoOriginal "
            ;
        }
        echo "<br>UpdateContenidoPedido says: numPedido nuevo: $numPedido"." y numPedido original: ".$numPedidoOriginal."<br>";
        echo "<br>UpdateContenidoPedido says:".$sqlQuery;
        $statement= $conPDO->prepare($sqlQuery);

        if($mantienennumPedido){
            $statement->bindParam(':numPedidoOriginal', $numPedidoOriginal);
        }else{
            $statement->bindParam(':numPedido', $numPedido);
            $statement->bindParam(':numPedidoOriginal', $numPedidoOriginal);
        }

        $statement->bindParam(':numLinea', $numLinea);
        $statement->bindParam(':codArticulo', $codArticulo);
        $statement->bindParam(':cantidad', $cantidad);
        $statement->bindParam(':precio', $precio);
        $statement->bindParam(':descuento', $descuento);

        $operacionRealizada = $statement->execute();

        if($operacionRealizada == false && $statement->rowCount() <= 0 ){
            //si SQL no se ejecuta, hay que deshacer lo hecho (solo queremos borrar si estamos subiendo imagen nueva, la que ya tenía no hay que borrarla)
            $_SESSION['GoodUpdateContenidoPedido']= false;
            $_SESSION['BadUpdateContenidoPedido']= true;
            return false;
        } else{
            $_SESSION['GoodUpdateContenidoPedido']= true;
            return true;
        }
    } catch(PDOException $e) {
        $_SESSION['OperationFailed'] = true;
        return false;
    };
    }

    /**
     * Función que comprueba si el código está libre y devuelve bool con el resultado.
     * @param string numPedido a comprobar 3 letras 5 numeros
     * @return bool devuelve false si el código ya está en uso, devuelve true si el código está libre (no hay resultados que ya estén usando ese código)
     */
    public static function numPedidoLibre($numPedido){
        try {
            $conPDO = contectarBbddPDO();
            $numPedidoCheck = "SELECT * FROM `ContenidoPedidos` WHERE numPedido = :numPedido";
            $statement = $conPDO->prepare($numPedidoCheck);
            $statement->bindParam(':numPedido', $numPedido);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,"ContenidoPedido");
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

    /**
     * @param string acepta un numero como texto "55.3" y lo transforma en float
     * @return float|bool devuelve como float el texto comprobado, si no se pudo transformar o si finalmente no tiene valor de float se devuelve false. P.e. 55,6 devolverá 55.6 al igual que 11.5 devolverá 11.5 mientras que a22,5 devolverá false.
    */
    public static function ValorFloat($float){
        //si son españoles y escriben la coma como una coma hay que cambiarla por un punto
        $float = str_replace( "," , "." , $float);
        $float = (float)$float;
        $float = floatval($float);//según manual parece que a esta función se la pela si llega una coma o un punto, mientras no empiece por letras we are good.
        if(is_float($float)){
            return $float;
        } else{
            return false;
        }
    }


}
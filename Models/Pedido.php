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
//todo poner a todas cosas que puedan llegar por get htmlspecialchars para que no intenten meternos M.... y revisar que todas las funciones que devuelven pedido tengan
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

public static function borradoLogicoPedido($idPedido){
    try {
        include_once("../Controllers/ContenidoPedidoBORRARController.php");
        $operacion1Confirmada = borradoLogicoContenidoPedido($idPedido);

        $conPDO=contectarBbddPDO();
        $query=("UPDATE pedidos SET activo=0 WHERE idPedido=:idPedido");
        $statement= $conPDO->prepare($query);
        $statement->bindParam(':idPedido', $idPedido);
        $operacion2Confirmada = $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Pedido');
        $operacion2Confirmada= $statement->fetch();
        if($operacion2Confirmada !== false && $operacion1Confirmada !== false){
            $_SESSION['ExitoBorrandoPedido'] = true;
        } else {
            $_SESSION['FalloBorrandoPedido'] = true;
        }
        return $operacion2Confirmada; //devolvemos el pedido, a partir de ahí pueden acceder al contenido si hace falta
    } catch(PDOException $e) {
        $_SESSION['BadOperation'] = true;
        return false;
    };
}

    /**
     * @return bool|Pedido devuelve false si falla, devuelve el Pedido si lo encuentra consultando el código
     */
    public static function GetPedidoByidPedido($idPedido, $dni=null){
        try{
            $con = contectarBbddPDO();
            if($dni !== null){
                $sqlQuery="SELECT * FROM  `pedidos` WHERE idPedido=:idPedido AND codUsuario=:dni AND activo=1;";
            } else{
                $sqlQuery="SELECT * FROM  `pedidos` WHERE idPedido=:idPedido ;";
            }
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':idPedido', $idPedido);
            if($dni !== null){
                $statement->bindParam(':dni', $dni);
            }
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            $Pedido = $statement->fetch();
            if(empty($Pedido)){
                $_SESSION['idPedidoNotFound'] = true;
                return false;
            }else{
                return $Pedido;
            }
        } catch(PDOException $e) {
            $_SESSION['ErrorGetPedidos']= true;
            return false;
        }
    }

    /**
     * @return bool|Pedido devuelve false si falla, devuelve el Pedido si lo encuentra consultando el código
     */
    public static function getPedidosByEstado($estado, $dni=null){
        try{
            $con = contectarBbddPDO();
            if($dni !== null){
                $sqlQuery="SELECT * FROM  `pedidos` WHERE estado=:estado AND codUsuario=:dni AND activo=1;";
            } else{
                $sqlQuery="SELECT * FROM  `pedidos` WHERE estado=:estado ;";
            }
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':estado', $estado);
            if($dni !== null){
                $statement->bindParam(':dni', $dni);
            }
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            $Pedido = $statement->fetch();
            if(empty($Pedido)){
                $_SESSION['EstadoNotFound'] = true;
                return false;
            }else{
                return $Pedido;
            }
        } catch(PDOException $e) {
            $_SESSION['ErrorGetPedidos']= true;
            return false;
        }
    }

    /**
     * @return bool|array devuelve false si falla, devuelve el Pedido si lo encuentra consultando el código
     */
    public static function getPedidosByCodUsuario($codUsuario, $dni=null){
        try{
            $con = contectarBbddPDO();
            if($dni !== null){
                $sqlQuery="SELECT * FROM  `pedidos` WHERE codUsuario=:dni AND activo=1;";
            } else{ 
                $sqlQuery="SELECT * FROM  `pedidos` WHERE codUsuario=:codUsuario;";
            }
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':codUsuario', $codUsuario);
            if($dni !== null){
                //el que quiera hacerse el listo poniendo un dni al azar solo verá lo que su propio dni tiene bwjajajaajaja
                $statement->bindParam(':dni', $dni);
            }
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            $arrayPedidos = $statement->fetchAll();
            if(empty($arrayPedidos)){
                $_SESSION['codUsuarioNotFound'] = true;
                return false;
            }else{
                return $arrayPedidos;
            }
        } catch(PDOException $e) {
            $_SESSION['ErrorGetPedidos']= true;
            return false;
        }
    }

    /**
     * @return bool|array devuelve false si falla, devuelve el Pedido si lo encuentra consultando el código
     */
    public static function GetPedidosByRangoFecha($fechaInicio,$fechaFin, $dni=null ){
        try{
            $con = contectarBbddPDO();
            if($dni !== null){
                $sqlQuery="SELECT * FROM  `pedidos` WHERE fecha >= :fechaInicio AND fecha <= :fechaFin AND codUsuario=:dni AND activo=1;";
            } else{
                $sqlQuery="SELECT * FROM  `pedidos` WHERE fecha >= :fechaInicio AND fecha <= :fechaFin;";
            }
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':fechaFin', $fechaFin);
            $statement->bindParam(':fechaInicio', $fechaInicio);
            if($dni !== null){
                $statement->bindParam(':dni', $dni);
            }
            $statement->execute();
            $resultados = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            if(empty($resultados)){
                $_SESSION['idPedidoNotFound'] = true;
                return false;
            }else{
                return $resultados;
            }
        } catch(PDOException $e) {
            $_SESSION['ErrorGetPedidos']= true;
            return false;
        }
    }


    public static function ComprobarLongitud($string, $longitud) {
        if(strlen($string) > $longitud) {
            return false;
        }
        return true;
    }

    public static Function getAllPedidos($dni=null){
        try {
            $con = contectarBbddPDO();
            if($dni !== null) {
                $sql = "SELECT * FROM pedidos WHERE codUsuario=:dni AND activo=1";//si el usuario "borra" un pedido tendrá que dejar de verlo, por eso el activo=1
            }else{
                $sql = "SELECT * FROM pedidos";
            }
            $statement = $con->prepare($sql);
            if($dni !== null) {
                $statement->bindParam(":dni", $dni);
            }
            $statement->execute();
            $arrayPedidos = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            return $arrayPedidos;
        } catch (PDOException $e) {
            $_SESSION['ErrorGetPedidos'] = true;
        }
    }


    public static function getASCSortedPedidosByAtributo($nombreAtributo, $dni=null) {
        try {
            $con = contectarBbddPDO();
            $nombreAtributoLimpio = htmlspecialchars($nombreAtributo);//quitamos cosas que nos intente inyectarSQL
            if($dni !== null) {
                $sql = "SELECT * FROM pedidos WHERE codUsuario=:dni AND activo=1 ORDER BY {$nombreAtributoLimpio} ASC";
            } else{
                $sql = "SELECT * FROM pedidos ORDER BY {$nombreAtributoLimpio} ASC";
            }
            $statement = $con->prepare($sql);
            if($dni !== null) {
                $statement->bindParam(":dni", $dni);
            }
            $statement->execute();
            $arrayPedidos = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            return $arrayPedidos;
        } catch (PDOException $e) {
            $_SESSION['ErrorGetPedidos'] = true;
        }
    } 

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
    /**
     * @return  Pedido|bool devuelve false si no encuentra el pedido, devuelve el pedido si tiene exito
     */
    public static function getPedidoByNumPedido($numPedido, $dni=null) {
        try {
            $con = contectarBbddPDO();
            if($dni!==null) {
                $sql = "SELECT * FROM pedidos WHERE numPedido=:numPedido AND codUsuario=:dni";
            } else{
                $sql = "SELECT * FROM pedidos WHERE numPedido=:numPedido";
            }
            $statement = $con->prepare($sql);
            if($dni!==null) {
                $statement->bindParam(":dni", $dni);
            }
            $statement->bindParam(':numPedido', $numPedido);
            $statement->execute();
            $pedido = $statement->fetch(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");

            if($pedido->RowCount() > 0) {
                return $pedido;
            } else {
                $_SESSION['ErrorGetPedidos'] = true;
                return false;
            }
        } catch (PDOException $e) {
            $_SESSION['ErrorGetPedidos'] = true;
            return false;
        }
    }
    public static function getDESCSortedPedidosByAtributo($nombreAtributo, $dni=null) {
        if($dni == null) {
            try {
                $con = contectarBbddPDO();
                $nombreAtributoLimpio = htmlspecialchars($nombreAtributo);//quitamos cosas que nos intente inyectarSQL
                $sql = "SELECT * FROM pedidos ORDER BY {$nombreAtributoLimpio} DESC";
                $statement = $con->prepare($sql);
                $statement->execute();
                $arrayPedidos = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
                return $arrayPedidos;
            } catch (PDOException $e) {
                $_SESSION['ErrorGetPedidos'] = true;
            }
        } else {
            try {
                $con = contectarBbddPDO();
                $nombreAtributoLimpio = htmlspecialchars($nombreAtributo);//quitamos cosas que nos intente inyectarSQL
                $sql = "SELECT * FROM pedidos WHERE codUsuario=:dni ORDER BY {$nombreAtributoLimpio} DESC";
                $statement = $con->prepare($sql);
                $statement->bindParam(":dni", $dni);
                $statement->execute();
                $arrayPedidos = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
                return $arrayPedidos;
            } catch (PDOException $e) {
                $_SESSION['ErrorGetPedidos'] = true;
            }
        }
    }

    /**
     * @return int|bool returns numPedido si inserción es exitosa, si no, false
     */
    public static function AltaPedido($fecha, $total, $estado, $codUsuario, $activo){
        $_SESSION["nuevoPedido"]=false;
        try{
            $con = contectarBbddPDO();
            $sqlQuery="INSERT INTO `pedidos` (`fecha`, `total`, `estado`, `codUsuario`, `activo`)
                                        VALUES (:fecha, :total, :estado, :codUsuario, :activo);";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':fecha', $fecha);
            $statement->bindParam(':total', $total);
            $statement->bindParam(':estado', $estado);
            $statement->bindParam(':codUsuario', $codUsuario);
            $statement->bindParam(':activo', $activo);
            $statement->execute();
            $numPedido = $con->lastInsertId();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            $resultado = $statement->fetch();

            if ($resultado !== false && $resultado->rowCount() == 0) {
                $_SESSION['BadInsertPedido']= true;
                return false;
            } else {
                $_SESSION['GoodInsertPedido']= true;
                return $numPedido;
            }
        } catch(PDOException $e) {
            $_SESSION['BadInsertPedido']= true;
           // $_SESSION['error_message'] = $e->getMessage();
            //$error= $_SESSION['error_message'];
            return false;
        };
    }

    /**
     * @return array devuelve array de atributos  del pedido (propiedades privadas)
     */
    public static function getArrayAtributosPedido() {
        $reflector = new ReflectionClass('Pedido');
        $atributos = $reflector->getProperties(ReflectionProperty::IS_PRIVATE);
        $arrayAtributosPedido = array();
        foreach ($atributos as $propiedad) {
            if ($propiedad->isPrivate()) {
                $arrayAtributosPedido[] = $propiedad->getName();
            }
        }
        return $arrayAtributosPedido;
    }

    /**
     * @return bool true si no encuentra esta 6 pagado 7 enviado 8 recibido o 9 finalizado en alguna posicion de estado (lo pasa a string para comprobarlo)no permitimos cancelación
     */
    public static  function SePuedeCancelarPedido($estado){
        $estado = (string)$estado;//lo pasamos a string
        if(
            strpos($estado, '6') !== false ||
            strpos($estado, '7') !== false ||
            strpos($estado, '8') !== false ||
            strpos($estado, '9') !== false
        ) {
            ///si encuentra en alguna posicion de estado alguno de los numeros (pagado, enviado, recibido) no permitimos cancelación
            return false;
        } else{
            return true;
        }
    }


    public function borradoLogico($idPedido){
        try {
            $conPDO=contectarBbddPDO();
            $query=("UPDATE pedidos SET activo=false WHERE idPedido=:idPedido");
            $statement= $conPDO->prepare($query);
            $statement->bindParam(':idPedido', $idPedido);
            $operacionConfirmada = $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Pedido');
            $operacionConfirmada= $statement->fetch();
            if($operacionConfirmada !== false){
                $_SESSION['ExitoBorrandoPedido'] = true;
            } else {
                $_SESSION['ExitoBorrandoPedido'] = false;
            }
            return $operacionConfirmada;
        } catch(PDOException $e) {
            $_SESSION['BadOperation'] = true;
            return false;
        };
    }
    /**
     * @return bool Devuelve true si tiene éxito, false si no es el caso.
     */
    public function updatePedido($numPedido, $fecha, $total, $estado, $codUsuario, $activo){
    //una vez aquí dentro hay que "reiniciar" el valor de "editando"
    $_SESSION["editandoPedido"]="false";

    $conPDO = contectarBbddPDO();

    try{
        $conPDO = contectarBbddPDO();
        $sqlQuery = " UPDATE `pedidos`
                SET `fecha` = :fecha, `total` = :total, `estado` = :estado, `codUsuario` = :codUsuario, `activo` = :activo
                WHERE `numPedido` = :numPedido "
        ;

        $statement= $conPDO->prepare($sqlQuery);

        $statement->bindParam(':numPedido', $numPedido);
        $statement->bindParam(':fecha', $fecha);
        $statement->bindParam(':total', $total);
        $statement->bindParam(':estado', $estado);
        $statement->bindParam(':codUsuario', $codUsuario);
        $statement->bindParam(':activo', $activo);
        $operacionRealizada = $statement->execute();

        if($operacionRealizada == false && $statement->rowCount() <= 0){
            $_SESSION['BadUpdatePedido']= true;
            $_SESSION['GoodUpdatePedido']= false;
            return false;
        } else{
            $_SESSION['GoodUpdatePedido']= true;
            return true;
        }
    } catch(PDOException $e) {
        $_SESSION['OperationFailed'] = true;
        return false;
    };
    }

    /**
     * Función que comprueba si el código está libre y devuelve bool con el resultado.
     * @param string idPedido a comprobar 3 letras 5 numeros
     * @return bool devuelve false si el código ya está en uso, devuelve true si el código está libre (no hay resultados que ya estén usando ese código)
     */
    public static function idPedidoLibre($idPedido){
        try {
            $conPDO = contectarBbddPDO();
            $idPedidoCheck = "SELECT * FROM `pedidos` WHERE idPedido = :idPedido";
            $statement = $conPDO->prepare($idPedidoCheck);
            $statement->bindParam(':idPedido', $idPedido);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,"Pedido");
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
        ?>
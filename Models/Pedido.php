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


    /**
     * @return bool|Pedido devuelve false si falla, devuelve el Pedido si lo encuentra consultando el código
     */
    public static function GetPedidoByidPedido($idPedido){
        try{
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `pedidos` WHERE idPedido=:idPedido;";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':idPedido', $idPedido);
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
     * @return bool|array devuelve false si falla, devuelve el Pedido o array de Pedidos si  encuentra 1 o más Pedidos que coincida el texto buscado (en fecha)
     */
    public static function GetPedidosByBusquedafecha($fecha){
        try{
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM pedidos WHERE fecha >= :fechaInicial AND fecha <= :fechaFinal;";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':fechaInicial', $fechaInicial);
            $statement->bindParam(':fechaFinal', $fechaFinal);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            $arrayPedidos = $statement->fetchAll();
            if(empty($arrayPedidos)){
                $_SESSION['fechaNotFound'] = true;
                return false;
            }else{
                return $arrayPedidos;
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
    public static Function getAllPedidos(){
        try {
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `pedidos`;";
            $statement=$con->prepare($sqlQuery);
            $statement->execute();
            $arrayPedidos=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            if($arrayPedidos == null){
                $_SESSION['NullReturned']= true;
            }
            return $arrayPedidos;
        } catch(PDOException $e) {
            $_SESSION['ErrorGetPedidos']= true;
        }
    }

    public static function getASCSortedPedidosByAtributo($nombreAtributo) {
        try {
            $con = contectarBbddPDO();
            $nombreAtributoLimpio = filter_var($nombreAtributo, FILTER_SANITIZE_STRING);//quitamos cosas que nos intente inyectarSQL
            $sql = "SELECT * FROM pedidos ORDER BY {$nombreAtributoLimpio} ASC";
            $statement = $con->prepare($sql);
            $statement->execute();
            $arrayPedidos = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            return $arrayPedidos;
        } catch (PDOException $e) {
            $_SESSION['ErrorGetPedidos'] = true;
        }
    }

    public static function getDESCSortedPedidosByAtributo($nombreAtributo) {
        try {
            $con = contectarBbddPDO();
            $nombreAtributoLimpio = filter_var($nombreAtributo, FILTER_SANITIZE_STRING);//quitamos cosas que nos intente inyectarSQL
            $sql = "SELECT * FROM pedidos ORDER BY {$nombreAtributoLimpio} DESC";
            $statement = $con->prepare($sql);
            $statement->execute();
            $arrayPedidos = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            return $arrayPedidos;
        } catch (PDOException $e) {
            $_SESSION['ErrorGetPedidos'] = true;
        }
    }

    /**
     * @return bool returns true si la inserción es exitosa, si no, false
     */
    public static function AltaPedido($idPedido, $fecha, $total, $estado, $codUsuario, $activo){
        $_SESSION["nuevoPedido"]=false;
        try{
            $con = contectarBbddPDO();
            $sqlQuery="INSERT INTO `pedidos` (`idPedido`, `fecha`, `total`, `estado`, `codUsuario`, `activo`)
                                        VALUES (:idPedido, :fecha, :total, :estado, :codUsuario, :activo);";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':idPedido', $idPedido);
            $statement->bindParam(':fecha', $fecha);
            $statement->bindParam(':total', $total);
            $statement->bindParam(':estado', $estado);
            $statement->bindParam(':codUsuario', $codUsuario);
            $statement->bindParam(':activo', $activo);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            $resultado = $statement->fetch();

            if ($resultado !== false && $resultado->rowCount() == 0) {
                $_SESSION['BadInsertPedido']= true;
                return false;
            } else {
                $_SESSION['GoodInsertPedido']= true;
                return true;
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
     * @return bool true si hay éxito, false si no es el caso.
     */
    public function updatePedido($fecha, $idPedido, $idPedidoOriginal,  $total, $estado, $codUsuario, $activo){
    //una vez aquí dentro hay que "reiniciar" el valor de "editando"
    $_SESSION["editandoPedido"]="false";
    $conPDO = contectarBbddPDO();


    $mantienenidPedido = ($idPedido == $idPedidoOriginal || $idPedido == null);

    try{
        $conPDO = contectarBbddPDO();
        if( $mantienenidPedido){
            $sqlQuery = " UPDATE `pedidos`
                    SET `fecha` = :fecha, `idPedido` = :idPedidoOriginal, `total` = :total, `estado` = :estado, `codUsuario` = :codUsuario, `activo` = :activo
                    WHERE `idPedido` = :idPedidoOriginal "
            ;
        } else{
            $sqlQuery = " UPDATE `pedidos`
                    SET `fecha` = :fecha, `idPedido` = :idPedido, `total` = :total, `estado` = :estado, `codUsuario` = :codUsuario, `activo` = :activo
                    WHERE `idPedido` = :idPedidoOriginal "
            ;
        }
        echo "<br>UpdatePedido says: idPedido nuevo: $idPedido"." y idPedido original: ".$idPedidoOriginal."<br>";
        echo "<br>UpdatePedido says:".$sqlQuery;
        $statement= $conPDO->prepare($sqlQuery);

        if($mantienenidPedido){
            $statement->bindParam(':idPedidoOriginal', $idPedidoOriginal);
        }else{
            $statement->bindParam(':idPedido', $idPedido);
            $statement->bindParam(':idPedidoOriginal', $idPedidoOriginal);
        }
        $statement->bindParam(':fecha', $fecha);
        $statement->bindParam(':total', $total);
        $statement->bindParam(':estado', $estado);
        $statement->bindParam(':codUsuario', $codUsuario);
        $statement->bindParam(':activo', $activo);
        $operacionRealizada = $statement->execute();

        if($operacionRealizada == false && $statement->rowCount() <= 0){
            //si SQL no se ejecuta, hay que deshacer lo hecho (solo queremos borrar si estamos subiendo imagen nueva, la que ya tenía no hay que borrarla)
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
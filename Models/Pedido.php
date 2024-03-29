<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");

//NO PROTEGER O NO PODRÁN CREARSE PEDIDOS SIN REGISTRARSE

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
        $desactivaContenidoPedidoConfirmado = borradoLogicoContenidoPedido($idPedido);

        if($desactivaContenidoPedidoConfirmado == false){
            $_SESSION['FalloBorrandoContenidoPedido'] = true;
        }

        $conPDO=contectarBbddPDO();
        $query=("UPDATE pedidos SET activo=0 WHERE idPedido=:idPedido");
        $statement= $conPDO->prepare($query);
        $statement->bindParam(':idPedido', $idPedido);
        $desactivaPedidoConfirmado = $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Pedido');
        $desactivaPedidoConfirmado= $statement->fetch();

        if($desactivaPedidoConfirmado == false){
            $_SESSION['falloBorrandoElPropioPedido'] = true;
        } 

        if($desactivaPedidoConfirmado !== false && $desactivaContenidoPedidoConfirmado !== false){
            $_SESSION['ExitoBorrandoTodoPedido'] = true;
        } 
        
        return $desactivaPedidoConfirmado; //devolvemos el pedido, a partir de ahí pueden acceder al contenido si hace falta
    } catch(PDOException $e) {
        $_SESSION['BadOperation'] = true;
        return false;
    };
}


    /**
     * @return bool|array devuelve false si falla, devuelve el Pedido o array de pedidos si lo/s encuentra consultando el estado (no requiere coincidencia exacta)
     * con que esté el estado escrito dentro del estado saldrá como resultado
     */
    public static function getPedidosByEstado($estado, $dni=null){
        $estado = (string)$estado; 
        try{
            $con = contectarBbddPDO();
            if($dni !== null){
                $sqlQuery="SELECT * FROM  `pedidos` WHERE estado LIKE CONCAT('%', :estado, '%') AND codUsuario=:dni AND activo=1;";
            } else{
                $sqlQuery="SELECT * FROM  `pedidos` WHERE estado LIKE CONCAT('%', :estado, '%') ;";
            }
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':estado', $estado);
            if($dni !== null){
                $statement->bindParam(':dni', $dni);
            }
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            $Pedido = $statement->fetchAll();
            if(empty($Pedido)){
                $_SESSION['estadoNotFound'] = true;
                return false;
            }else{
                return $Pedido;
            }
        } catch(PDOException $e) {
            $_SESSION['ErrorGetPedidos']= true;
           // $_SESSION['ErrorGetPedidos1'] = $e->getMessage();
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
     *  devuelve false si no encuentra el pedido, devuelve el pedido si tiene exito
     */
    public static function getPedidoByIdPedido($idPedido, $dni=null) {
        try {
            $con = contectarBbddPDO();
            if($dni!==null) {
                $sql = "SELECT * FROM pedidos WHERE idPedido=:idPedido AND codUsuario=:dni";
            } else{
                $sql = "SELECT * FROM pedidos WHERE idPedido=:idPedido";
            }
            $statement = $con->prepare($sql);
            if($dni!==null) {
                $statement->bindParam(":dni", $dni);
            }
            $statement->bindParam(':idPedido', $idPedido);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            $pedido = $statement->fetch();
            if(empty($pedido) || $pedido==false || empty($pedido->idPedido) ){
                //si el resultado está vacio, es falso o es un objeto vacio (si idPedido está vacio el resto de props estarán vacias), devolvemos false
                $_SESSION['NoHayPedidos'] = true;
                return false;
            }else{
                return $pedido;
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
     * @return int|bool returns idPedido si inserción es exitosa, si no, false
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
            $idPedido = $con->lastInsertId();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Pedido");
            $resultado = $statement->fetch();

            if ($resultado !== false && $resultado->rowCount() == 0) {
                $_SESSION['BadInsertPedido']= true;
                return false;
            } else {
                $_SESSION['GoodInsertPedido']= true;
                return $idPedido;
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
        $estaPagado = strpos($estado, '6') ;
        $estaEnviado = strpos($estado, '7') ;
        $seHaRecibido = strpos($estado, '8')  ;
        $estaFinalizado= strpos($estado, '9') ;

        if( 
            $estaPagado !== false ||
            $estaEnviado !== false || 
            $seHaRecibido  !== false || 
            $estaFinalizado !== false 
        ) {
            ///si no son falsos, es que ha encontrado alguno de ellos, no dejamos cancelar (pagado, enviado, recibido, finalizado)
            $_SESSION['BadEstadoParaCancelar'] = true;
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
    public function updatePedido($idPedido, $fecha, $total, $estado, $codUsuario, $activo){
    //una vez aquí dentro hay que "reiniciar" el valor de "editando"
    $_SESSION["editandoPedido"]="false";

    $conPDO = contectarBbddPDO();

    try{
        $conPDO = contectarBbddPDO();
        $sqlQuery = " UPDATE `pedidos`
                SET `fecha` = :fecha, `total` = :total, `estado` = :estado, `codUsuario` = :codUsuario, `activo` = :activo
                WHERE `idPedido` = :idPedido "
        ;

        $statement= $conPDO->prepare($sqlQuery);

        $statement->bindParam(':idPedido', $idPedido);
        $statement->bindParam(':fecha', $fecha);
        $statement->bindParam(':total', $total);
        $statement->bindParam(':estado', $estado);
        $statement->bindParam(':codUsuario', $codUsuario);
        $statement->bindParam(':activo', $activo);
        $operacionRealizada = $statement->execute();

        if($operacionRealizada == false && $statement->rowCount() <= 0){
            $_SESSION['BadUpdatePedido']= true;
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

    
    /**
     * @param string comprueba que el string de fecha esté en formato Y-m-d
     * @return bool devuelve true si tiene formato Y-m-d y false si no tiene ese formato
     */
    public static function fechaValida($fecha){
       //transforma la fecha recbida como string a DateTime, devuelve false si el string no vale apra el formato especificado (lo usamos como comprobadora)
        $fechaValida = DateTime::createFromFormat('Y-m-d', $fecha);
        if($fechaValida !== false){
            return true;
        } else{
            $_SESSION['BadFecha']= true;
            return false;
        }
    }


}
        ?>
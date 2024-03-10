<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
 //si protejo esto no funciona bien coger articulos relacionados (se tiene quie poder acceder sin haber logueado)
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


 public function __construct($codigo = null, $nombre = null, $activo = null, $codCategoriaPadre = null) {
    $this->codigo = $codigo;
    $this->nombre = $nombre;
    $this->activo = $activo;
    $this->codCategoriaPadre = $codCategoriaPadre;
}

        /**
     * @return bool|array devuelve false si falla, devuelve el Categoria o array de Categorias si  encuentra 1 o más Categorias que coincida el texto buscado (en nombre)
     */
    public static function GetCategoriasByBusquedaNombre($nombre){
        try{
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `categorias` WHERE nombre LIKE CONCAT('%', :nombre, '%');";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':nombre', $nombre);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Categoria");
            $arrayCategoria = $statement->fetchAll();
            if(empty($arrayCategoria)){
                $_SESSION['NombreNotFound'] = true;
                return false;
            }else{
                return $arrayCategoria;
            }
        } catch(PDOException $e) {
            $_SESSION['ErrorGetCategorias']= true;
            return false;
        }
    }

    /**
     * @return array|bool devuelve false si no encuentra más de un resultado, si los encuentra devuelve un array con las subcategorias (solo las activadas)
     */
    public static function getSubCategorias($codigoCategoria){
        try{
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `categorias` WHERE codigo LIKE CONCAT('%', :categoria, '%') AND activo=1;";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':categoria', $codigoCategoria);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Categoria");
            $arraySubCategorias = $statement->fetchAll();
            if($statement->rowCount() > 1){
                //si hay más resultados buscando exactamente la categoria del item o categoria en cuestion es que sí que tiene subcategorias
                //p.e. 23 si es para 2pc parts>3SCREENS y encuentra 231(pantallas samsung) y 232(pantallas dell) (más de una fila) es que hay subcategorias 
                return $arraySubCategorias;
            }else{
                return false;
            }
        } catch(PDOException $e) {
            $_SESSION['ErrorGetSubCategorias']= true;
            return false;
        }
    }

    
    /**
     * @return bool|Categoria devuelve false si falla, devuelve el Categoria si lo encuentra consultando el código
     */
    public static function GetCategoriaByCodigo($codigo){
        try{
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `categorias` WHERE codigo=:codigo;";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':codigo', $codigo);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Categoria");
            $Categoria = $statement->fetch();
            if(empty($Categoria)){
                $_SESSION['CodigoNotFound'] = true;
                return false;
            }else{
                return $Categoria;
            }
        } catch(PDOException $e) {
            $_SESSION['ErrorGetCategorias']= true;
            return false;
        }
    }


    public static function ComprobarLongitud($string, $longitud) {
        if(strlen($string) > $longitud) {
            return false;
        }
        return true;
    }
    public static Function getAllCategorias(){
        try {
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `categorias`;";
            $statement=$con->prepare($sqlQuery);
            $statement->execute();
            $arrayCategorias=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Categoria");
            return $arrayCategorias;
        } catch(PDOException $e) {
            $_SESSION['ErrorGetCategorias']= true;
        }
    }

    public static function getASCSortedCategorias() {
        try{
            $con= contectarBbddPDO();
            $sql="SELECT * FROM categorias ORDER BY nombre ASC";
            $statement=$con->prepare($sql);
            $statement->execute();
            $arrayCategorias=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Categoria");
            return $arrayCategorias;
        }catch(PDOException $e) {
            $_SESSION['ErrorGetCategorias']= true;
        }
    }

    public static function getDESCSortedCategorias() {
        try {
            $con= contectarBbddPDO();
            $sql="SELECT * FROM categorias ORDER BY nombre DESC";
            $statement=$con->prepare($sql);
            $statement->execute();
            $arrayCategorias=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Categoria");
            return $arrayCategorias;
        }catch(PDOException $e) {
            $_SESSION['ErrorGetCategorias']= true;
        }
    }

    /**
     * @return bool returns true si la inserción es exitosa, si no, false
     */
    public static function AltaCategoria($nombre, $codigo, $codigoOriginal, $activo, $codCategoriaPadre){
        $_SESSION["nuevoCategoria"]=false;
        try{
            $con = contectarBbddPDO();
            $sqlQuery="INSERT INTO `categorias` (`codigo`, `nombre`, `activo`, `codCategoriaPadre`)
                                        VALUES (:codigo, :nombre, :activo, :codCategoriaPadre);";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':codigo', $codigo);
            $statement->bindParam(':nombre', $nombre);
            $statement->bindParam(':activo', $activo);
            $statement->bindParam(':codCategoriaPadre', $codCategoriaPadre);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Categoria");
            $resultado = $statement->fetch();

            if ($resultado !== false && $resultado->rowCount() == 0) {
                $_SESSION['BadInsertCategoria']= true;
                return false;
            } else {
                $_SESSION['GoodInsertCategoria']= true;
                return true;
            }
        } catch(PDOException $e) {
            $_SESSION['BadInsertCategoria']= true;
         // $_SESSION['error_message'] = $e->getMessage();
            //$error= $_SESSION['error_message'];
            return false;
        };
    }

    /**
     * @return array devuelve array de atributos  del artículo (propiedades privadas)
     */
    public static function getArrayAtributosCategoria() {
        $reflector = new ReflectionClass('Categoria');
        $atributos = $reflector->getProperties(ReflectionProperty::IS_PRIVATE);
        $arrayAtributosCategoria = array();
        foreach ($atributos as $propiedad) {
            if ($propiedad->isPrivate()) {
                $arrayAtributosCategoria[] = $propiedad->getName();
            }
        }
        return $arrayAtributosCategoria;
    }


    public function borradoLogico($codigo){
        try {
            $conPDO=contectarBbddPDO();
            $query=("UPDATE categorias SET activo=false WHERE codigo=:codigo");
            $statement= $conPDO->prepare($query);
            $statement->bindParam(':codigo', $codigo);
            $operacionConfirmada = $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Categoria');
            $operacionConfirmada= $statement->fetch();
            if($operacionConfirmada !== false){
                $_SESSION['ExitoBorrandoCategoria'] = true;
            } else {
                $_SESSION['ExitoBorrandoCategoria'] = false;
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
    public function updateCategoria($nombre, $codigo, $codigoOriginal, $activo, $codCategoriaPadre){
    //una vez aquí dentro hay que "reiniciar" el valor de "editando"
    $_SESSION["editandoCategoria"]="false";
    $conPDO = contectarBbddPDO();

    $mantienenCodigo = ($codigo == $codigoOriginal || $codigo == null);

    try{
        $conPDO = contectarBbddPDO();
        if( $mantienenCodigo){
            $sqlQuery = " UPDATE `categorias`
                    SET `nombre` = :nombre, `codigo` = :codigoOriginal, `activo` = :activo, `codCategoriaPadre` = :codCategoriaPadre
                    WHERE `codigo` = :codigoOriginal "
            ;
        } else{
            $sqlQuery = " UPDATE `categorias`
                    SET `nombre` = :nombre, `codigo` = :codigo,  `activo` = :activo,  `codCategoriaPadre` = :codCategoriaPadre
                    WHERE `codigo` = :codigoOriginal "
            ;
        }
        echo "<br>UpdateCategoria says: codigo nuevo: $codigo"." y codigo original: ".$codigoOriginal."<br>";
        echo "<br>UpdateCategoria says:".$sqlQuery;
        $statement= $conPDO->prepare($sqlQuery);
        $statement->bindParam(':nombre', $nombre);

        if($mantienenCodigo){
            $statement->bindParam(':codigoOriginal', $codigoOriginal);
        }else{
            $statement->bindParam(':codigo', $codigo);
            $statement->bindParam(':codigoOriginal', $codigoOriginal);
        }

        $statement->bindParam(':activo', $activo);
        $statement->bindParam(':codCategoriaPadre', $codCategoriaPadre);

        $operacionRealizada = $statement->execute();

        if($operacionRealizada == false && $statement->rowCount() <= 0){
            $_SESSION['GoodUpdateCategoria']= false;
            $_SESSION['BadUpdateCategoria']= true;
            return false;
        } else{
            $_SESSION['GoodUpdateCategoria']= true;
            return true;
        }
    } catch(PDOException $e) {
        $_SESSION['OperationFailed'] = true;
        return false;
    };
    }
    /**
     * Función que comprueba si el código del padre existe y devuelve bool con el resultado, true si lo encuentra.
     * @param string codigo a comprobar
     * @return bool devuelve true si el código existe, devuelve false si el código no está usado
     */
    public static function CodigoPadreExiste($codCategoriaPadre){
        try {
            $conPDO = contectarBbddPDO();
            $codigoCheck = "SELECT * FROM `categorias` WHERE codCategoriaPadre = :codCategoriaPadre";
            $statement = $conPDO->prepare($codigoCheck);
            $statement->bindParam(':codCategoriaPadre', $codCategoriaPadre);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,"Categoria");
            $resultado = $statement->fetch();
            if ( $resultado !== false) {
                return true;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            $_SESSION['BadOperation']= true;
            return true;
        };
    }
    /**
     * Función que comprueba si el código está libre y devuelve bool con el resultado.
     * @param string codigo a comprobar 3 letras 5 numeros
     * @return bool devuelve false si el código ya está en uso, devuelve true si el código está libre (no hay resultados que ya estén usando ese código)
     */
    public static function CodigoLibre($codigo){
        try {
            $conPDO = contectarBbddPDO();
            $codigoCheck = "SELECT * FROM `categorias` WHERE codigo = :codigo";
            $statement = $conPDO->prepare($codigoCheck);
            $statement->bindParam(':codigo', $codigo);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,"Categoria");
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
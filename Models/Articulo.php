<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//si protejo esto los clientes no pueden ver el catálogo
include_once("../config/conectarBD.php");
include_once("../Controllers/Directorio.php");

class Articulo {

    private $codigo;
    private $nombre;
    private $descripcion;
    private $categoria;
    private $precio;
    private $imagen;
    private $descuento;
    private $activo;

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

        public function getDescuento() { return $this->descuento; }
        public function setDescuento($descuento) { $this->descuento = $descuento; }

        public function getActivo() { return $this->activo; }
        public function setActivo($activo) { $this->activo = $activo; }

    public function __construct($codigo = null, $nombre = null, $descripcion = null, $categoria = null, $precio = null, $imagen = null, $descuento = null, $activo = null) {
            $this->codigo = $codigo;
            $this->nombre = $nombre;
            $this->descripcion = $descripcion;
            $this->categoria = $categoria;
            $this->precio = $precio;
            $this->imagen = $imagen;
            $this->descuento = $descuento;
            $this->activo = $activo;
    }

    /**
     * @return bool|Articulo devuelve false si falla, devuelve el articulo si lo encuentra consultando el código
     */
    public static function GetArticuloByCodigo($codigo){
        try{
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `articulos` WHERE codigo=:codigo;";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':codigo', $codigo);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
            $articulo = $statement->fetch();
          //  $articulo=$statement->fetch(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
            if(empty($articulo)){
                $_SESSION['CodigoNotFound'] = true;
                return false;
            }else{
                return $articulo;
            }
        } catch(PDOException $e) {
            $_SESSION['ErrorGetArticulos']= true;
            return false;
        }
    }

    /**
     * @return array|bool Devuelve un array de artículos (busca por lógica en como enumeramos y también buscando articulos en la categoria padre), si no encuentra nada returns false
     */
    public static function GerArticulosRelacionadosByCodigo($codigo){
        $arrayArticulos = array();
        $articulo = getArticuloByCodigo($codigo);
        $categoria = $articulo->getCategoria();
        
        include_once("../Models/Categoria.php");
        $categoriaObjeto = Categoria::getCategoriaByCodigo($categoria);
        $categoriaPadre = $categoriaObjeto->getCodCategoriaPadre();  

        $longitudCategoria= strlen((string)$categoria);
        $categoriaSuperior= substr($categoria,0,$longitudCategoria-1);

        try{                
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `articulos` WHERE categoria LIKE CONCAT('%', :categoriaSuperior, '%');";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':categoriaSuperior', $categoriaSuperior);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
            $arrayArticulos1 = $statement->fetchAll();
            $arrayArticulos1= array();

            if(empty($arrayArticulos1)){
                $noHayRelacionadosEnCategoriaSuperior= true;    
            }

            $sqlQuery2="SELECT * FROM  `articulos` WHERE categoria LIKE CONCAT('%', :categoriaPadre, '%');";
            $statement2=$con->prepare($sqlQuery2);
            $statement2->bindParam(':categoriaPadre', $categoriaPadre);
            $statement2->execute();
            $statement2->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
            $arrayArticulos2 = $statement2->fetchAll();
            if(empty($arrayArticulos2)){
                $noHayRelacionadosEnCodCategoriaPadre= true;    
            }

            if($noHayRelacionadosEnCategoriaSuperior && $noHayRelacionadosEnCodCategoriaPadre){
                $_SESSION['RelacionadosNotFound'] = true;
                return false;
            } else{
                $arrayCodigosArticulos = array_merge($arrayCodigos, $arrayCodigos2);
                //hay mezclados códigos y clientes 

                foreach ($arrayCategorias as $index => $categoriaDelArray) {
                    $categoriaDelArray->getCodigo
                    $articuloDelArray=getArticuloByCodigo($categoriaDelArray);
                    $arrayArticulos[] = $articuloDelArray;
                }
                return $arrayArticulos;
            
        } catch(PDOException $e) {
            $_SESSION['ErrorGetArticulos']= true;
            return false;
        }
    }
    


    /**
     * @return bool|array devuelve false si falla, devuelve el articulo o array de articulos si  encuentra 1 o más articulos que coincida el texto buscado (en nombre)
     */
    public static function GetArticulosByBusquedaNombre($nombre){
        try{
            $con = contectarBbddPDO();
            $sqlQuery="SELECT * FROM  `articulos` WHERE nombre LIKE CONCAT('%', :nombre, '%');";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':nombre', $nombre);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
            $arrayArticulo = $statement->fetchAll();
            if(empty($arrayArticulo)){
                $_SESSION['NombreNotFound'] = true;
                return false;
            }else{
                return $arrayArticulo;
            }
        } catch(PDOException $e) {
            $_SESSION['ErrorGetArticulos']= true;
            return false;
        }
    }

    public static function ComprobarLongitud($string, $longitud) {
        if(strlen($string) > $longitud) {
            return false;
        }
        return true;
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

    /**
     * @return bool returns true si la inserción es exitosa, si no, false
     */
    public static function AltaArticulo($codigo, $nombre,$descripcion, $categoria, $precio, $nombreArchivoDestino, $descuento, $activo){
        $_SESSION["nuevoArticulo"]=false;
        try{
            $con = contectarBbddPDO();
            $sqlQuery="INSERT INTO `articulos` (`codigo`, `nombre`, `descripcion`, `categoria`, `precio`, `imagen`, `descuento`, `activo`)
                                        VALUES (:codigo, :nombre, :descripcion, :categoria, :precio, :imagen, :descuento, :activo);";
            $statement=$con->prepare($sqlQuery);
            $statement->bindParam(':codigo', $codigo);
            $statement->bindParam(':nombre', $nombre);
            $statement->bindParam(':descripcion', $descripcion);
            $statement->bindParam(':categoria', $categoria);
            $statement->bindParam(':precio', $precio);
            $statement->bindParam(':imagen', $nombreArchivoDestino);
            $statement->bindParam(':descuento', $descuento);
            $statement->bindParam(':activo', $activo);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
            $resultado = $statement->fetch();

            if ($resultado !== false && $resultado->rowCount() == 0) {
                $_SESSION['BadInsertArticulo']= true;
                if (file_exists($nombreArchivoDestino)) {//si no se realiza la operación borramos la imagen (aquí ya se había movido)
                    unlink($nombreArchivoDestino);
                }
                return false;
            } else {
                $_SESSION['GoodInsertArticulo']= true;
                return true;
            }
        } catch(PDOException $e) {
            $_SESSION['BadInsertArticulo']= true;
            if (file_exists($nombreArchivoDestino)) {//si no se realiza la operación borramos la imagen (aquí ya se había movido)
                unlink($nombreArchivoDestino);
            }
           // $_SESSION['error_message'] = $e->getMessage();
            //$error= $_SESSION['error_message'];
            return false;
        };
    }

    /**
     * @return array devuelve array de atributos  del artículo (propiedades privadas)
     */
    public static function getArrayAtributosArticulo() {
        $reflector = new ReflectionClass('Articulo');
        $atributos = $reflector->getProperties(ReflectionProperty::IS_PRIVATE);
        $arrayAtributosArticulo = array();
        foreach ($atributos as $propiedad) {
            if ($propiedad->isPrivate()) {
                $arrayAtributosArticulo[] = $propiedad->getName();
            }
        }
        return $arrayAtributosArticulo;
    }


    public function borradoLogico($codigo){
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
     * @return bool true si hay éxito, false si no es el caso.
     */
    public function updateArticulo($nombre, $codigo, $codigoOriginal, $descripcion, $categoria, $precio, $nombreArchivoDestino, $imagenReciclada, $descuento, $activo){
    //una vez aquí dentro hay que "reiniciar" el valor de "editando"
    $_SESSION["editandoArticulo"]="false";
    $conPDO = contectarBbddPDO();


    $mantienenCodigo = ($codigo == $codigoOriginal || $codigo == null);

    try{
        $conPDO = contectarBbddPDO();
        if( $mantienenCodigo){
            $sqlQuery = " UPDATE `articulos`
                    SET `nombre` = :nombre, `codigo` = :codigoOriginal, `descripcion` = :descripcion, `categoria` = :categoria, `precio` = :precio,
                    `imagen` = :imagen, `descuento` = :descuento , `activo` = :activo
                    WHERE `codigo` = :codigoOriginal "
            ;
        } else{
            $sqlQuery = " UPDATE `articulos`
                    SET `nombre` = :nombre, `codigo` = :codigo, `descripcion` = :descripcion, `categoria` = :categoria, `precio` = :precio,
                    `imagen` = :imagen, `descuento` = :descuento, `activo` = :activo
                    WHERE `codigo` = :codigoOriginal "
            ;
        }
        echo "<br>UpdateArticulo says: codigo nuevo: $codigo"." y codigo original: ".$codigoOriginal."<br>";
        echo "<br>UpdateArticulo says:".$sqlQuery;
        $statement= $conPDO->prepare($sqlQuery);
        $statement->bindParam(':nombre', $nombre);

        if($mantienenCodigo){
            $statement->bindParam(':codigoOriginal', $codigoOriginal);
        }else{
            $statement->bindParam(':codigo', $codigo);
            $statement->bindParam(':codigoOriginal', $codigoOriginal);
        }

        $statement->bindParam(':descripcion', $descripcion);
        $statement->bindParam(':categoria', $categoria);
        $statement->bindParam(':precio', $precio);
        $statement->bindParam(':descuento', $descuento);
        $statement->bindParam(':activo', $activo);

        $estamosReciclandoImagen = ( isset($_SESSION["imagenReciclada"]) && !empty($_SESSION["imagenReciclada"]) );
        if( $estamosReciclandoImagen ){
            $statement->bindParam(':imagen', $imagenReciclada);
        } else{
            $statement->bindParam(':imagen', $imagen);
        }

        $operacionRealizada = $statement->execute();

        if($operacionRealizada == false && $statement->rowCount() <= 0 && !$estamosReciclandoImagen){
            //si SQL no se ejecuta, hay que deshacer lo hecho (solo queremos borrar si estamos subiendo imagen nueva, la que ya tenía no hay que borrarla)
            if (file_exists($imagen)) {//si llegó imagen cmo aquí ya se había movido la borramos
                $_SESSION['BadUpdateArticulo']= true;
                echo "<br>nos cargamos la imagen.";
                unlink($imagen);
            }
            return false;
        } else{
            $_SESSION['GoodUpdateArticulo']= true;
            return true;
        }
    } catch(PDOException $e) {
        $_SESSION['OperationFailed'] = true;
        return false;
    };
    }


        /**
         * comprueba que el código generado sea adecuado: 8 caracteres, 3 de los primeros letras y los 5 últimos numeros.
         * @param string string con codigo identificador del articulo
         * @return bool true si cumple criterio de 3 letras (da igual minus, esta función lo pasa primero a mayus) y 5 números. si no se cumple este criterio se devuelve false.
         *
         *  */
        public static function EsFormatoCodigoCorrecto($codigo){
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
        public static function TransformarCodigo($codigo){
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
    public static function CodigoLibre($codigo){
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

    /**
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

    /** Función que DEBE ser llamada tras un formulario (p.e. un POST), ya que consulta >>>>>>>>>>>>$_FILES['imagen']<<<<<<<<<<<<. Comprueba si la imagen subida cumple los criterios especificados.
     * @return bool Devulve true si archivo cumple: 1 ser jpg, png, jpeg o gif ; 2 pesar menos de 300Kb ; 3 dimensiones máximas 200 x 200 px ; 4 no existe ya una imagen con ese nombre. Si CUALQUIER de las anteriores condiciones no se cumple devuelve false.
     */
    public static function ValidaImagen(){
        if (isset($_FILES['imagen'])) {
            if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

            $nombreArchivo = $_FILES['imagen']['name'];//este es el nombre con el que se sube el archivo (como lo nombra el usuario)
            //@ delante impide mensajes de error (ya lo estamos controlando con mensajes en el sistema)
            $arrayInfoImagen = @getimagesize($_FILES['imagen']['tmp_name']);//con esto no nos colarán cosas que no sean imágenes. si no tiene dimensiones es que no es imagen.
            if($arrayInfoImagen == false) {
                $_SESSION['FileBadFormat']= true;
            }

            $formatoImagen = preg_match("/\.(jpg|gif|png|jpeg)$/", $nombreArchivo);
            if ($formatoImagen == false) {
                $_SESSION['FileBadFormat']= true;
            }

            if($arrayInfoImagen[0] > 200 || $arrayInfoImagen[1] > 200) { //en el indice 0 tenemos el ancho y en el indice 1 tenemos el alto
                echo"<br>ValidaImagenes says: el ancho es $arrayInfoImagen[0] y el alto es $arrayInfoImagen[1]";
                $_SESSION['ImagenGrande'] = true;
            }

            include_once("../Controllers/Directorio.php");
            $directorio=PrepararDirectorio();
            $directorioDestino = $directorio ."/". $nombreArchivo;

            if (file_exists($directorioDestino)) {
                $_SESSION['FileAlreadyExists']= true;
            }
            $tamaño = $_FILES['imagen']['size'];
            if($_FILES['imagen']['size'] > 300 * 1024) { //el 1024 es para pasar los Bits a Kilobits
                echo"<br>ValidaImagenes says: el tamaño del archivo es $tamaño";
                $_SESSION['ImagenPesada'] = true;
            }

            if(
                isset($_SESSION['ImagenPesada']) && $_SESSION['ImagenPesada'] == true ||
                isset( $_SESSION['FileAlreadyExists']) && $_SESSION['FileAlreadyExists']== true ||
                isset( $_SESSION['ImagenGrande']) && $_SESSION['ImagenGrande']== true ||
                isset( $_SESSION['FileBadFormat']) && $_SESSION['FileBadFormat']== true
            ){
                return false;
            } else{
                return true;
            }

        } else{
            return false;
        }
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

        $float1 ="55.2";
        $float2 ="55,2";
        $float3 ="a55,2";
        $float = Articulo::ValorFloat($float1);
        print"$float";
        $float = Articulo::ValorFloat($float2);
        print"$float";
        $float = Articulo::ValorFloat($float3);
        print"$float";
        */
        ?>
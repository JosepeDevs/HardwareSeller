<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}

//con conexión inical PDO a la BD no puedo proteger esto sin cargarme el acceso.
//NO PROTEGER, HACE FALTA DESPROTEGIDO PARA QUE PUEDA USARLO SIN HACER LOG IN


$raiz= dirname(__DIR__);
$ruta = $raiz.'/config/conectarBD.php';
include_once("$ruta");

class Cliente {

    private $dni;
    private $nombre;
    private $direccion;
    private $localidad;
    private $provincia;
    private $telefono;
    private $email;
    private $psswrd;
    private $rol;
    private $activo;

    ///tablaclientescontroller requiere que el constructor esté en el mismo orden en el que se declaran los atributos
    public function __construct($dni = null, $nombre = null, $direccion = null, $localidad = null, $provincia = null, $telefono = null, $email = null, $psswrd = null, $rol = null, $activo =1) {//le ponemos que activo, de forma default sea 1 (igual que en la BBDD)
        $this->dni = $dni;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->localidad = $localidad;
        $this->provincia = $provincia;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->psswrd = $psswrd;
        $this->rol = $rol;
        $this->activo = $activo;
    }

    /**
     * @return array Array con los NOMBRES de los atributos de la clase, en este caseo CLIENTE
     */

    public static function getArrayAtributosCliente() {
        $reflector = new ReflectionClass('Cliente');
        $atributos = $reflector->getProperties(ReflectionProperty::IS_PRIVATE);
        $arrayAtributosCliente = array();
        foreach ($atributos as $propiedad) {
            if ($propiedad->isPrivate()) {
                $arrayAtributosCliente[] = $propiedad->getName();
            }
        }
        return $arrayAtributosCliente;
    }

    /**
     * @return  array|bool devuelve array con todos los clientes
     *
     */
    public static function getAllClients() {
        try{
            $con= contectarBbddPDO();
            $sql="SELECT * FROM clientes";
            $statement=$con->prepare($sql);
            $statement->execute();
            $arrayClientes=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
            return $arrayClientes;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return Cliente|bool devuelve cliente si lo encuentra por dni, si no, devuelve false.
     */
    public static function getClienteByDni($dni) {
        $con= contectarBbddPDO();
        $sql="SELECT * FROM clientes WHERE dni=:dni";
        $statement=$con->prepare($sql);
        $statement->bindParam(':dni', $dni);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
        $cliente = $statement->fetch();
        if(empty($cliente)){
            $_SESSION['DniNotFound'] = true;
            return false;
        }else{
            return $cliente;
        }
    }
    /**
     * @return Cliente|bool devuelve cliente si lo encuentra por email, si no, devuelve false.
     */
    public static function getClienteByEmail($email) {
        $con= contectarBbddPDO();
        $sql="SELECT * FROM clientes WHERE email=:email";
        $statement=$con->prepare($sql);
        $statement->bindParam(':email', $email);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
        $cliente = $statement->fetch();
        if(empty($cliente)){
            $_SESSION['DniNotFound'] = true;
            return false;
        }else{
            return $cliente;
        }
    }

    public static function getASCSortedClients() {
        try{
            $con= contectarBbddPDO();
            $sql="SELECT * FROM clientes ORDER BY nombre ASC";
            $statement=$con->prepare($sql);
            $statement->execute();
            $arrayClientes=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
            return $arrayClientes;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getDESCSortedClients() {
        try {
            $con= contectarBbddPDO();
            $sql="SELECT * FROM clientes ORDER BY nombre DESC";
            $statement=$con->prepare($sql);
            $statement->execute();
            $arrayClientes=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
            return $arrayClientes;
        } catch (Exception $e) {
            return false;
        }
    }
/**
 *      * Ya sube esto a session si algo sale mal
 * @return bool true si tiene éxito el update, false si falla.
 */
    public static function UpdateCliente($dni, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol, $activo, $noPsswrd){
    //hay que "reiniciar" el valor de "editando"
        $_SESSION["editandoCliente"]="false";
        if( $noPsswrd == true){
            //no se posteo contraseña y no se escribió nada en la psswrd y debemos ejecutar un SQL diferente al update de todos los datos
            try {
                $conPDO = contectarBbddPDO();
                $sqlQuery = " UPDATE `clientes`
                        SET `nombre` = :nombre, `telefono` = :telefono, `direccion` = :direccion, `provincia` = :provincia, `localidad` = :localidad, `email` = :email,
                        `dni` = :dni, `rol` = :rol,`activo` = :activo
                        WHERE `dni` = :dni "
                ;
                $statement= $conPDO->prepare($sqlQuery);
                $statement->bindParam(':nombre', $nombre);
                $statement->bindParam(':telefono', $telefono);
                $statement->bindParam(':direccion', $direccion);
                $statement->bindParam(':provincia', $provincia);
                $statement->bindParam(':localidad', $localidad);
                $statement->bindParam(':email', $email);
                $statement->bindParam(':psswrd', $psswrd);

                $statement->bindParam(':dni', $dni);
                $statement->bindParam(':rol', $rol);
                $statement->bindParam(':activo', $activo);

                $operacionRealizada = $statement->execute();

                if($operacionRealizada == false && $statement->rowCount() <=0) {
                    $_SESSION['BadUpdateCliente']= true;
                    return false;
                }

                return $operacionRealizada;
            } catch(PDOException $e) {
                $_SESSION['BadOperation'] = true;
                return false;
            };

        } else{
            //sí que se posteó una contraseña por lo que debemos actualizarla

            try {
                $conPDO = contectarBbddPDO();
                $sqlQuery = " UPDATE `clientes`
                        SET `nombre` = :nombre, `telefono` = :telefono, `direccion` = :direccion, `provincia` = :provincia, `localidad` = :localidad, `email` = :email,
                        `dni` = :dni, `psswrd` = :psswrd, `rol` = :rol, `activo` = :activo
                        WHERE `dni` = :dni "
                ;

                $statement= $conPDO->prepare($sqlQuery);
                $statement->bindParam(':nombre', $nombre);
                $statement->bindParam(':telefono', $telefono);
                $statement->bindParam(':direccion', $direccion);
                $statement->bindParam(':provincia', $provincia);
                $statement->bindParam(':localidad', $localidad);
                $statement->bindParam(':email', $email);
                $statement->bindParam(':psswrd', $psswrd);
                $statement->bindParam(':activo', $activo);

                $statement->bindParam(':dni', $dni);
                $statement->bindParam(':rol', $rol);

                $operacionRealizada = $statement->execute();

                if($operacionRealizada == false && $statement->rowCount() <=0) {
                    $_SESSION['BadUpdateCliente']= true;
                    return false;
                }

                return $operacionRealizada;

            } catch(PDOException $e) {
                $_SESSION['BadOperation'] = true;
                return false;
            };
        }
    }

    /**
     * Ya sube esto a session si algo sale mal
     * @return bool true si tiene éxito el update, false si falla.
     */
    public static function InsertCliente($dni, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $psswrd, $rol, $activo){//activo no hace falta default = 1 (TRUE)
        if($activo == null){$activo=1;};// al dar de alta de forma predeterminada activo=true
        if($rol == null){$rol="user";};// al dar de alta de forma predeterminada activo=true
        $_SESSION["nuevoCliente"]=false;
        $con = contectarBbddPDO();
        //rescatamos de session los datos subidos por ValidarDatos
        //nos llega la psswrd ya hasheada
        try{
                $sqlQuery="INSERT INTO `clientes` (`dni`, `nombre`, `direccion`, `localidad`, `provincia`, `telefono`, `email`, `psswrd`, `activo`)
                                        VALUES (:dni, :nombre, :direccion, :localidad, :provincia, :telefono, :email, :psswrd, :activo);";
                $statement=$con->prepare($sqlQuery);
                $statement->bindParam(':dni', $dni);
                $statement->bindParam(':nombre', $nombre);
                $statement->bindParam(':direccion', $direccion);
                $statement->bindParam(':localidad', $localidad);
                $statement->bindParam(':provincia', $provincia);
                $statement->bindParam(':telefono', $telefono);
                $statement->bindParam(':email', $email);
                $statement->bindParam(':psswrd', $psswrd);
                $statement->bindParam(':activo', $activo);
                $OperacionExitosa = $statement->execute();
              //  $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
                //$resultado = $statement->fetch();

                if ($OperacionExitosa) {
                    $_SESSION['BadInsertCliente']= false;
                    $_SESSION['GoodInsertCliente']= true;
                    return true;
                } else {
                    $_SESSION['BadInsertCliente']= true;
                    return false;
                }
        } catch(PDOException $e) {
            $_SESSION['BadInsertCliente']= true;
            return false;
        };
    }

    /**
     * @return Cliente|bool devuelve cliente si encuentra el correo en la BBDD, en cualquier otro caso devuelve false
     */
    public static function GetClientByEmail($email){
        try{
            $conPDO=contectarBbddPDO();
            $query=("select * from clientes WHERE email=:email");
            $statement= $conPDO->prepare($query);
            $statement->bindParam(':email', $email);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Cliente');
            $cliente= $statement->fetch();
            if($cliente !== false){
                return $cliente;
            } else {
                return false;
            }
        }catch(PDOException $e) {
            $_SESSION['OperationFailed'] = true;
            return false;
        }
    }
    public static function GetDniByEmail($email){
        try{
            $conPDO=contectarBbddPDO();
            $query=("select * from clientes WHERE email=:email");
            $statement= $conPDO->prepare($query);
            $statement->bindParam(':email', $email);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Cliente');
            $cliente= $statement->fetch();
            if($cliente !== false){
                $dni= $cliente->getDni();
                return $dni;
            } else {
                return false;
            }
        }catch(PDOException $e) {
            $_SESSION['OperationFailed'] = true;
            return false;
        }
    }

    /**
     * @return bool returns true si hay exito (y sube a session ExitoBorrandoCliente=true), si falla returns false y sube ExitoBorrandoCliente=false a session)
     */
    public function borradoLogicoCliente($dni){
        try {
            $conPDO=contectarBbddPDO();
            $query=("UPDATE clientes SET activo=0 WHERE dni=:dni");
            $statement= $conPDO->prepare($query);
            $statement->bindParam(':dni', $dni);
            $operacionConfirmada = $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Cliente');
            $operacionConfirmada= $statement->fetch();
            if($operacionConfirmada){
                $_SESSION['ExitoBorrandoCliente'] = false;
            } else {
                $_SESSION['ExitoBorrandoCliente'] = true;
            }
            return $operacionConfirmada;
        } catch(PDOException $e) {
            $_SESSION['BadOperation'] = true;
            return false;
        };
    }

    /**
     * @return bool true si existe, false si no se encontró el cliente por dni y email
     */
public static function checkClientByEmailAndDni($email, $dni){
    try{
        $conPDO = contectarBbddPDO();
        $query = $conPDO->prepare("SELECT * FROM clientes WHERE email = :email AND dni = :dni");
        $query->bindParam(':email', $email);
        $query->bindParam(':dni', $dni);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Cliente');
        $operacionExitosa = $query->fetch();
        if($operacionExitosa == false){
            return false;
        } else {
            return true;
        }
    } catch(Exception $e){
        return false;
    }
}

/**
 * @return bool true si actualizó correctamente, false si falló algo
 */
    public static function updatePasswrdUsingDni($dni,$newpsswrd){
        try {
            $conPDO = contectarBbddPDO();
            $query = $conPDO->prepare("UPDATE clientes SET psswrd = :newpsswrd WHERE dni = :dni");
            $query->bindParam(':dni', $dni);
            $query->bindParam(':newpsswrd', $newpsswrd);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_CLASS, 'Cliente');

            if ( ( $query->rowCount() ) > 0) {
                return true;
            } else {
                $_SESSION['PsswrdSeQuedaIgual'] = true;
                return false;
            }
        } catch(Exception $e){
            return false;
        }
    }


    /**
  * @param $dni . (String) con el dni a comprobar (8 numeros y 1 letra, da igual minus o mayus)
  * @return bool devuelve true si la letra se corresponde a las 8 cifras introducidas. Devuelve false si la letra no es correcta, si no cumple regex de ser 8 numeros y 1 letra (permitimos que el input esté en minusculas), también devuelve false si dni es null.
  */
  public static function ValidaDni($dniNuevo){
    if($dniNuevo == null){
        return false;
    };
    if(preg_match("/^\d{8}\w{1}$/", $dniNuevo) == true ){
        $numerosString=substr($dniNuevo,0,8);
        $letra=strtoupper(substr($dniNuevo,8,9));
        $arrayLetras=array("T","R","W","A","G","M","Y","F","P","D","X","B","N","J","Z","S","Q","V","H","L","C","K","E");
        $numero=intval($numerosString);
        $resto=$numero%23;
        $letraCalculada=$arrayLetras[$resto];
        if($letra == $letraCalculada){
            return true;
        } else {
            return false;
        }
    }else{
        return false;
    }
}

/**
 * @param string telefono como texto cada 3 numeros puede haber una separación de un punto o un guión y sería aceptable. p.e. formatos aceptados formatos: 444-555-123, 246.555.888, 123456789
 *@return bool devuelve true si telefono cumple regex de 3 cifras guion?punto? 3 cifras guion?punto? 3 cifras. Devuelve false si no cumple regex
  *  */
  public static function ValidaTelefono($telefono){
    $formatoTelefonoCorrecto = preg_match("/\d{3}[-.]?\d{3}[-.]?\d{3}/", $telefono);
    if($formatoTelefonoCorrecto == true) {
        return true;
    } else {
        return false;
    }
}


 /**
 * @param $email el email a comprobar, permite lo que sea delante del arroba (puntos, barras bajas. guiones, comas, excepto @)
 * @return boolean true si cumple regex, si no, devuelve false
 *
 * */
public static function ValidarEmail($email) {
    $email = trim($email);
    $formatoEmailCorrecto = preg_match("/^[\w\-\.]+@([\w-]+\.)+[\w-]{2,}$/", $email);
    if ( $formatoEmailCorrecto == true ) {
        return true;// all good
    } else {
        return false;
    }
}


/**
 * @param string Recibe correo electrónico (esta función NO valida el formato), solo comprueba si ya existe en la BBDD.
 * @return bool true si ya existe en la BBDD, false si no está en uso.
 */
public static function EmailRepetido($email) {
    $conPDO = contectarBbddPDO();
    $emailCheck = "SELECT `email` FROM `clientes` WHERE `email` = :email";
    $statement = $conPDO->prepare($emailCheck);
    $statement->bindParam(':email', $email);
    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_CLASS,"Cliente");
    $yaHayclienteConEseCorreo = $statement->fetch();
    if ( $yaHayclienteConEseCorreo !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * @param string $string String a comprobar.
 * @param int $longitud longitud máxima.
 * @return bool devuelve true si NO supera la longitud especificada, devuelve false si es más largo de lo especificado.
 */
public static function ComprobarLongitud($string, $longitud) {
    if(strlen($string) > $longitud) {
        return false;
    }
    return true;
}

    
/////// /////// ////////CUIDADO////////////////
//Partes del código construyen dinámicamente los nombres de estos métodos (los getters), siempre deben ser getMayus nombrar con camelCase
    public function getNombre() {return $this->nombre;}
    public function getDireccion() {return $this->direccion;}
    public function getLocalidad() {return $this->localidad;}
    public function getProvincia() {return $this->provincia;}
    public function getTelefono() {return $this->telefono;}
    public function getEmail() {return $this->email;}
    public function getDni() {return $this->dni;}
    public function getRol() {return $this->rol;}
    public function getPsswrd() {return $this->psswrd;}
    public function getActivo() {return $this->activo;}

}

?>
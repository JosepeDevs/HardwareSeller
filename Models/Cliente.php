<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}

//con conexión inical PDO a la BD no puedo proteger esto sin cargarme el acceso.
//NO PROTEGER, HACE FALTA DESPROTEGIDO PARA QUE PUEDA USARLO SIN HACER LOG IN


include_once("../config/conectarBD.php");

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
                $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
                $resultado = $statement->fetch();

                if ($resultado !== false && $resultado->rowCount() == 0) {
                    $_SESSION['BadInsertCliente']= true;
                    return false;
                } else if($resultado !== false && $resultado->rowCount() !== 0){
                    return true;
                }
                return $OperacionExitosa;
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

/////// /////// ////////CUIDADO////////////////
//Partes del código construyen dinámicamente los nombres de estos métodos (los getters), siempre deben ser getMayus nombrar con camelCase o cambiar BuscarCliente
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
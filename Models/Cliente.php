<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}

//con conexión inical PDO a la BD no puedo proteger esto sin cargarme el acceso.

include_once("/../config/conectarBD.php");

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

    public function __construct($dni = null, $nombre = null, $direccion = null, $localidad = null, $provincia = null, $telefono = null, $email = null, $psswrd = null, $rol = null) {
        $this->dni = $dni;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->localidad = $localidad;
        $this->provincia = $provincia;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->psswrd = $psswrd;
        $this->rol = $rol;
    }


    /**
     * @return  array devuelve array con todos los clientes
     *
     */
    public static function getAllClients() {
        $con= contectarBbddPDO();
        $sql="SELECT * FROM clientes";
        $statement=$con->prepare($sql);
        $statement->execute();
        $arrayClientes=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
        return $arrayClientes;
    }

    public static function getClienteByDni($dni) {
        $con= contectarBbddPDO();
        $sql="SELECT * FROM clientes WHERE dni=:dni";
        $statement=$con->prepare($sql);
        $statement->bindParam(':dni', $dni);
        $statement->execute();
        $cliente=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
        if(empty($cliente)){
            return false;
        }else{
            return $cliente;
        }
    }

    public static function getASCSortedClients() {
        $con= contectarBbddPDO();
        $sql="SELECT * FROM clientes ORDER BY nombre ASC";
        $statement=$con->prepare($sql);
        $statement->execute();
        $arrayClientes=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
        return $arrayClientes;
    }
    public static function getDESCSortedClients() {
        $con= contectarBbddPDO();
        $sql="SELECT * FROM clientes ORDER BY nombre DESC";
        $statement=$con->prepare($sql);
        $statement->execute();
        $arrayClientes=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
        return $arrayClientes;
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
            $dni= $cliente->getDni();
            return $dni;
        }catch(PDOException $e) {
            $_SESSION['OperationFailed'] = true;
            header("/../Views/Index.php");
        }
    }

    public function borradoLogico($dni){
        include_once("/../config/conectarBD.php");
        try {
            $conPDO=contectarBbddPDO();
            $query=("UPDATE clientes SET activo=false WHERE dni=:dni");
            $statement= $conPDO->prepare($query);
            $statement->bindParam(':dni', $dni);
            $operacionConfirmada = $statement->execute();
            /*
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Articulo');
            $operacionConfirmada= $statement->fetch();
            */
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

}

?>
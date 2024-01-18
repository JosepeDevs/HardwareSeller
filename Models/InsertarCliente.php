<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

//NO PROTEGER, HACE FALTA DESPROTEGIDO PARA QUE PUEDA USARLO SIN HACER LOG IN

include_once("conectarBD.php");
include_once("Cliente.php");

$_SESSION["nuevoCliente"]=false;
$con = contectarBbddPDO();
//rescatamos de session los datos subidos por ValidarDatos
$direccion = ( isset($_SESSION["direccion"]) ? $_SESSION["direccion"] : null );
$localidad = ( isset($_SESSION["localidad"]) ? $_SESSION["localidad"] : null );
$provincia = ( isset($_SESSION["provincia"]) ? $_SESSION["provincia"] : null );
$telefono = ( isset($_SESSION["telefono"]) ? $_SESSION["telefono"] : null );
$nombre = ( isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : null );
$email = ( isset($_SESSION["email"]) ? $_SESSION["email"] : null );
$dni = ( isset($_SESSION["dni"]) ? $_SESSION["dni"] : null );
$psswrd = ( isset($_SESSION["psswrd"]) ? $_SESSION["psswrd"] : null );//esta psswrd ya está hasheada en validar datos

try{
        $sqlQuery="INSERT INTO `clientes` (`dni`, `nombre`, `direccion`, `localidad`, `provincia`, `telefono`, `email`, `psswrd`)
                                VALUES (:dni, :nombre, :direccion, :localidad, :provincia, :telefono, :email, :psswrd);";
        $statement=$con->prepare($sqlQuery);
        $statement->bindParam(':dni', $dni);
        $statement->bindParam(':nombre', $nombre);
        $statement->bindParam(':direccion', $direccion);
        $statement->bindParam(':localidad', $localidad);
        $statement->bindParam(':provincia', $provincia);
        $statement->bindParam(':telefono', $telefono);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':psswrd', $psswrd);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
        $resultado = $statement->fetch();

        if ($resultado !== false && $resultado->rowCount() == 0) {
            $_SESSION['BadInsertCliente']= true;
            header("Location: clientenuevo.php");
            exit;
        }

        if(isset($_SESSION['rol']) && $_SESSION['rol'] == "admin" && isset($_SESSION['auth']) && $_SESSION['auth'] == "OK") {
            $rolAdmin=true;
        } else {
            $rolAdmin=false;
        }

        if($rolAdmin == true){
            $_SESSION['GoodInsertCliente']= true;
            header("Location: TablaClientes.php");
            exit;
        }else{
            $_SESSION['GoodInsertCliente']= true;
            header("Location: clientenuevo.php");
            exit;
        }
} catch(PDOException $e) {
    $_SESSION['BadInsertCliente']= true;
    header("Location: clientenuevo.php");
    exit;
};
?>
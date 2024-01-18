<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "UpdateCliente dice: shit no está user en session";
    header("Location: index.php");
}
include_once("conectarBD.php");
include_once("Cliente.php");
include_once("CheckRol.php");

//una vez aquí dentro hay que "reiniciar" el valor de "editando"
$_SESSION["editandoCliente"]="false";
$con = contectarBbddPDO();
print_r($_SESSION);
//rescatamos de session los datos subidos por ValidarDatos (por algún motivo no están llegando ahora)
$nombre = ( isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : null );
$telefono = ( isset($_SESSION["telefono"]) ? $_SESSION["telefono"] : null );
$direccion = ( isset($_SESSION["direccion"]) ? $_SESSION["direccion"] : null );
$provincia = ( isset($_SESSION["provincia"]) ? $_SESSION["provincia"] : null );
$localidad = ( isset($_SESSION["localidad"]) ? $_SESSION["localidad"] : null );
$email = ( isset($_SESSION["email"]) ? $_SESSION["email"] : null );
$psswrd = ( isset($_SESSION["psswrd"]) ? $_SESSION["psswrd"] : null );
$dni = ( isset($_GET["dni"]) ? $_GET["dni"] : null );
$rol = ( isset($_SESSION["rolCliente"]) ? $_SESSION["rolCliente"] : "user" );
$noPsswrd = ( isset($_SESSION["NoPsswrd"]) ? $_SESSION["NoPsswrd"] : null );

if( $noPsswrd == true){
//no se posteo contraseña y no se escribió nada en la psswrd y debemos ejecutar un SQL diferente al update de todos los datos

    try {
        $conPDO = contectarBbddPDO();
        $sqlQuery = " UPDATE `clientes`
                SET `nombre` = :nombre, `telefono` = :telefono, `direccion` = :direccion, `provincia` = :provincia, `localidad` = :localidad, `email` = :email,
                `dni` = :dni, `rol` = :rol
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

        $operacionRealizada = $statement->execute();

        if($operacionRealizada == false && $statement->rowCount() <=0) {
            $_SESSION['BadUpdateCliente']= true;
            echo $dni;
        }
        $rolAdmin = AuthYRolAdmin();
        if($rolAdmin == true) {
            $_SESSION['GoodUpdateCliente']= true;
            header("Location: TablaClientes.php");
            exit;
        } else {
            $_SESSION['GoodUpdateCliente']= true;
            header("Location: editarcliente.php?dni=$dni");
            exit;
        }
    } catch(PDOException $e) {
        $_SESSION['BadOperation'] = true;
        header("Location: index.php");
    };

} else{
    //sí que se posteó una contraseña por lo que debemos actualizarla

    try {
        $conPDO = contectarBbddPDO();
        $sqlQuery = " UPDATE `clientes`
                  SET `nombre` = :nombre, `telefono` = :telefono, `direccion` = :direccion, `provincia` = :provincia, `localidad` = :localidad, `email` = :email,
                  `dni` = :dni, `psswrd` = :psswrd, `rol` = :rol
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

        $operacionRealizada = $statement->execute();

        if($operacionRealizada == false && $statement->rowCount() <=0) {
            $_SESSION['BadUpdateCliente']= true;
            echo $dni;
        }
        $rolAdmin = AuthYRolAdmin();
        if($rolAdmin == true) {
            $_SESSION['GoodUpdateCliente']= true;
           header("Location: TablaClientes.php");
            exit;
        } else {
            $_SESSION['GoodUpdateCliente']= true;
            header("Location: editarcliente.php?dni=$dni");
            exit;
        }
    } catch(PDOException $e) {
        $_SESSION['BadOperation'] = true;
        header("Location: index.php");
    };
}
?>
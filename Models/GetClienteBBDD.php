<?php
//como esto se usa en conexión no puedo protegerlo sin cargarme el acceso.

/**
 * @param string $usuario es un string con el email/correo del cliente que queremos recuperar.
 * @return Cliente|bool devuelve instancia de CLIENTE o FALSE si no lo encuentra o hay algún fallo en la conexión a la BBDD.
 *
 */
function GetClientePorEmail($usuario){
    if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
    include_once("conectarBD.php");
    include_once("Cliente.php");
    try {
        $con = contectarBbddPDO();
        $sql = "select * from clientes where email= :email";
        $statement = $con->prepare($sql);
        $statement->bindValue(':email', $usuario);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Cliente');
        $cliente=$statement->fetch();
        if($cliente == false) {
            return false;
        }
        return $cliente;
    } catch(PDOException $e) {
        $_SESSION['OperationFailed']=true;
        return false;
    };
}


?>
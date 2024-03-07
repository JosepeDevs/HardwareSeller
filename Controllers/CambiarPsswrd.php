<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("ProtegerDatos.php");
include_once("conectarBD.php");
include_once("Cliente.php");
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "Articulo dice: no está user en session";
    header("Location: index.php");
    exit;
}

try {
    if(isset($_POST['mail']) && isset($_POST['dni']) && !isset($_POST['newpsswrd'])) {
        $conPDO = contectarBbddPDO();
        $email = $_POST['mail'];
        $dni = $_POST['dni'];
        $_SESSION['dni'] = $dni;
        $query = $conPDO->prepare("SELECT * FROM clientes WHERE email = :email AND dni = :dni");
        $query->bindParam(':email', $email);
        $query->bindParam(':dni', $dni);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Cliente');

        if ($query->fetch()) {
            echo '<form action="CambiarPsswrd.php" method="POST">';
            echo '<label>Escriba su nueva contraseña</label><br><br><input type="text" name="newpsswrd"><br><br>';
            echo '<br><br><input type="submit" value="Submit"></form>';
        } else {
            $_SESSION['ClienteNoExiste'] =true;
            header("Location: RecuperarPsswrd.php");
        }
    } else {
        $conPDO = contectarBbddPDO();
        $newpsswrd = $_POST['newpsswrd'];
        $newpsswrd = password_hash($newpsswrd, PASSWORD_BCRYPT);
        $query = $conPDO->prepare("UPDATE clientes SET psswrd = :newpsswrd WHERE dni = :dni");
        $query->bindParam(':dni', $_SESSION['dni']);
        $query->bindParam(':newpsswrd', $newpsswrd);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS, 'Cliente');

        if ( ( $query->rowCount() ) > 0) {
            $_SESSION['PsswrdActualizada'] = true;
            echo "psswrd actualizada";
            header("Location: Index.php");
        } else {
            $_SESSION['PsswrdSeQuedaIgual'] = true;
            echo "psswrd NO actualizada";
            header("Location: RecuperarPsswrd.php");
        }
    }
} catch(Exception $e){
    echo '<h2>Hubo algun problema, es posible que con la conexión a la base de datos.</h2>';
}
?>
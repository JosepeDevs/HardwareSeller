<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

if( ! isset($_POST['newpsswrd']) && isset($_POST['mail']) && isset($_POST['dni']) ) {
    $email = $_POST['mail'];
    $dni = $_POST['dni'];
    $_SESSION['dni'] = $dni;
    $clienteExiste = checkClientByEmailAndDni($email, $dni);
    if( $clienteExiste) {
        echo '<form action="UpdatePsswrd.php" method="POST">';
        echo '<label>Escriba su nueva contraseña</label><br><br><input type="text" name="newpsswrd"><br><br>';
        echo '<br><br><input type="submit" value="Submit"></form>';
    } else {
        $_SESSION['ClienteNoExiste'] =true;
        header("Location: ../Views/index.php");
    }
} else if (isset($_POST['newpsswrd']) && !empty($_POST['newpsswrd'])){
    $newpsswrd = $_POST['newpsswrd'];
    $newpsswrd = password_hash($newpsswrd, PASSWORD_BCRYPT);
    $dni = $_SESSION['dni'];
    $operacionExitosa = updatePasswrdUsingDni($dni, $newpsswrd);
}

if ($operacionExitosa) {
    $_SESSION['PsswrdActualizada'] = true;
}
header("Location: ../Views/index.php");

function checkClientByEmailAndDni($email, $dni){
    include_once("../Models/Cliente.php");
    $operacionExitosa = Cliente::checkClientByEmailAndDni($email, $dni);
    if($operacionExitosa){
        return true;
    } else{
        return false;
    }
}


function updatePasswrdUsingDni($dni, $newpsswrd){
    include_once("../Models/Cliente.php");
    $operacionExitosa = Cliente::updatePasswrdUsingDni($dni, $newpsswrd);
    if($operacionExitosa){
        return true;
    } else{
        return false;
    }
}


?>
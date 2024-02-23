<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Views/header.php");

 $operacionExitosa=false;
if( ! isset($_POST['newpsswrd']) && isset($_POST['mail']) && isset($_POST['dni']) ) {
    $email = $_POST['mail'];
    $dni = $_POST['dni'];
    $_SESSION['dni'] = $dni;
    $clienteExiste = checkClientByEmailAndDni($email, $dni);
    if( $clienteExiste) {
        echo '<form action="UpdatePsswrd.php" method="POST">';
        echo '<h1>Recuperación de contraseña</h1>';
        echo '<h2><label>Escriba su nueva contraseña</label></h2><br><br><input type="text" name="newpsswrd"><br><br>';
        echo '<br><br><input type="submit" value="Submit"></form>';
    } else {
        $_SESSION['ClienteNoExiste'] =true;
       header("Location: ../index.php");
    }
} else if (isset($_POST['newpsswrd']) && !empty($_POST['newpsswrd'])){
    $newpsswrd = $_POST['newpsswrd'];
    $newpsswrd = password_hash($newpsswrd, PASSWORD_BCRYPT);
    $dni = $_SESSION['dni'];
    echo"entramos a actualizar la contraseña";
    $operacionExitosa = updatePasswrdUsingDni($dni, $newpsswrd);
if ($operacionExitosa) {
    echo"la operacion ha sido $operacionExitosa";
    $_SESSION['PsswrdActualizada'] = true;
}
//haya éxito o no iremos a index
header("Location: ../index.php");
}



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

include_once("../Views/footer.php");
?>
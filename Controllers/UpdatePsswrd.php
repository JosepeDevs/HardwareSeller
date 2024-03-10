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
        print '<form action="UpdatePsswrd.php" method="POST">';
        print '<h1>Recuperación de contraseña</h1>';
        print '<h2><label>Escriba su nueva contraseña</label></h2><br><br><input type="password" name="newpsswrd"><br><br>';
        print '<br><input type="submit" value="Submit"></form>';
        $atras =false; 
    } else {
        $atras= true;
    }
} else if (isset($_POST['newpsswrd']) && !empty($_POST['newpsswrd'])){
    $newpsswrd = $_POST['newpsswrd'];
    $newpsswrd = password_hash($newpsswrd, PASSWORD_BCRYPT);
    $dni = $_SESSION['dni'];
   //print"entramos a actualizar la contraseña";
    $operacionExitosa = updatePasswrdUsingDni($dni, $newpsswrd);
    if ($operacionExitosa) {
       // print"la operacion ha sido $operacionExitosa";
        $_SESSION['PsswrdActualizada'] = true;
        $todoOK =true;
    } else{
        $todoOK =false;
    }
}

?>
    <h3> <p><a id='cerrar' href="../index.php">Cancelar</a></p></h3>

<?


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


if( ! isset($_POST['newpsswrd']) && isset($_POST['mail']) && isset($_POST['dni']) ) {
    if($atras == true){
        //haya éxito o no, iremos a index
        print "<script>history.back();</script>";
        exit;
    };
} else if (isset($_POST['newpsswrd']) && !empty($_POST['newpsswrd'])){
    if($todoOK == true){
        //haya éxito o no, iremos a index
        header("Location: /index.php");
        exit;
    };
}

include_once("../Views/footer.php");
?>
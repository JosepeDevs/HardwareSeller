<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
//ESTA PÁGINA NO SE DEBE PROTEGER, ACCESIBLE A TODOS LOS NAVEGANTES
//HEADER Y TITULO
include_once("header.php");

//RECIBIMOS POR POST LOS DATOS 
if(isset( $_POST['estadoEnvio'] ) && !empty($_POST['estadoEnvio'] )){
    if($_POST['estadoEnvio'] == "tiendaCONcuenta"){
        $_SESSION['estadoEnvio'] = 5;
        //header("Location: ../Views/MetodoDePago.php");
        //exit;
    } else if($_POST['estadoEnvio'] == "tiendaSINcuenta"){
        $_SESSION['estadoEnvio'] = 5;
        $_SESSION['sinCuenta'] = true;
        //header("Location: ../Views/MetodoDePago.php");
        //exit;
    }else if($_POST['estadoEnvio'] == "DireccionAreaCliente"){
        $_SESSION['estadoEnvio'] = '0.';
        //header("Location:../Views/DireccionSeleccionada.php");
        //exit;
    }else if($_POST['estadoEnvio'] == "direccionYcuenta"){
        $_SESSION['estadoEnvio'] = '0.';
        //header("Location:../Views/DireccionSeleccionada.php");
        ///exit;
    }else if($_POST['estadoEnvio'] == "direccionSINcuenta"){
        $_SESSION['estadoEnvio'] = '0.';
        $_SESSION['sinCuenta'] = true;
        //header("Location:../Views/DireccionSeleccionada.php");
        //exit;
    } else{
        //en caso de algun error o casos no contemplado avanzaremos con el pedido pero se tendrá que recoger en tienda (si no quieren podrán cancelarlo)
        $_SESSION['estadoEnvio'] = 9999; //un número cualquiera para saber que no ha tenido ningún comportamiento de los esperados
        //header("Location: ../Views/DireccionTiendaSeleccionada.php");
       // exit;
    }
    print_r($_SESSION);
}
   
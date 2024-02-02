<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="/Views/estilosTabla.css">
  <link rel="icon" type="image/png" href="/Resources/HSLogoFondoBlancoYGrisSinTexto.png">
  <title>Hardware Seller</title>
</head>
<header>
    <div id="logoHeader">
        <a href="/index.php"><img src="/Resources/HSLogoAzulSinPadding.png" height="110px" alt="Logo"></a>
    </div>
    <div id="navegacion">
      <nav>
            <ul>
                <li><a href="/index.php">Inicio</a></li>
                <li class="dropdown">
                    <a href="#">Catalogue</a>
                    <div class="dropdown-content">
                        <a href="#">Pre-built computers</a>
                        <a href="#">CPU</a>
                        <a href="#">GPU</a>
                        <a href="#">Mother Boards</a>
                        <a href="#">RAM</a>
                        <a href="#">More Hardware coming soon</a>
                    </div>
                </li>
                <li>
                    <?php
                            if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
                            if( isset($_GET['destroy']) && $_GET['destroy'] == "Y" ){
                                if (session_status() == PHP_SESSION_ACTIVE) {session_unset();}
                                header("../index.php?destroy=false");
                            }
                            if( ! isset($_SESSION['user'])){
                                print('
                    <div class="tooltip-container">
                        <a class="tooltip-trigger">Ingresar/Registrarse</a>
                        <div class="tooltip-content">
                                <form action="/Controllers/conexion.php" method="post">
                                    <table class="tablaLogin">
                                    <caption><h2>Ingresar:</h2></caption>
                                    <tr>
                                        <th colspan="2">Usuario (email) :<br></td>
                                        <td><input type="text" name="user" placeholder="example@example.com"></td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Contraseña<br></th>
                                        <td><input type="password" name="key" placeholder="***********"></td>
                                    </tr>
                                    </table>
                                    <div id="IngresarYReiniciarHeader">
                                        <br> <input type="submit" value="Ingresar">
                                        <br> <input type="reset" value="Reiniciar">
                                    </div>
                                </form>
                                <div id="sinAcceso">
                                    <p><a href="/Views/ClienteALTA.php?auth='."temp".'">Registrar nuevo usuario</a></p>
                                    <p><a href="/Views/RecuperarPsswrd.php">Recuperar contraseña</a></p>
                                </div>
                        </div>
                    </div>');
                        } else{
                            print('
                            <div class="tooltip-container-logged">
                            <a class="tooltip-trigger-logged">Hola ' . $_SESSION['user'] . '</a>
                            <div class="tooltip-content-logged ">
                                <ul>');
                            $raiz= dirname(__DIR__);
                            $rutaOperaciones = $raiz.'/Controllers/OperacionesSession.php';
                            include_once("$rutaOperaciones");
                            $rutaDniByEmail=$raiz.'/Controllers/GetDniByEmailController.php';
                            include_once("$rutaDniByEmail");
                            $email=  GetEmailDeSession();
                            $dni=GetDniByEmail($email);
                            $esEditor = AuthYRolEditor();
                            $esAdmin = AuthYRolAdmin();
                            if($esAdmin){
                                print ('
                                <li><a href="/Views/ClienteEDITAR.php">Ver/editar mis datos</a></li>
                                <li><a href="/Views/PedidosLISTAR.php">Administrar PEDIDOS</a></li>
                                <li><a href="/Views/TablaClientes.php">Administrar CLIENTES</a></li>
                                <li><a href="/Views/ArticulosLISTAR.php">Administrar ARTÍCULOS</a></li>
                                <li><a href="/Views/CategoriasLISTAR.php">Administrar CATEGORÍAS</a></li>
                                <li><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></li>');
                            } else if ($esEditor){
                                print ('
                                <li><a href="/Views/ClienteEDITAR.php?dni='.$dni.'">Ver/editar mis datos</a></li>
                                <li><a href="/Views/ArticulosLISTAR.php">Administrar ARTÍCULOS</a></li>
                                <li><a href="/Views/CategoriasLISTAR.php">Administrar CATEGORÍAS</a></li>
                                <li><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></li>');
                            } else{
                                print ('
                                <li><a href="/Views/ClienteEDITAR.php">Ver/editar mis datos</a></li>
                                <li><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></li>');
                            }
                            print ('
                            </ul>
                            </div>
                        </div>');
                        }
                    ?>
                </li>
            </ul>
        </nav>
    </div>
    <form action="Busqueda.php">
    <div class="search">
            <input type="text" name="textoBuscado" placeholder="Buscar..." autofocus>
            <button type="submit">🔎︎</button>
            <button id="hamburger">☰</button>
    </div>
    </form>
</header>
<body>
<div class="cta-container">
    <img src="/Resources/backgroundHS.png">
    <div class="cuerpo">





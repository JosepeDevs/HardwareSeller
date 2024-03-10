<!DOCTYPE html>
<html class="no-js" lang="es">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="/Views/estilosTabla.css">
  <link rel="icon" type="image/png" href="/Resources/HSLogoFondoBlancoYGrisSinTexto.png">
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Hardware Seller.</title>
    <meta name="description" content="" />

    <!-- Web Font de la palntilla de bootstrap -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- ========================= CSS here ========================= todo esto es de la plantilla bootstrap de index-->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/LineIcons.2.0.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/css/tiny-slider.css" />
    <link rel="stylesheet" href="assets/css/glightbox.min.css" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
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
                    <a href="/Views/Catalogo.php">Catálogo</a>
                    <div class="dropdown-content">
                        <a href="/Views/Catalogo.php?categoria=22">Pantallas</a>
                        <a href="/Views/Catalogo.php?categoria=23">Gráficas</a>
                        <a href="/Views/Catalogo.php?categoria=24">Placas base</a>
                        <a href="/Views/Catalogo.php?categoria=25">RAM</a>
                        <a href="/Views/Catalogo.php">¡Más hardward muy pronto!</a>
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
                        <a href="/Views/AreaCliente.php" class="tooltip-trigger">Ingresar/Registrarse</a>
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
                                    <p><a href="/Views/AreaCliente.php">Acceder al área personal</a></p>
                                    <p><a href="/Views/ClienteALTA.php?auth='."temp".'">Registrar nuevo usuario</a></p>
                                    <p><a href="/Views/RecuperarPsswrd.php">Recuperar contraseña</a></p>
                                </div>
                        </div>
                    </div>');
                        } else{
                            print('
                            <div class="tooltip-container-logged">
                            <a href="/Views/AreaCliente.php" class="tooltip-trigger-logged">Hola ' . $_SESSION['user'] ." (".$_SESSION['rol'].')</a>
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
                            $esEmpleado = AuthYRolEmpleado();
                            if($esAdmin){
                                print ('
                                <li><a href="/Views/AreaCliente.php">Ir a mi espacio personal</a></li>
                                <li><a href="/Views/ClienteEDITAR.php">Ver/editar mis datos</a></li>
                                <li><a href="/Views/PedidosLISTAR.php">Administrar PEDIDOS</a></li>
                                <li><a href="/Views/TablaClientes.php">Administrar CLIENTES</a></li>
                                <li><a href="/Views/ArticulosLISTAR.php">Administrar ARTÍCULOS</a></li>
                                <li><a href="/Views/CategoriasLISTAR.php">Administrar CATEGORÍAS</a></li>
                                <li><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></li>');
                            } else if ($esEditor){
                                print ('
                                <li><a href="/Views/AreaCliente.php">Ir a mi espacio personal</a></li>
                                <li><a href="/Views/ClienteEDITAR.php?dni='.$dni.'">Ver/editar mis datos</a></li>
                                <li><a href="/Views/ArticulosLISTAR.php">Administrar ARTÍCULOS</a></li>
                                <li><a href="/Views/CategoriasLISTAR.php">Administrar CATEGORÍAS</a></li>
                                <li><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></li>');
                            } else if($esEmpleado){
                                print ('
                                <li><a href="/Views/AreaCliente.php">Ir a mi espacio personal</a></li>
                                <li><a href="/Views/PedidosLISTAR.php">Administrar PEDIDOS</a></li>
                                <li><a href="/Views/ArticulosLISTAR.php">Administrar ARTÍCULOS</a></li>
                                <li><a href="/Views/CategoriasLISTAR.php">Administrar CATEGORÍAS</a></li>
                                <li><a href="/Controllers/DestructorSession.php">Cerrar sesión</a></li>');
                            } else{
                                print ('
                                <li><a href="/Views/AreaCliente.php">Acceder al área personal</a></li>
                                <li><a href="/Views/PedidosLISTAR.php">Mis pedidos</a></li>
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
    <form action="/Views/Busqueda.php" method="post">
    <div class="search">
            <input type="text" name="textoBuscado" placeholder="Buscar..." autofocus>
            <button type="submit">🔎︎</button>
            <button id="hamburger">☰</button>
    </div>
    </form>
    <div id="carrito-header">
        <small>
            <?
                if( isset($_SESSION['productos']) && !empty($_SESSION['productos']) ){
                    //contamos cuantos codigos hay en el array de productos 
                    print count($_SESSION['productos']);
                    print (":&emsp");// imprimirmos un tabulador para separar el número de items del precio total
                }
             ?>
        </small>
        <small>
            <?  
                $sumaTotal = 0;
                if(isset($_SESSION['precios']) && !empty($_SESSION['precios'])) {
                    foreach($_SESSION['precios'] as $precio) {
                        //como solo guardamos precio en este array no tengo que especificar índice numerico ni asociativo y puedo acceder al contenido más fácil 
                        $sumaTotal += $precio;
                    }
                }
                print round($sumaTotal,2)."€";
            ?>
        </small>
        <a href="/Views/Carrito.php"><i class="lni lni-cart"></i></a>
    </div>
</header>
<body>
<div class="cta-container">
    <img src="/Resources/backgroundHS.png">
    <div class="cuerpo">
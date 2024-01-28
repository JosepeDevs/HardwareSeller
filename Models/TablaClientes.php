<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
include("header.php");
include_once("OperacionesSession.php");

$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "TablaClientes dice:  no está user en session";
    header("Location: index.php");
}

include_once("CheckRol.php");
if( AuthYRolAdmin() == false){
    session_destroy();
    echo " el rol no era adecuado";
    header("Location: index.php");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Listar clientes</title>
    <link rel="stylesheet" type="text/css" href="estilosTabla.css">
</head>
<body>
    <h1>Gestionar clientes</h1>
        <div id="EnlacesArriba">
            <h2><a class='enlace' href='clientenuevo.php'><img class='iconArribaTabla' src='add.png' alt='add user' /> Nuevo cliente</h2></a>
            <h2><a class='enlace' href='TablaClientes.php'><img class='iconArribaTabla' src='refresh.png' alt='refresh' /> Recargar tabla (super útil, no te lo creerás)</h2></a>
            <h2><a class='enlace' href='BuscarCliente.php'><img class='iconArribaTabla' src="search.png" alt="search user"/> Buscar cliente</h2></a>
            <h2><a class='enlace' href='ArticulosLISTAR.php'><img class='iconArribaTabla' src="buscaAr.png" alt="view products"/> Ver listado de productos</h2></a>
        </div>

<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
    include_once("Cliente.php");//para el PDO
    include_once("tablaClientesMensajes.php");//para ver mensajes
    include_once("OperacionesSession.php");//para el reset de session

    $arrayMensajes=getArrayMensajesTabla();
    if(is_array($arrayMensajes)){
        foreach($arrayMensajes as $mensaje) {
            echo "<h3>$mensaje</h3>";
        }
    };
    ResetearSesion();

        //ENCABEZADOS
        echo"<table>
                    <tr>
                        <th>
                            Nombre <br>Ordenar:<br>
                            <a class='ordenar' href='TablaClientes.php?ordenNombres=ASC'>A->Z</a>
                            <a class='ordenar' href='TablaClientes.php?ordenNombres=DESC'>Z->A</a>
                        </th>
                        <th>Direccion</th>
                        <th>Localidad</th>
                        <th>Provincia</th>
                        <th>Telefono</th>
                        <th>Email</th>
                        <th>DNI</th>
                        <th>Rol</th>
                        <th>Editar</th>
                        <th>Borrar</th>
                    </tr>";
            //PREPARAR ARRAYS CON OBJETOS
            $orden = isset($_GET['ordenNombres']) ? $_GET['ordenNombres']:null;
            if($orden == "ASC"){
                $arrayClientes= Cliente::getASCSortedClients();
            } else if($orden == "DESC"){
                $arrayClientes= Cliente::getDESCSortedClients();
            }else{
                $arrayClientes= Cliente::getAllClients();
            }

            $usuarioInvisible = $_SESSION['usuario'];
            $arrayAImpimir=[];
            $filasTotales = count($arrayClientes);
            $numPagPredeterminado=3;
            $filasAMostrar = isset($_GET['numpag'])? $_GET['numpag'] : $numPagPredeterminado;

            if(! isset($_GET['pag'])){
                $paginaActual = 0;
            }else{
                if( is_numeric($_GET['pag'])){
                    $paginaActual = $_GET['pag'] - 1 ;
                } else if ($_GET['pag'] == "X" ){
                    $paginaActual = "X";
                }
            }

            if(is_numeric($paginaActual)){
                $ultimoRegistroMostrado = $paginaActual * $filasAMostrar;
            }

            //todo: cuando llega a usuarioInvisible (el admin que logeó), hace que una página tengan un registro menos de los que se deberían mostrar
            if(is_numeric($paginaActual)){
                $ultimoRegistroMostrado = $paginaActual * $filasAMostrar;
                $finalRegistro = min($ultimoRegistroMostrado + $filasAMostrar, $filasTotales);
                $j = 0; // Variable para indexar $arrayAImpimir (era para intentar solucionar no mostrar datos del admin logeado)
                for($i=$ultimoRegistroMostrado ; $i < $finalRegistro; $i++){
                    $nombre = $arrayClientes[$i]->getNombre();
                    $direccion = $arrayClientes[$i]->getDireccion();//.".".$j.".".$finalRegistro;
                    $localidad = $arrayClientes[$i]->getLocalidad();
                    $provincia = $arrayClientes[$i]->getProvincia();
                    $telefono = $arrayClientes[$i]->getTelefono();
                    $email = $arrayClientes[$i]->getEmail();
                    $dni = $arrayClientes[$i]->getDni();
                    $rol = $arrayClientes[$i]->getRol();
                    $psswrd=null;
                    $cliente = new Cliente($dni,$nombre,$direccion,$localidad,$provincia,$telefono,$email,$psswrd,$rol);
                    if($email !== $usuarioInvisible){
                        $arrayAImpimir[$j] = $cliente;
                        $j++;
                    }
                }
            }
            if($paginaActual == "X"){
                $arrayAImpimir=$arrayClientes;
            }
            //IMPRIMIR DATOS OBJETOS
            foreach($arrayAImpimir as $cliente){
                $nombre = $cliente->getNombre();
                $direccion = $cliente->getDireccion();
                $localidad = $cliente->getLocalidad();
                $provincia = $cliente->getProvincia();
                $telefono = $cliente->getTelefono();
                $email = $cliente->getEmail();
                $dni = $cliente->getDni();
                $rol = $cliente->getRol();
                if($email == $usuarioInvisible){
                    //no imprimimos los datos del admin logeado
                    continue;
                }
                echo "<tr>
                        <td>$nombre</td>
                        <td>$direccion</td>
                        <td>$localidad</td>
                        <td>$provincia</td>
                        <td>$telefono</td>
                        <td>$email</td>
                        <td>$dni</td>
                        <td>$rol</td>
                        <td><a class='icon' href='editarcliente.php?dni=$dni&rol4consulta=administradormaestro'><img src='edit.png' alt='Editar cliente' /></td>
                        <td><a class='icon' href='borrarcliente.php?dni=$dni'><img src='delete.png' alt='Borrar cliente' /></td>";
                    }
        echo "      </tr>
            </table>";


            //PAGINACIÓN
            echo "<div class='paginacion'>";
            $paginasTotales = ceil($filasTotales / $filasAMostrar);
            if(is_numeric($paginaActual) && is_numeric($filasAMostrar)){
                //estamos viendo los registros paginados
                for ($p = 1; $p <= $paginasTotales; $p++) {
                    if($p == $paginaActual +1 ){
                        echo "<b>$p</b>";
                    }else{
                        echo "<a href='TablaClientes.php?pag=$p&ordenNombres=$orden&numpag=$filasAMostrar'>$p</a>";
                    }
                }
            } else{
                //estamos viendo todos los registros en una página
                for ($p = 1; $p <= $paginasTotales; $p++) {
                    echo "<a href='TablaClientes.php?pag=$p&ordenNombres=$orden&numpag=$filasAMostrar'>$p</a>";
                }
            }
            $opcionesNumPag=[3,4,5];
            if (isset($_GET['pag']) && ( $_GET['pag'] == "X" ) ){
                echo "<b>Ver todos</b>";
            } else{
                echo "<a href='TablaClientes.php?pag=X&ordenNombres=$orden'>Ver todos</a>";
            }
            echo "
            <form action='TablaClientes.php' method='GET'>
            <label for='numpag'>Registros/página</label><br>
            <select id='numpag' name='numpag' onchange='this.form.submit()' required>
                <option value='$filasAMostrar'>$filasAMostrar</option>";
                for ($i = 1; $i < count($opcionesNumPag); $i++) {
                    if( $opcionesNumPag[$i] == $filasAMostrar){
                        continue;
                    } else{
                        echo "<option value='$opcionesNumPag[$i]'>$opcionesNumPag[$i]</option>";
                    }
                }
            echo
            "</select><br>
            </form>
            </div>";
            include("footer.php");
            ?>

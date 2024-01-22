<!DOCTYPE html>
<html>
<head>
    <title>Editar cliente</title>
    <link rel="stylesheet" type="text/css" href="estilosTabla.css">
</head>
<body>
    <h1>Gestionar tu usuario</h1>
    <?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start();}
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "EditarCliente dice: shit no está user en session";
    header("Location: index.php");
}

include_once("EditarClienteMensajes.php");
include_once("conectarBD.php");
include_once("Cliente.php");
include_once("CheckRol.php");

echo("<h2>Bienvenido usuario</h2>");
//ponemos "editando" en true para que cuando lo mandemos a ValidarDatos lo trate como update
$_SESSION["editandoCliente"]="true";
try {
    $conPDO=contectarBbddPDO();
    //el DNI ha llegado por la url (es el dni original, lo guardamos como dni)
    $dniOriginal=$_GET["dni"];
    $_SESSION["dniOriginal"] = $dniOriginal;
    $verClienteQuery=("select * from clientes WHERE dni=:dniOriginal");
    $statement= $conPDO->prepare($verClienteQuery);
    $statement->bindValue(':dniOriginal', $dniOriginal);
    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,'Cliente');
    //ENCABEZADOS obtenidos de la clase, por si más adelante añadimos atributos
    //https://www.php.net/manual/en/class.reflectionproperty.php
    $reflejo = new ReflectionClass('Cliente');
    $arrayAtributos = $reflejo->getProperties(ReflectionProperty::IS_PRIVATE);//como hemos puesto todos private vamos a meter esos en un array
    $rol4consulta = isset($_GET['rol4consulta'])? $_GET['rol4consulta'] : null;
    echo"<table>";
    echo"<tr><th>Atributos:</th>";
    foreach ($arrayAtributos as $index => $atributo) {
        $nombreAtributo = $atributo->getName();
        if($rol4consulta == 'administradormaestro' && $nombreAtributo == "rol"){
            echo "<th>$nombreAtributo (user /editor)</th>";
        }else if($rol4consulta == 'administradormaestro'){
            echo "<th>$nombreAtributo</th>";
        }else{
            if($nombreAtributo == 'rol'){
                break;
            } else{
                echo "<th>$nombreAtributo   </th>";
            }
        }

    }
    echo "</tr>";
    echo"<tr><th>Datos actuales:</th>";
    //datos actuales del objeto Cliente, aquí no sé como aprovechar lo de arriba para no tener que "hardcodear" los atributos
    while($fila=$statement->fetch()){
        $dniOriginal=$fila->getDni();
        $nombre=$fila->getNombre();
        $direccion=$fila->getDireccion();
        $localidad=$fila->getLocalidad();
        $provincia=$fila->getProvincia();
        $telefono=$fila->getTelefono();
        $email=$fila->getEmail();
        $rolCliente=$fila->getRol();
        //TO DO: hacer una consulta SQL a la BDDD para recuperar la contnraseña hasheada, para que cuando no escriban nada en la contraseña que se mantenga la que tuvieran.
        $psswrd=$fila->getPsswrd();
        echo "
        <td>$dniOriginal</td>
        <td>$nombre</td>
        <td>$direccion</td>
        <td>$localidad</td>
        <td>$provincia</td>
        <td>$telefono</td>
        <td>$email</td>
        <td></td>";//esta fila esta vacia para que no muestre la contraseña
        if($rol4consulta == "administradormaestro"){
            echo"<td>$rolCliente</td>";
        }
            }
            $_SESSION['dni'] = $dniOriginal;
            $_SESSION["email"] = $email;
            $_SESSION["psswrd"] = $psswrd;
            //dentro de la tabla creamos el formulario, que enviará los datos a ValidarDatos, ese archivo php sabrá de donde viene por la $_SESSION
            echo '<form action="ValidarDatos.php" method="POST">';//ENVIAREMOS MEDIANTE $_POST["DNI"] EL DNI NUEVO (SI LO HA EDITADO)
            echo"<tr><th>Nuevos datos</th>";
            //creamos un campo por cada atributo
            foreach ($arrayAtributos as $index => $atributo) {
                $nombreAtributo = $atributo->getName();

                if($rol4consulta == 'administradormaestro') {
                    if($nombreAtributo == "rol") {
                        echo "
                            <td>
                                <select id='rol' name='rol' required>
                                    <option value='user'>User</option>
                                    <option value='editor'>Editor</option>
                                </select>
                            </td>";
                    } elseif($nombreAtributo == "dni") {
                        echo "<td>$dniOriginal</td>";
                    } elseif( $nombreAtributo == "psswrd") {
                        echo "<td><input type='password' id='$nombreAtributo' name='$nombreAtributo'></td>";//no required
                    } else {
                        echo "<td><input type='text' id='$nombreAtributo' name='$nombreAtributo' required></td>";
                    };
                } else{
                    if($nombreAtributo == "dni") {
                        echo "<td>$dniOriginal</td>";
                    } elseif($nombreAtributo == "rol") {
                        //no imprimir nada
                    } elseif($nombreAtributo == "psswrd") {
                        echo "<td><input type='password' id='$nombreAtributo' name='$nombreAtributo' ></td>";//no required
                    } else {
                        echo "<td><input type='text' id='$nombreAtributo' name='$nombreAtributo' required></td>";
                    }
                }
            }
            echo "</tr>";
            echo"<tr>
                    <th>Consejos</th>
                    <td colspan=8> Puede dejar la contraseña sin rellenar para mantener la misma que tenía.</td>
                </tr>";
            echo "</table>";
            echo "<div><h2><input type='submit' value='Guardar'></h2>";
            echo "<h2><input type='reset' value='Reinicar'></h2></div>";
            echo "</form>";
        } catch(PDOException $e) {
           $_SESSION["BadUpdateCliente"]=true;
           header("Location: editarcliente.php");
        };

        $arrayMensajes=getArrayMensajes();
        if(is_array($arrayMensajes)){
            foreach($arrayMensajes as $mensaje) {
                echo "<h3>$mensaje</h3>";
            }
        };

        //según el rol cambia el comportamiento del botón
        if(AuthYRolAdmin() == true){
            echo("<h2><a class='cerrar' href='TablaClientes.php?editandoCliente=false'>Cancelar edición / volver a la tabla</a></h2>");
        } else {
            echo("<h2><a class='cerrar' href='ArticulosLISTAR.php?editandoArticulo=false'>Ver listado de productos</a></h2>");
            echo("<h2><a class='cerrar' href='index.php?editandoCliente=false'>Cancelar edición / cerrar sesión</a></h2>");
        }
        echo"<br><br><br><br><br>";
        echo("<h2><a class='cerrar' href='borrarcliente.php?dni=$dniOriginal'>BORRAR CUENTA (ELIMINA TODOS LOS DATOS)</a></h2>");
?>
</body>
</html>
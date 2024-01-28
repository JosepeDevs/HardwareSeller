<?php
include("header.php");
?>
    <h1>
        Buscar cliente por DNI
    </h1>
<form action="BuscarCliente.php" method="POST">
    <table>
        <tr>
            <th><label for="dni">DNI:</label></th>
        </tr>
        <tr>
            <td><input type="text" name="dni" autofocus required pattern="^\d{8}\w{1}$"><br><br></td>
        </tr>
    </table>
    <br>
    <div>
        <h2><input type="submit" value="Consultar"></h2>

    </div>
</form>
<br><br><br>

<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    //session_destroy();
    echo "TablaClientes dice: shit no está user en session";
    //header("Location: index.php");
}

include_once("conectarBD.php");
include_once("Cliente.php");

if(isset($_POST["dni"])) {
    $dni=$_POST["dni"];
    try {
        $conPDO=contectarBbddPDO();
        $verClienteQuery=("select * from clientes WHERE dni='$dni'");
        $statement= $conPDO->prepare($verClienteQuery);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Cliente');
        //ENCABEZADOS obtenidos de la clase, por si más adelante añadimos atributos
        //https://www.php.net/manual/en/class.reflectionproperty.php
        $reflejo = new ReflectionClass('Cliente');
        $arrayAtributos = $reflejo->getProperties(ReflectionProperty::IS_PRIVATE);//como hemos puesto todos private vamos a meter esos en un array
        echo"<table>";
        echo"<tr><th>Atributos:</th>";
        foreach ($arrayAtributos as $atributo) {
            $nombreAtributo = $atributo->getName();
            echo "<th>$nombreAtributo</th>";
        }
        echo "</tr>";
        echo"<tr><th>Datos del cliente consultado:</th>";
        //datos actuales del objeto Cliente, aquí no sé como aprovechar lo de arriba para no tener que "hardcodear" los atributos
        $cliente= $statement->fetch();
        if($cliente == false){
            $_SESSION['DniNotFound'] = true;
            header("Location:BuscarCliente.php");
        }
        $nombre=$cliente->getNombre();
        $direccion=$cliente->getDireccion();
        $localidad=$cliente->getLocalidad();
        $provincia=$cliente->getProvincia();
        $telefono=$cliente->getTelefono();
        $email=$cliente->getEmail();
        $dni=$cliente->getDni();
        $rol=$cliente->getRol();
//admin no debe poder ver contraseñas, por eso no lo ponemos.
        echo "  <td>$dni</td>
                <td>$nombre</td>
                <td>$direccion</td>
                <td>$localidad</td>
                <td>$provincia</td>
                <td>$telefono</td>
                <td>$email</td>
                <td></td>
                <td>$rol</td>";
        echo "</tr>";
        echo "</table>";
    } catch(PDOException $e) {
        $_SESSION['OperationFailed'] = true;
        header("Location: BuscarCliente.php");
    };
}

include_once("BuscarClienteMensajes.php");
$arrayMensajes=getArrayMensajesBuscar();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};

?>
<br><br><br>
<h2><a class="cerrar"  href="TablaClientes.php">Volver a la tabla</a></h2>
<?php
include("footer.php");
?>
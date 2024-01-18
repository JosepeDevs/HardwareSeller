<!DOCTYPE html>
<html>
<head>
    <title>Buscar artículo</title>
    <link rel="stylesheet" type="text/css" href="estilosTabla.css">
</head>
<body>
    <h1>
        Buscar articulo por código
    </h1>
<form action="ArticuloBUSCAR.php" method="POST">
    <table>
        <tr>
            <th><label for="codigo">Código:</label></th>
        </tr>
        <tr>
            <td><input type="text" name="codigo" autofocus required><br><br></td>
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
include_once("UserSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticuloBUSCAR dice: no está user en session";
    header("Location: index.php");
}

include_once("conectarBD.php");
include_once("Articulo.php");
include_once("cliente.php");
include_once("CheckRol.php");
include_once("ExtraeDeSession.php");
include_once("ValidaCodigoArticulo.php");

if(isset($_POST["codigo"])) {
    $codigo=$_POST["codigo"];
    $codigo = TransformarCodigo($codigo);
    try {
        $conPDO=contectarBbddPDO();
        $query=("select * from articulos WHERE codigo='$codigo'");
        $statement= $conPDO->prepare($query);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Articulo');

        //ENCABEZADOS
        $reflejo = new ReflectionClass('Articulo');
        $arrayAtributos = $reflejo->getProperties(ReflectionProperty::IS_PRIVATE);//como hemos puesto todos private vamos a meter esos en un array
        echo"<table>";
        echo"<tr><th>Atributos:</th>";
        foreach ($arrayAtributos as $atributo) {
            $nombreAtributo = $atributo->getName();
            echo "<th>$nombreAtributo</th>";
        }
        echo "</tr>";
        echo"<tr><th>Datos del cliente consultado:</th>";
        //DATOS DEL OBJETO
        $cliente= $statement->fetch();
        if($cliente == false){
            $_SESSION['CodigoNotFound'] = true;
            header("Location:ArticuloBUSCAR.php");
        }
        $nombre=$cliente->getNombre();
        $codigo=$cliente->getCodigo();
        $descripcion=$cliente->getDescripcion();
        $categoria=$cliente->getCategoria();
        $precio=$cliente->getPrecio();
        $imagen=$cliente->getImagen();

        echo "  <td>$codigo</td>
                <td>$nombre</td>
                <td>$descripcion</td>
                <td>$categoria</td>
                <td>$precio</td>
                <td><img class='imagenes' src='{$imagen}' width='200' height='200'/></td>";
        echo "</tr>";
        echo "</table>";
    } catch(PDOException $e) {
        $_SESSION['OperationFailed'] = true;
        header("Location: ArticulosBUSCAR.php");
    };
}

include_once("ArticuloBUSCARMensajes.php");
$arrayMensajes=getArrayMensajesArticulos();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};

echo'
<br><br><br>
<h2><a class="cerrar" href="ArticulosLISTAR.php"><img src="arrow.png" alt="listar articulos" />Volver a la tabla de artículos</a></h2>';
$rol = GetRolDeSession();
if($rol == "admin" || $rol == "editor"){
    echo '<h2><a class="cerrar"  href="TablaClientes.php">Ver usuarios</a></h2>';
} else{
    try{
        $email = GetEmailDeSession();
        $conPDO=contectarBbddPDO();
        $query=("select * from clientes WHERE email='$email'");
        $statement= $conPDO->prepare($query);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Cliente');
        $cliente= $statement->fetch();
        $dni= $cliente->getDni();
        echo"<h2><a class='enlace' href='editarcliente.php?dni=$dni'><img src='edit.png' alt='editar datos user' /> Editar mis datos $email </h2></a></a>";
    }catch(PDOException $e) {
        $_SESSION['OperationFailed'] = true;
        echo"<h2><a class='enlace' href='ArticuloBUSCAR.php'><img src='arrow.png' alt='buscar articulo' /> Buscar articulo </h2></a></a>";
    };
}
?>
</body>
</html>
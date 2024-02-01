<?php
include_once("header.php");
?>
    <h1>
        Buscar articulo por código
    </h1>
<form action="../Controllers/ArticuloBUSCAR.php" method="POST">
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
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticuloBUSCAR dice: no está user en session";
    header("Location: /index.php");
}
include_once("../Controllers/ArticuloBUSCARController.php");
include_once("../Controllers/ArticuloBUSCARMensajes.php");
include_once("../Controllers/GetDniByEmailController.php");

if(isset($_POST["codigo"])) {
    $codigo=$_POST["codigo"];
    $codigo = TransformarCodigo($codigo);
    echo"<table>";
    echo"<tr><th>Atributos:</th>";
    //ENCABEZADOS
    $arrayAtributos = getArrayAtributos();
    foreach ($arrayAtributos as $atributo) {
        $nombreAtributo = $atributo;
        echo "<th>$nombreAtributo</th>";
    }
    echo "</tr>";
    echo"<tr><th>Datos del artículo consultado:</th>";
    //DATOS DEL OBJETO
    $articulo = getArticuloByCodigo($codigo);
    if($articulo == false || $articulo == null){
        $_SESSION['CodigoNotFound'] = true;
        header("Location:ArticuloBUSCAR.php");
    }

    foreach ($arrayAtributos as $index => $atributo) {
        $nombreAtributo = $atributo;
        $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
        $valor = $articulo->$getter();//lo llamamos para obtener el valor
        if($nombreAtributo == "imagen"){
            echo " <td><img class='imagenes' src='{$imagen}' width='200' height='200'/></td>";
        } else if($nombreAtributo == "activo"){
            if($valor == 1){
                echo "<td>Activo (1)</td>";
            } else{
                echo "<td>Inactivo (0)</td>";
            }
        } else {
            echo "<td>$valor</td>";
        }
    echo "</tr>";
    echo "</table>";
    }

    $arrayMensajes=getArrayMensajesArticulos();
    if(is_array($arrayMensajes)){
        foreach($arrayMensajes as $mensaje) {
            echo "<h3>$mensaje</h3>";
        }
    };

    echo'
    <h2><a class="cerrar" href="ArticulosLISTAR.php"><img src="../Resources/arrow.png" alt="listar articulos" />Volver a la tabla de artículos</a></h2>';
    $rol = GetRolDeSession();
    if($rol == "admin" || $rol == "editor"){
        echo '<h2><a class="cerrar"  href="TablaClientes.php">Ver usuarios</a></h2>';
    } else{
        $email = GetEmailDeSession();
        $dni = GetDniByEmail($email);
        echo"<h2>
                <a class='enlace' href='ClienteEDITAR.php?dni=$dni'>
                    <img src='../Resources/edit.png' alt='editar datos user' /> Editar mis datos $email
                </a>
            </h2>";
    }
}
include_once("footer.php");
?>
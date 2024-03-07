<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ClientesBuscar dice: shit no está user en session";
    header("Location: index.php");
    exit;
}
$rol = GetRolDeSession();
if( $rol == "admin" || $rol == "empleado" ){
} else{
    session_destroy();
    echo "Articulos alta dice: no está user en session";
    header("Location: /index.php");
    exit;

}

include("header.php");
?>
    <h1>
        Buscar cliente por DNI
    </h1>
<form action="ClienteBUSCAR.php" method="POST">
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


include_once("../Controllers/ClienteBUSCARController.php");
$arrayAtributos = getArrayAtributosCliente();

if(isset($_POST["dni"])) {
    $dni=$_POST["dni"];
    $cliente = getClienteByDni($dni);
    if($cliente == false){
        $_SESSION['ClientNotFound']=true;
        header("location: ClienteBUSCAR.php");
        exit;
    }else{
            //ENCABEZADOS obtenidos de la clase, por si más adelante añadimos atributos
            //https://www.php.net/manual/en/class.reflectionproperty.php
            echo"<table>";
                    echo"<tr><th>Atributos:</th>";
                                foreach ($arrayAtributos as $atributo) {
                                    $nombreAtributo = $atributo;
                                    echo "<th>$nombreAtributo</th>";
                                }
                    echo "</tr>";
                    echo"<tr><th>Datos del cliente consultado:</th>";
            //datos actuales del objeto Cliente
                        foreach ($arrayAtributos as $index => $atributo) {
                            $nombreAtributo = $atributo;
                            $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                            $valor = $cliente->$getter();//lo llamamos para obtener el valor
                            if($nombreAtributo == "psswrd"){
                                echo "<td>***</td>";//admin no debe poder ver contraseñas, por eso no lo ponemos.
                            } else if($nombreAtributo == "activo"){
                                if($valor == 1){
                                    echo "<td>Activo (1)</td>";
                                } else{
                                    echo "<td>Inactivo (0) </td>";
                                }
                            } else {
                                echo "<td>$valor</td>";
                            }
                        }
                    echo "</tr>
                </table>";
    }
}
include_once("../Controllers/ClienteBUSCARMensajes.php");
$arrayMensajes=getArrayMensajesBuscar();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};


///////////PONER AQUÍ QUE SI ESTÁ DESACTIVADO, ?¿ACTIVAR???

?>
<h2><a class="cerrar"  href="TablaClientes.php">Volver a la tabla</a></h2>
<?php
include("footer.php");
?>
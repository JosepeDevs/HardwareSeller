<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "ClientesBuscar dice: shit no está user en session";
    header("Location: index.php");
    exit;
}
$rol = GetRolDeSession();
if( $rol == "admin" || $rol == "empleado" ){
} else{
    session_destroy();
    print "Articulos alta dice: no está user en session";
    header("Location: /index.php");
    exit;

}

include("header.php");

include_once("../Controllers/ClienteBUSCARController.php");
$arrayAtributos = getArrayAtributosCliente();
$clienteEncontrado = false;
if(isset($_POST["dni"])) {
    $dni=$_POST["dni"];
    $cliente = getClienteByDni($dni);
    if($cliente == false){
        header("location: ClienteBUSCAR.php");
        exit;
    } else{
        $clienteEncontrado =true; // ponemos esto a true para que el código detrás del texto se ejecute, havcemos esto para poder poner el header antes que cualquier texto
    }
}
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

if($clienteEncontrado){
            //ENCABEZADOS obtenidos de la clase, por si más adelante añadimos atributos
            //https://www.php.net/manual/en/class.reflectionproperty.php
            print"<table>";
                    print"<tr><th>Atributos:</th>";
                                foreach ($arrayAtributos as $atributo) {
                                    $nombreAtributo = $atributo;
                                    print "<th>$nombreAtributo</th>";
                                }
                    print "</tr>";
                    print"<tr><th>Datos del cliente consultado:</th>";
            //datos actuales del objeto Cliente
                        foreach ($arrayAtributos as $index => $atributo) {
                            $nombreAtributo = $atributo;
                            $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                            $valor = $cliente->$getter();//lo llamamos para obtener el valor
                            if($nombreAtributo == "psswrd"){
                                print "<td>***</td>";//admin no debe poder ver contraseñas, por eso no lo ponemos.
                            } else if($nombreAtributo == "activo"){
                                if($valor == 1){
                                    print "<td>Activo (1)</td>";
                                } else{
                                    print "<td>Inactivo (0) </td>";
                                }
                            } else {
                                print "<td>$valor</td>";
                            }
                        }
                    print "</tr>
                </table>";
    }

include_once("../Controllers/ClienteBUSCARMensajes.php");
$arrayMensajes=getArrayMensajesBuscar();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        print "<h3>$mensaje</h3>";
    }
};


///////////PONER AQUÍ QUE SI ESTÁ DESACTIVADO, ?¿ACTIVAR???

?>
<h2><a class="cerrar"  href="TablaClientes.php">Volver a la tabla</a></h2>
<?php
include("footer.php");
?>
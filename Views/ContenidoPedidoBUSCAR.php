<?php
include_once("header.php");
?>
    <h1>
        Buscar contenido de un pedido ...
    </h1>
<form action="ContenidoPedidoBUSCAR.php" method="POST">
    <table>
        <tr>
            <th><h2><label for="numPedido">Número de pedido</label></h2></th>
            <th><h2><label for="codArticulo">Código del artículo</label></h2></th>
        </tr>
        <tr>
            <td><input type="text" name="numPedido" ><br><br></td>
            <td><input type="text" name="codArticulo" autofocus><br><br></td>
        </tr>
    </table>

    <br>
    <div>
        <h2><input type="submit" value="Consultar"></h2><br><br><br>
    </div>
</form>
<br><br><br><br><br><br><br>


<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ContenidoPedidoBUSCAR dice: no está user en session";
    header("Location: /index.php");
}


if(isset($_POST["numPedido"]) || isset($_POST["codArticulo"])) {
    $numPedido=null;//mejor null que sin declarar
    $codArticulo=null;//mejor null que sin declarar
    include_once("../Controllers/ContenidoPedidoBUSCARController.php");
    if(!empty(($_POST["numPedido"]))){
        $numPedido=$_POST["numPedido"];
        $ContenidoPedido = getContenidoPedidoBynumPedido($numPedido);
        if($ContenidoPedido == false){
            $_SESSION['numPedidoNotFound'] = true;
        }
        $arrayContenidoPedidos[] = $ContenidoPedido;
    }

    if(!empty(($_POST["codArticulo"]))){
        $codArticulo=$_POST["codArticulo"];
        $arrayContenidoPedidos = GetContenidoPedidoByCodArticulo($codArticulo);
        if($arrayContenidoPedidos == false){
            $_SESSION['codArticuloNotFound'] = true;
        }
    }

    $arrayAtributos = getArrayAtributosContenidoPedido();
    if( $arrayContenidoPedidos !== false){
        echo"<table>";
        echo"<tr><th>Atributos:</th>";
        //ENCABEZADOS

        foreach ($arrayAtributos as $atributo) {
            $nombreAtributo = $atributo;
            echo "<th>$nombreAtributo</th>";
        }
        //DATOS DEL OBJETO O LOS OBJETOS
        echo "</tr>";

        //todo hay que hacer que se impriman tantas lineas como numLineas haya!!
        //arrayContenidoPedidos puede conntener de 0 a vete tu a saber cuantos ContenidoPedidos
        foreach($arrayContenidoPedidos as $ContenidoPedido) {
            echo"<tr><th>Datos del ContenidoPedido encontrado:</th>";
            foreach ($arrayAtributos as $index => $atributo) {
                $codArticuloAtributo = $atributo;
                $getter = 'get' . ucfirst($codArticuloAtributo);//montamos dinámicamente el getter
                $valor = $ContenidoPedido->$getter();//lo llamamos para obtener el valor
                echo "<td>$valor</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else{
        echo "<h3>No se encontraron coincidencias.</h3>";
    }


    include_once("../Controllers/ContenidoPedidoMensajes.php");
    $arrayMensajes=getArrayMensajesContenidoPedido();
    if(is_array($arrayMensajes)){
        foreach($arrayMensajes as $mensaje) {
            echo "<h3>$mensaje</h3>";
        }
    };
}
    echo'
    <h2><a class="cerrar" href="ContenidoPedidosLISTAR.php"><img src="../Resources/arrow.png" alt="listar ContenidoPedidos" />Volver a la tabla de ContenidoPedidos</a></h2>';
    $rol = GetRolDeSession();
    if($rol == "admin" || $rol == "editor"){
        echo '<h2><a class="cerrar"  href="TablaClientes.php">Ver usuarios</a></h2>';
    } else{
        $email = GetEmailDeSession();
        $dni = GetDniByEmail($email);
        echo"<h2>
                <a class='enlace' href='ClienteEDITAR.php?dni=$dni'>
                    <img src='../Resources/edit.png' alt='editar datos user'/> Editar mis datos $email
                </a>
            </h2>";
    }

include_once("footer.php");
?>
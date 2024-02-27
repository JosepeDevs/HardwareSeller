<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
/// las funciones de contenido pedidos y pedidos ya bloquean para que los usuarios solo puedan ver lo suyo propio si no tienen un rol de admin o empleado
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "PedidosLISTAR dice: no está user en session";
    header("Location: ../index.php");
}

include_once("header.php");

$rol = GetRolDeSession();
$dni = GetDniByEmail($_SESSION['user']);

?>
<h1>
    Contenido del pedido 
</h1>

<?
//BREADCRUMBS AREA CLIENTE
include_once("BreadCrumbsAreaCliente.php");
if(!empty($_GET)){
    echo'<br><h2><a class="finForm" href="PedidoBUSCAR.php?"><img src="../Resources/arrow.png" alt="listar ContenidoPedido" />Buscar otro pedido</a></h2>
    <br>';
} else{
    echo'
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
<br><br><br><br><br><br><br>';
}


if(isset($_REQUEST["numPedido"]) || isset($_REQUEST["codArticulo"])) {
    $numPedido=null;//mejor null que sin declarar
    $codArticulo=null;//mejor null que sin declarar
    include_once("../Controllers/ContenidoPedidoBUSCARController.php");
    if(!empty(($_REQUEST["numPedido"]))){
        $numPedido=$_REQUEST["numPedido"];
        if( $rol == "admin" || $rol == "empleado" ){
            $contenidoPedido = getContenidoPedidoBynumPedido($numPedido);
            if(is_object($contenidoPedido)){
                //si es un objeto lo metemos en un array
                $arrayContenidoPedido[] = $contenidoPedido;
            } else{
                //si es un array le ponemos el nombre que nos interesa para asegurarnos que luego se le llama
                $arrayContenidoPedido = $contenidoPedido;
            }
        } else{
            $contenidoPedido = getContenidoPedidoBynumPedido($numPedido, $dni);
            if(is_object($contenidoPedido)){
                //si es un objeto lo metemos en un array
                $arrayContenidoPedido[] = $contenidoPedido;
            } else{
                //si es un array le ponemos el nombre que nos interesa para asegurarnos que luego se le llama
                $arrayContenidoPedido = $contenidoPedido;
            }
        }
        if($arrayContenidoPedido == false){
            $_SESSION['numPedidoNotFound'] = true;
        }
    }

    if(!empty(($_REQUEST["codArticulo"]))){
        $codArticulo=$_REQUEST["codArticulo"];
        if( $rol == "admin" || $rol == "empleado" ){
            $contenidoPedido = GetContenidoPedidoByCodArticulo($codArticulo);
            if(is_object($contenidoPedido)){
                //si es un objeto lo metemos en un array
                $arrayContenidoPedido[] = $contenidoPedido;
            } else{
                //si es un array le ponemos el nombre que nos interesa para asegurarnos que luego se le llama
                $arrayContenidoPedido = $contenidoPedido;
            }
        } else{
            $contenidoPedido = GetContenidoPedidoByCodArticulo($codArticulo, $dni);
            if(is_object($contenidoPedido)){
                //si es un objeto lo metemos en un array
                $arrayContenidoPedido[] = $contenidoPedido;
            } else{
                //si es un array le ponemos el nombre que nos interesa para asegurarnos que luego se le llama
                $arrayContenidoPedido = $contenidoPedido;
            }
        }
        if($arrayContenidoPedido == false){
            $_SESSION['codArticuloNotFound'] = true;
        }
    }
    //si llegados a esta punto no existe, dejalo false, si existe, déjalo como estaba
    $arrayContenidoPedido = (!isset($arrayContenidoPedido)) ?  false : $arrayContenidoPedido ;

    $arrayAtributos = getArrayAtributosContenidoPedido();
    if( $arrayContenidoPedido !== false){
        echo"<table>";
        echo"<tr><th>Atributos:</th>";
        //ENCABEZADOS

        foreach ($arrayAtributos as $atributo) {
            $nombreAtributo = $atributo;
            echo "<th>$nombreAtributo</th>";
        }
        //DATOS DEL OBJETO O LOS OBJETOS
        echo "</tr>";
        //arrayContenidoPedido puede conntener de 0 a vete tu a saber cuantos ContenidoPedido
        foreach($arrayContenidoPedido as $index => $objetoContenidoPedido) {
            echo"<tr><th>Contenidos del Pedido encontrados:</th>";
            foreach ($arrayAtributos as $atributo) {
                $getter = 'get' . ucfirst($atributo);//montamos dinámicamente el getter
                $valor = $objetoContenidoPedido->$getter();//lo llamamos para obtener el valor
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

if(isset($_REQUEST['numPedido']) && ( $rol =="admin" || $rol =="empleado" )){
    echo'
    <h2><a class="finForm" href="ContenidoPedidoEDITAR.php?numPedido='.$numPedido.'""><img src="../Resources/arrow.png" alt="listar ContenidoPedido" />Editar el contenido de este pedido</a></h2>
    ';
}
echo'
<h2><a class="finForm" href="PedidoBUSCAR.php?idPedido='.$numPedido.'"><img src="../Resources/arrow.png" alt="listar ContenidoPedido" />Volver al PEDIDO</a></h2>
<h2><a class="finForm" href="ContenidoPedidoLISTAR.php"><img src="../Resources/arrow.png" alt="listar ContenidoPedido" />Ver los contenidos de todos mis pedidos</a></h2>
';

echo '<h2><a class="finForm"  href="AreaCliente.php">Volver a mi área personal</a></h2>';


include_once("footer.php");
?>
<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "ArticuloEDITAR dice: no está user en session";
    header("Location: index.php");
    exit;

}
$rol = GetRolDeSession();
if( $rol == "admin" || $rol == "empleado" || $rol == "editor"){
} else{
    session_destroy();
    print "Articulos alta dice: no está user en session";
    header("Location: /index.php");
    exit;

}


include_once("header.php");
print("<h1>Modificar artículo</h1>");

include_once("../Controllers/ArticuloEDITARController.php");
$codigoOriginal=$_GET["codigo"];    //el codigo ha llegado por la url
$_SESSION["codigo"] = $codigoOriginal;
$arrayAtributos = getArrayAtributosArticulo();

print("<h2>Bienvenido</h2>");
//ponemos "editando" en true para que cuando lo mandemos a ValidarDatos lo trate como update
$_SESSION["editandoArticulo"]="true";
$rol4consulta = isset($_GET['rol4consulta'])? $_GET['rol4consulta'] : null;
print"<table>";
        print"<tr><th>Atributos:</th>";
                foreach ($arrayAtributos as $index => $atributo) {
                    $nombreAtributo = $atributo;
                    print "<th>$nombreAtributo</th>";
                }
        print "</tr>";

        //datos ACTUALES OBJETO (estaticos, para que se vean siempre los actuales)
        print"<tr>
                <th>Datos actuales:</th>";
                    $articulo = getArticuloByCodigo($codigoOriginal);
                    //imprimimos los valores
                    foreach ($arrayAtributos as $atributo) {
                        $getter = 'get' . ucfirst($atributo);
                        $valor = $articulo->$getter();
                        if($atributo == "imagen" ){
                            $directorio = "/Resources/ImagenesArticulos/";
                            $rutaAbsoluta = $directorio . $valor;
                            print"<td><img class='imagenes' src='{$rutaAbsoluta}' width='200' height='200'/><br>$valor</td>";
                        } else if($atributo == "activo"){
                            if($valor == 1){
                                print "<td>Activo ($valor)</td>";
                            }else{
                                print "<td>Inactivo ($valor)</td>";
                            }
                        }else{
                            print "<td>$valor</td>";
                        }
                    }
                //FORMULARIO para EDITAR PRERELLENADO para que se mantengan los datos si no cambia nada
                    print '<form action="../Controllers/ArticuloVALIDAR.php" method="POST" enctype="multipart/form-data">';//ENVIAREMOS MEDIANTE $_POST EL NUEVO (SI LO HA EDITADO)
                    print"<tr><th>Nuevos datos</th>";
                    foreach ($arrayAtributos as $atributo) {
                        $getter = 'get' . ucfirst($atributo);
                        $nombreAtributo = $atributo;
                        $valor = $articulo->$getter();
                        if( $nombreAtributo == "descuento" || $nombreAtributo == "precio") {
                            print "<td><input type='number' accept='^(\d+\.\d+|\d+)$'step='0.01' id='$nombreAtributo' name='$nombreAtributo' value='$valor'></td>";
                        } else if( $nombreAtributo == "imagen") {
                            print "<td>
                                     <input type='file' name='imagen' accept='.jpg,.jpeg,.png,.gif'>
                                </td>";
                        }else if( $nombreAtributo == "categoria") {
                            print "<td>
                                     <input type='number' name='$nombreAtributo' id='$nombreAtributo' value='$valor'>
                                </td>";
                        } else if($nombreAtributo == "activo") {
                            print "
                                <td>
                                    <select id='activo' name='activo' required>";
                                    if($valor == 0){
                                        print"
                                            <option value='0' selected>Inactivo</option>
                                            <option value='1' >Activo</option>
                                        </select>";
                                    } else{
                                        print"
                                            <option value='0' >Inactivo</option>
                                            <option value='1' selected>Activo</option>
                                        </select>";
                                    }
                                print"</td>";
                        }else{
                            print "<td><input type='text' id='$nombreAtributo' name='$nombreAtributo' value='$valor'></td>";
                        }
                    }
        print "</tr>
   </table>";
    print "<div class='finForm'><h2><input type='submit' value='Guardar'></h2>";
    print "</form>";

    include_once("../Controllers/ArticulosMensajes.php");
    $arrayMensajes=getArrayMensajesArticulos();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        print "<h3>$mensaje</h3>";
    }
};

print("<h2><a class='cerrar' href='ArticulosLISTAR.php?editandoArticulo=false'>Volver al listado de productos</a></h2>");
print("<h2><a class='cerrar' href='/index.php?editandoArticulo=false'> cerrar sesión</a></h2>");
print("<h2><a class='cerrar' a href='ArticuloBORRAR.php?codigo=$codigoOriginal'>BORRAR ARTÍCULO</a></h2>");
include_once("footer.php");
?>
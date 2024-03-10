<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "CategoriaEDITAR dice: no está user en session";
    header("Location: ../index.php");
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



include_once("header.php");
print("<h1>Modificar Categoria</h1>");

include_once("../Controllers/CategoriaEDITARController.php");
$codigoOriginal=$_GET["codigo"];    //el codigo ha llegado por la url
$_SESSION["codigo"] = $codigoOriginal;
$arrayAtributos = getArrayAtributosCategoria();

print("<h2>Bienvenido</h2>");
//ponemos "editando" en true para que cuando lo mandemos a ValidarDatos lo trate como update
$_SESSION["editandoCategoria"]="true";
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
                    $Categoria = getCategoriaByCodigo($codigoOriginal);
                    //imprimimos los valores
                    foreach ($arrayAtributos as $atributo) {
                        $getter = 'get' . ucfirst($atributo);
                        $valor = $Categoria->$getter();
                        if($atributo == "activo"){
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
                    print '<form action="../Controllers/CategoriaVALIDAR.php" method="POST" enctype="multipart/form-data">';//ENVIAREMOS MEDIANTE $_POST EL NUEVO (SI LO HA EDITADO)
                    print"<tr><th>Nuevos datos</th>";
                    foreach ($arrayAtributos as $atributo) {
                        $getter = 'get' . ucfirst($atributo);
                        $nombreAtributo = $atributo;
                        $valor = $Categoria->$getter();
                        if($nombreAtributo == "activo") {
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

include_once("../Controllers/CategoriaMensajes.php");
$arrayMensajes=getArrayMensajesCategorias();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        print "<h3>$mensaje</h3>";
    }
};

print("<h2><a class='cerrar' href='CategoriasLISTAR.php?editandoCategoria=false'>Volver al listado de categorias</a></h2>");
print("<h2><a class='cerrar' href='/index.php?editandoCategoria=false'> cerrar sesión</a></h2>");
print("<h2><a class='cerrar' a href='CategoriaBORRAR.php?codigo=$codigoOriginal'>BORRAR Categoria</a></h2>");
include_once("footer.php");
?>
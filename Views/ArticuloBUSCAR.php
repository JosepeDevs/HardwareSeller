<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
$rol = GetRolDeSession();
//NO PROTEGER SE USA EN ASIDE PARA QUE PUEDAN BUSCAR ARTICULOS
include_once("header.php");
?>
    <h1>
        Buscar articulo por ...
    </h1>
<form action="ArticuloBUSCAR.php" method="POST">
    <table>
        <tr>
            <th><h2><label for="codigo">Código:</label></h2></th>
            <th><h2><label for="nombre">Nombre:</label></h2></th>
        </tr>
        <tr>
            <td><input type="text" name="codigo" ><br><br></td>
            <td><input type="text" name="nombre" autofocus><br><br></td>
        </tr>
    </table>

    <br>
    <div>
        <h2><input type="submit" value="Consultar"></h2><br><br><br>
    </div>
</form>
<br><br><br><br><br><br><br>


<?php



if(isset($_POST["codigo"]) || isset($_POST["nombre"])) {
    $codigo=null;//mejor null que sin declarar
    $nombre=null;//mejor null que sin declarar
    $articulo=null;//mejor null que sin declarar
    include_once("../Controllers/ArticuloBUSCARController.php");
    if(!empty(($_POST["codigo"]))){
        $codigo=$_POST["codigo"];
        $codigo = TransformarCodigo($codigo);
        $articulo = getArticuloByCodigo($codigo);
        if($articulo == false){
            $_SESSION['CodigoNotFound'] = true;
            print "<script>history.back();</script>";
            exit;
        }
        $arrayArticulos[] = $articulo;
    }

    if(!empty(($_POST["nombre"]))){
        $nombre=$_POST["nombre"];
        $arrayArticulos = GetArticulosByBusquedaNombre($nombre);
        if($arrayArticulos == false){
            $_SESSION['NombreNotFound'] = true;
            print "<script>history.back();</script>";
            exit;
        }
    }

    $arrayAtributos = getArrayAtributosArticulo();
    if( $arrayArticulos !== false){
        print"<table>";
        print"<tr><th>Atributos:</th>";
        //ENCABEZADOS

        foreach ($arrayAtributos as $atributo) {
            $nombreAtributo = $atributo;
            print "<th>$nombreAtributo</th>";
        }
        if($rol !== null || $rol == "admin" || $rol == "empleado" || $rol == "editor"){
            print "<th>Editar</th>";
        }

        //DATOS DEL OBJETO O LOS OBJETOS
        print "</tr>";


        //arrayArticulos puede conntener de 0 a vete tu a saber cuantos articulos
        foreach($arrayArticulos as $articulo) {
            print"<tr><th>Datos del artículo encontrado:</th>";
            foreach ($arrayAtributos as $index => $atributo) {
                $nombreAtributo = $atributo;
                $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                $valor = $articulo->$getter();//lo llamamos para obtener el valor
                if($nombreAtributo == "imagen"){
                    $directorio = "/Resources/ImagenesArticulos/";
                    $ruta=$directorio.$valor;
                    print " <td><a href='../Views/FichaArticulo.php?codigo=$codigo'><img class='imagenes' src='{$ruta}' width='200' height='200'/><br>$valor</a></td>";
                } else if($nombreAtributo == "activo"){
                    if($valor == 1){
                        print "<td>Activo (1)</td>";
                    } else{
                        print "<td>Inactivo (0)</td>";
                    }
                } else if($nombreAtributo == "codigo"){
                    $codigo = $valor;
                    print "<td>$valor</td>";
                }else {
                    print "<td>$valor</td>";
                }
            }
            if($rol !== null || $rol == "admin" || $rol == "empleado" || $rol == "editor"){
                print "<td> 
                        <a href='ArticuloEDITAR.php?codigo=$codigo'>
                            <img class='iconArribaTabla' src='../Resources/search.png' alt='añadir' />
                        </a>
                    </td>";
            }
            print "</tr>";
        }
        print "</table>";
    } else{
        print "<h3>No se encontraron coincidencias.</h3>";
    }


    include_once("../Controllers/ArticulosMensajes.php");
    $arrayMensajes=getArrayMensajesArticulos();
    if(is_array($arrayMensajes)){
        foreach($arrayMensajes as $mensaje) {
            print "<h3>$mensaje</h3>";
        }
    };
}
print'
        <h2><a class="cerrar" href="../Views/Catalogo.php"><img src="../Resources/arrow.png" alt="listar articulos" />Volver al catálogo</a></h2>';
$rol = GetRolDeSession();
if($rol == "admin" || $rol == "editor" || $rol == "empleado") {
    print("<h2><a class='cerrar' a href='AreaCliente.php'>Ir al área personal</a></h2>");
} 


include_once("footer.php");
?>
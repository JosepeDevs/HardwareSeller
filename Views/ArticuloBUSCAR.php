<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
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
        }
        $arrayArticulos[] = $articulo;
    }

    if(!empty(($_POST["nombre"]))){
        $nombre=$_POST["nombre"];
        $arrayArticulos = GetArticulosByBusquedaNombre($nombre);
        if($arrayArticulos == false){
            $_SESSION['NombreNotFound'] = true;
        }
    }

    $arrayAtributos = getArrayAtributosArticulo();
    if( $arrayArticulos !== false){
        echo"<table>";
        echo"<tr><th>Atributos:</th>";
        //ENCABEZADOS

        foreach ($arrayAtributos as $atributo) {
            $nombreAtributo = $atributo;
            echo "<th>$nombreAtributo</th>";
        }
        //DATOS DEL OBJETO O LOS OBJETOS
        echo "</tr>";


        //arrayArticulos puede conntener de 0 a vete tu a saber cuantos articulos
        foreach($arrayArticulos as $articulo) {
            echo"<tr><th>Datos del artículo encontrado:</th>";
            foreach ($arrayAtributos as $index => $atributo) {
                $nombreAtributo = $atributo;
                $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                $valor = $articulo->$getter();//lo llamamos para obtener el valor
                if($nombreAtributo == "imagen"){
                    $directorio = "/Resources/ImagenesArticulos/";
                    $ruta=$directorio.$valor;
                    echo " <td><img class='imagenes' src='{$ruta}' width='200' height='200'/><br>$valor</td>";
                } else if($nombreAtributo == "activo"){
                    if($valor == 1){
                        echo "<td>Activo (1)</td>";
                    } else{
                        echo "<td>Inactivo (0)</td>";
                    }
                } else {
                    echo "<td>$valor</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    } else{
        echo "<h3>No se encontraron coincidencias.</h3>";
    }


    include_once("../Controllers/ArticuloBUSCARMensajes.php");
    $arrayMensajes=getArrayMensajesArticulos();
    if(is_array($arrayMensajes)){
        foreach($arrayMensajes as $mensaje) {
            echo "<h3>$mensaje</h3>";
        }
    };
}
echo'
        <h2><a class="cerrar" href="../Views/Catalogo.php"><img src="../Resources/arrow.png" alt="listar articulos" />Volver al catálogo</a></h2>';
$rol = GetRolDeSession();
if($rol == "admin" || $rol == "editor"){
        echo'
        <h2><a class="cerrar" href="ArticulosLISTAR.php"><img src="../Resources/arrow.png" alt="listar articulos" />Volver a la tabla de artículos</a></h2>';
        echo '<h2><a class="cerrar"  href="TablaClientes.php">Ver usuarios</a></h2>';
    } 


include_once("footer.php");
?>
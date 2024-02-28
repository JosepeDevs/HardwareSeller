<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "CategoriaBUSCAR dice: no está user en session";
    header("Location: /index.php");
}
$rol = GetRolDeSession();
if( $rol == "admin" || $rol == "empleado" ){
} else{
    session_destroy();
    echo "Articulos alta dice: no está user en session";
    header("Location: /index.php");
}

include_once("header.php");
?>
    <h1>
        Buscar Categoria por ...
    </h1>
<form action="CategoriaBUSCAR.php" method="POST">
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
    $Categoria=null;//mejor null que sin declarar
    include_once("../Controllers/CategoriaBUSCARController.php");
    if(!empty(($_POST["codigo"]))){
        $codigo=$_POST["codigo"];
        $Categoria = getCategoriaByCodigo($codigo);
        if($Categoria == false){
            $_SESSION['CodigoNotFound'] = true;
        }
        $arrayCategorias[] = $Categoria;
    }

    if(!empty(($_POST["nombre"]))){
        $nombre=$_POST["nombre"];
        $arrayCategorias = GetCategoriasByBusquedaNombre($nombre);
        if($arrayCategorias == false){
            $_SESSION['NombreNotFound'] = true;
        }
    }

    $arrayAtributos = getArrayAtributosCategoria();
    if( $arrayCategorias !== false){
        echo"<table>";
        echo"<tr><th>Atributos:</th>";
        //ENCABEZADOS

        foreach ($arrayAtributos as $atributo) {
            $nombreAtributo = $atributo;
            echo "<th>$nombreAtributo</th>";
        }
        //DATOS DEL OBJETO O LOS OBJETOS
        echo "</tr>";


        //arrayCategorias puede conntener de 0 a vete tu a saber cuantos Categorias
        foreach($arrayCategorias as $Categoria) {
            echo"<tr><th>Datos del Categoria encontrado:</th>";
            foreach ($arrayAtributos as $index => $atributo) {
                $nombreAtributo = $atributo;
                $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                $valor = $Categoria->$getter();//lo llamamos para obtener el valor
                 if($nombreAtributo == "activo"){
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


    include_once("../Controllers/CategoriaMensajes.php");
    $arrayMensajes=getArrayMensajesCategorias();
    if(is_array($arrayMensajes)){
        foreach($arrayMensajes as $mensaje) {
            echo "<h3>$mensaje</h3>";
        }
    };
}
    echo'
    <h2><a class="cerrar" href="CategoriasLISTAR.php"><img src="../Resources/arrow.png" alt="listar Categorias" />Volver a la tabla de Categorias</a></h2>';
    $rol = GetRolDeSession();
    if($rol == "admin" || $rol == "editor" || $rol == "empleado") {
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
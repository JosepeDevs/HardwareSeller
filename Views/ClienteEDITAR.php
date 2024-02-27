<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("../Controllers/OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ClienteEDITAR dice: no está user en session";
    header("Location: ../index.php");
}

include_once("header.php");

print("<h1>Modificar cliente</h1>");
include_once("../Controllers/ClienteEDITARController.php");
if(isset($_GET['dni'])){
    //el DNI ha llegado por la url
    $rol = GetRolDeSession();
    //solo podrán acceder a esa info algunos roles
    if( $rol == "admin" || $rol == "empleado" ){
        $dniOriginal=$_GET["dni"];
    }
} else{
    //hemos llegado con la url vacia
    $raiz= dirname(__DIR__);
    $rutaDniByEmail=$raiz.'/Controllers/GetDniByEmailController.php';
    include_once("$rutaDniByEmail");
    $email=  GetEmailDeSession();
    $dniOriginal=GetDniByEmail($email);
}
$_SESSION["dni"] = $dniOriginal;
$arrayAtributos = getArrayAtributos();

echo("<h2>Bienvenido $email</h2>");
//ponemos "editando" en true para que cuando lo mandemos a ValidarDatos lo trate como update
$_SESSION["editandoCliente"]="true";

include_once("../Controllers/OperacionesSession.php");
$rol4consulta = isset($_GET['rol4consulta'])? $_GET['rol4consulta'] : null;
echo"<table>";
        echo"<tr><th>Atributos:</th>";
                foreach ($arrayAtributos as $index => $atributo) {
                    $nombreAtributo = $atributo;
                    echo "<th>$nombreAtributo</th>";
                }
        echo "</tr>";

        //datos ACTUALES OBJETO (estaticos, para que se vean siempre los actuales)
        echo"<tr>
                <th>Datos actuales:</th>";
                    $cliente = getClienteByDni($dniOriginal);
                    foreach ($arrayAtributos as $index => $atributo) {
                        $nombreAtributo = $atributo;
                        $getter = 'get' . ucfirst($nombreAtributo);//montamos dinámicamente el getter
                        $valor = $cliente->$getter();//lo llamamos para obtener el valor
                        if($rol4consulta == 'administradormaestro') {
                            if($nombreAtributo == "dni") {
                                echo "<td>$dniOriginal</td>";
                            } elseif( $nombreAtributo == "psswrd") {
                                echo "<td></td>";//no required y vacio, la contraseña no debe mostrarse
                            } else {
                                echo "<td>$valor</td>";
                            };
                        } else{
                            //NO ES UN ADMIN
                            if($nombreAtributo == "dni") {
                                echo "<td>$dniOriginal</td>";
                            } elseif($nombreAtributo == "rol") {
                                //no admins no deben ver el rol
                            }elseif($nombreAtributo == "email") {
                                echo "<td>$valor</td>";
                                $_SESSION['email'] =$valor; //subimos a session el email original del cliente, por si se lo intenta modificar
                            } elseif($nombreAtributo == "psswrd") {
                                echo "<td></td>";//no required y sin imprimir
                            } else {
                                echo "<td>$valor</td>";
                            }
                        }
                    }



                //FORMULARIO para EDITAR PRERELLENADO para que se mantengan los datos si no cambia nada
                echo '<form action="/Controllers/ValidarDatosCliente.php" method="POST" enctype="multipart/form-data">';//ENVIAREMOS MEDIANTE $_POST EL NUEVO (SI LO HA EDITADO)
                echo"<tr><th>Nuevos datos</th>";
                    foreach ($arrayAtributos as $index => $atributo) {
                        $nombreAtributo = $atributo;
                        $getter = 'get' . ucfirst($atributo);
                        $valor = $cliente->$getter();
                        if($rol4consulta == 'administradormaestro') {
                            if($nombreAtributo == "rol") {
                                echo "
                                    <td>
                                        <select id='rol' name='rol' required value='$valor'>
                                            <option value='user'>User</option>
                                            <option value='editor'>Editor</option>
                                            <option value='empleado'>Empleado</option>
                                            <option value='admin'>Administrador</option>
                                        </select>
                                    </td>";
                            } elseif($nombreAtributo == "activo") {
                                echo "
                                    <td>
                                        <select id='activo' name='activo' required>";
                                        if($valor == 0){
                                            echo"
                                                <option value='0' selected>Inactivo</option>
                                                <option value='1' >Activo</option>
                                            </select>";
                                        } else{
                                            echo"
                                                <option value='0' >Inactivo</option>
                                                <option value='1' selected>Activo</option>
                                            </select>";
                                        }
                                    echo"</td>";
                            } elseif($nombreAtributo == "dni") {
                                echo "<td>$dniOriginal</td>";//no input, dni no se debe poder cambiar
                            } elseif( $nombreAtributo == "psswrd") {
                                echo "<td><input type='password' id='$nombreAtributo' name='$nombreAtributo'></td>";//no required, pueden dejarlo en blanco y la psswd debe mantenerse la misma
                            }elseif( $nombreAtributo == "email") {
                                echo "<td><input type='email' id='$nombreAtributo' name='$nombreAtributo'  value='$valor' required></td>";//required, pueden dejarlo como esta para mantenerlo o escribir otro para cambiarlo
                                $_SESSION['email'] =$valor; //subimos a session el email original del cliente, por si se lo intenta modificar
                            } else {
                                echo "<td><input type='text' id='$nombreAtributo' name='$nombreAtributo' required value='$valor'></td>";
                            };
                        } else{
                            //si no es un administrador:
                            if($nombreAtributo == "dni") {
                                echo "<td>$dniOriginal</td>";
                            } elseif($nombreAtributo == "rol") {
                                //no imprimir nada
                            } elseif($nombreAtributo == "psswrd") {
                                echo "<td><input type='password' id='$nombreAtributo' name='$nombreAtributo'></td>";//no required y vacio
                            } elseif( $nombreAtributo == "email") {
                                echo "<td><input type='email' id='$nombreAtributo' name='$nombreAtributo' value='$valor' required></td>";//required, pueden dejarlo como esta para mantenerlo o escribir otro para cambiarlo
                                $_SESSION['email'] =$valor; //subimos a session el email original del cliente, por si se lo intenta modificar
                            } else {
                                echo "<td><input type='text' id='$nombreAtributo' name='$nombreAtributo'  value='$valor' required></td>";
                            }
                        }
                    }
        echo "</tr>
            <tr>
                <th>Consejos</th>
                <td colspan=8> Puede dejar la contraseña sin rellenar para mantener la misma que tenía.</td>
            </tr>
   </table>";
    echo "<div class='finForm'><h2><input type='submit' value='Guardar'></h2>";
    echo "</form>";

include_once("../Controllers/ClienteEDITARMensajes.php");
$arrayMensajes=getArrayMensajes();
if(is_array($arrayMensajes)){
    foreach($arrayMensajes as $mensaje) {
        echo "<h3>$mensaje</h3>";
    }
};

//según el rol cambia el comportamiento del botón
if(AuthYRolAdmin() == true){
    echo("<h2><a class='cerrar' href='TablaClientes.php?editandoCliente=false'>Cancelar edición / volver a la tabla</a></h2>");
} else {
    echo("<h2><a class='cerrar' href='TablaClientes.php?editandoCliente=false'>Ver listado de productos</a></h2>");
    echo("<h2><a class='cerrar' href='/index.php?editandoCliente=false'>Cancelar edición / cerrar sesión</a></h2>");
}
echo"<br><br><br><br><br>";
echo("<h2><a class='cerrar' href='ClienteBORRAR.php?dni=$dniOriginal'>BORRAR CUENTA (ELIMINA TODOS LOS DATOS)</a></h2>");

include_once("footer.php");
?>
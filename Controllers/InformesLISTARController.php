<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

include_once("OperacionesSession.php");
$rolEsAdmin = AuthYRolAdmin();
if(!$rolEsAdmin) {
    session_destroy();
    echo "PedidoVALIDAR dice: no está user en session";
    header("Location: /index.php");
}

include_once("OperacionesSession.php");
include_once("../Controllers/Directorio.php");


//todo wrappear esto en función y llamar a CLASE Informes (también todo)
if(
    isset( $_GET["EstadisticasUsuariosWeb"] ) &&
    $_GET["EstadisticasUsuariosWeb"] == 1  &&
    isset( $_GET["dni"] ) 
) {

    $dni=$_GET['dni'];
    $textoGenerado = EstadisticasUsuariosWeb($dni);//con llamar al método se debería descargar

}

function EstadisticasUsuariosWeb($dni){

    try{
        $con= contectarBbddPDO();
        $sql="SELECT * FROM clientes WHERE activo=1";
        $statement=$con->prepare($sql);
        $statement->execute();
        $arrayClientesActivos=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
        $_SESSION['ClientesActivos']= count($arrayClientesActivos);
    } catch (Exception $e) {
        $_SESSION['BadOperation'] = true;
        return false;
    }

    try{
        $con= contectarBbddPDO();
        $sql="SELECT * FROM clientes WHERE activo=0";
        $statement=$con->prepare($sql);
        $statement->execute();
        $arrayClientesInactivos=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
        $_SESSION['ClientesInactivos']= count($arrayClientesInactivos);
    } catch (Exception $e) {
        $_SESSION['BadOperation'] = true;
        return false;
    }
    
    $_SESSION['NumeroClientes'] = count(array_merge($arrayClientesActivos, $arrayClientesInactivos));

    $carpeta = DirectorioInformes();
    $nombreArchivo='estadisticasClientes'.date("Y-m-d")."txt";
    $rutaArchivo = $carpeta.$nombreArchivo;
    $informe = fopen($rutaArchivo, "w");//esto también intenta crearla
    $dniLog ="Informe generado por consulta de adminitrador con $dni";
    $totalRegistrados = "Número de clientes registrados (activos e inactivos): ".$_SESSION['NumeroClientes'];
    $activosRegistrados="Número de clientes registrados (activos): ".$_SESSION['ClientesActivos'];
    $inactivosRegistrados="Número de clientes registrados (inactivos): ".$_SESSION['ClientesInactivos'];
    $textoInforme = $dni."\n".$totalRegistrados."\n".$activosRegistrados."\n".$inactivosRegistrados;

    if (fwrite($informe, $dniLog . PHP_EOL) !== false && //EOL es end of line, vamos que hace un break line
        fwrite($informe, $totalRegistrados . PHP_EOL) !== false &&
        fwrite($informe, $activosRegistrados . PHP_EOL) !== false &&
        fwrite($informe, $inactivosRegistrados . PHP_EOL) !== false) {
        $_SESSION["InformeGenerado"] = true;
        return $textoInforme;
    }
    fclose($informe);
    
    //todo  TCPDF  o FPDF u otro para poder descargarlo como pdf

    header("Content-Disposition: attachment; filename=".basename($rutaArchivo)); // con attachement el browser sabe que debe descargar, cojemos solo el nombre del archivo con basename
    header("Content-Length: " . filesize($rutaArchivo)); // Configura el tamaño del archivo,necesario para que se pueda ver una barra de progreso de la descarga
    header("Content-Type: application/octet-stream;"); // Establece el tipo MIME de contenido a octet-stream, esto ayuda a que se descarge y no se intente sacar por pantalla
    readfile($rutaArchivo); // Lee y envía el archivo al cliente

}

function EstadisticasArticulosWeb(){
//nº activos
//nº inactivos
//articulo más vendido

}
function EstadisticasPedidosTotales(){

//numero total de pedidos
//promedio gasto en pedidos
//total facturado

}

function EstadisticasPedidosRangoFechas(){

//numero total de pedidos
//promedio gasto en pedidos
//total facturado

}

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

function EstadisticasUsuariosWeb(){

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
    $totalRegistrados = "Número de clientes registrados (activos e inactivos): ".$_SESSION['NumeroClientes'];
    $activosRegistrados="Número de clientes registrados (activos): ".$_SESSION['ClientesActivos'];
    $inactivosRegistrados="Número de clientes registrados (inactivos): ".$_SESSION['ClientesInactivos'];
    $textoInforme = $totalRegistrados."\n".$activosRegistrados."\n".$inactivosRegistrados;

    if (fwrite($informe, $totalRegistrados . PHP_EOL) !== false && //EOL es end of line, vamos que hace un break line
        fwrite($informe, $activosRegistrados . PHP_EOL) !== false &&
        fwrite($informe, $inactivosRegistrados . PHP_EOL) !== false) {
        $_SESSION["InformeGenerado"] = true;
        return $textoInforme;
    }
    fclose($informe);

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

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

//todo  TCPDF  o FPDF u otro para poder descargarlo como pdf


function EstadisticasUsuariosWeb($dni){
    include_once('../Models/Cliente.php');
    //todo pendeinte refactorizar para que haga el query con count que es más efeiciente que devolver todo de todas las filas
    try{
        $con= contectarBbddPDO();
        $sql="SELECT * FROM clientes WHERE activo=1";
        $statement=$con->prepare($sql);
        $statement->execute();
        $arrayClientesActivos=$statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Cliente");
        $_SESSION['ClientesActivos']= count($arrayClientesActivos);
    } catch (Exception $e) {
        $_SESSION['BadCliente'] = true;
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
        $_SESSION['BadCliente'] = true;
        return false;
    }
    
    $_SESSION['NumeroClientes'] = count(array_merge($arrayClientesActivos, $arrayClientesInactivos));

    $carpeta = DirectorioInformes();
    $nombreArchivo='estadisticasClientes'.date("Y-m-d").".txt";
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
        $_SESSION["InformeClientesGenerado"] = true;
        return $textoInforme;
    }
    fclose($informe);
    

    header("Content-Disposition: attachment; filename=".basename($rutaArchivo)); // con attachement el browser sabe que debe descargar, cojemos solo el nombre del archivo con basename
    header("Content-Length: " . filesize($rutaArchivo)); // Configura el tamaño del archivo,necesario para que se pueda ver una barra de progreso de la descarga
    header("Content-Type: application/octet-stream;"); // Establece el tipo MIME de contenido a octet-stream, esto ayuda a que se descarge y no se intente sacar por pantalla
    readfile($rutaArchivo); // Lee y envía el archivo al cliente

}

function EstadisticasArticulosWeb($dni){

    try{
        $con= contectarBbddPDO();
        $sql="SELECT COUNT(codigo) FROM articulos WHERE activo=1";
        $statement=$con->prepare($sql);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_COLUMN);// queremos  la fila 
        $totalActivos = intval($result);
    } catch (Exception $e) {
        $_SESSION['BadArticulos'] = true;
        return false;
    }

    try{
        $con= contectarBbddPDO();
        $sql="SELECT COUNT(codigo) FROM articulos WHERE activo=0";
        $statement=$con->prepare($sql);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_COLUMN);// queremos  la fila 
        $totalInactivos=intval($result);
    } catch (Exception $e) {
        $_SESSION['BadArticulos'] = true;
        return false;
    }
    include_once("../Models/Articulo.php");
    try{
        $con= contectarBbddPDO();
        $sql="  SELECT articulos.*, COUNT(contenidopedido.codArticulo)
                FROM contenidopedido  
                JOIN articulos ON contenidopedido.codArticulo = articulos.codigo
                GROUP BY contenidopedido.codArticulo
                ORDER BY COUNT(contenidopedido.codArticulo) DESC
                LIMIT  1;";
        $statement=$con->prepare($sql);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Articulo");
        $articuloMasVendido=$statement->fetch();
        if(empty($articuloMasVendido) || $articuloMasVendido == false){
            $_SESSION["ProblemaArticuloMasVendido"] = true;
            $articuloMasVendido = "No se pudo determinar el artículo más vendido";
        } else{
            $articuloCodigo = $articuloMasVendido->getCodigo();
            $nombreNombre = $articuloMasVendido->getNombre();
            $categoriaCategoria = $articuloMasVendido->getCategoria();
            $precioPrecio = $articuloMasVendido->getPrecio();
            $descuentoDescuento = $articuloMasVendido->getDescuento();
            $articuloMasVendido = "el artículo más vendido es". $articuloCodigo .". con nombre: ". $nombreNombre .". Con categoria: ". $categoriaCategoria .". Con precio: ". $precioPrecio .". Vendido con descuento: ". $descuentoDescuento;
        }
    } catch (Exception $e) {
        $_SESSION['BadArticulos'] = true;
        return false;
    }
    
    $totalArticulos= $totalActivos + $totalInactivos;

    $carpeta = DirectorioInformes();
    $nombreArchivo='estadisticasArticulos'.date("Y-m-d").".txt";
    $rutaArchivo = $carpeta.$nombreArchivo;
    $informe = fopen($rutaArchivo, "w");//esto también intenta crearla
    $dniLog ="Informe generado por consulta de adminitrador con $dni";
    $textoNumeroTotal = "Número de artículos registrados (activos e inactivos): ".$totalArticulos;
    $textoActivos="Número de artículos registrados (activos): ".$totalActivos;
    $textoInactivos ="Número de artículos registrados (inactivos): ".$totalInactivos;
    $textoInforme = $dni."\n".$textoNumeroTotal."\n".$textoActivos."\n".$textoInactivos."\n".$articuloMasVendido;

    if (fwrite($informe, $dniLog . PHP_EOL) !== false && //EOL es end of line, vamos que hace un break line
        fwrite($informe, $textoNumeroTotal . PHP_EOL) !== false &&
        fwrite($informe, $textoActivos . PHP_EOL) !== false &&
        fwrite($informe, $textoInactivos . PHP_EOL) !== false &&
        fwrite($informe, $articuloMasVendido . PHP_EOL) !== false) {
        $_SESSION["InformeArticulosGenerado"] = true;
        return $textoInforme;
    }
    fclose($informe);
    

    header("Content-Disposition: attachment; filename=".basename($rutaArchivo)); // con attachement el browser sabe que debe descargar, cojemos solo el nombre del archivo con basename
    header("Content-Length: " . filesize($rutaArchivo)); // Configura el tamaño del archivo,necesario para que se pueda ver una barra de progreso de la descarga
    header("Content-Type: application/octet-stream;"); // Establece el tipo MIME de contenido a octet-stream, esto ayuda a que se descarge y no se intente sacar por pantalla
    readfile($rutaArchivo); // Lee y envía el archivo al cliente

}
function EstadisticasPedidosWeb($dni){

    try{
        $con= contectarBbddPDO();
        $sql="SELECT SUM(total) FROM pedidos";
        $statement=$con->prepare($sql);
        $statement->execute();
        $facturacionTotal=$statement->fetch();
        $facturacionTotal=round(floatval($facturacionTotal),2);
    } catch (Exception $e) {
        $_SESSION['BadPedido'] = true;
        return false;
    }

    try{
        $con= contectarBbddPDO();
        $sql="SELECT AVG(total) FROM pedidos";
        $statement=$con->prepare($sql);
        $statement->execute();
        $promedioTotalPedidos=$statement->fetch();
        $promedioTotalPedidos=round(floatval($promedioTotalPedidos),2);
    } catch (Exception $e) {
        $_SESSION['BadPedido'] = true;
        return false;
    }

    try{
        $con= contectarBbddPDO();
        $sql="  SELECT COUNT(*) FROM pedidos;";
        $statement=$con->prepare($sql);
        $statement->execute();
        $numeroPedidosTotal=$statement->fetch();
        $numeroPedidosTotal=intval($numeroPedidosTotal);
    } catch (Exception $e) {
        $_SESSION['BadPedido'] = true;
        return false;
    }
    
    $carpeta = DirectorioInformes();
    $nombreArchivo='estadisticasPedidosAllTime'.date("Y-m-d").".txt";
    $rutaArchivo = $carpeta.$nombreArchivo;
    $informe = fopen($rutaArchivo, "w");//esto también intenta crearla
    $dniLog ="Informe generado por consulta de adminitrador con $dni";
    $textoFacturacionTotal = "Facturacion total = ".$facturacionTotal;
    $textoPromedioPedidos=" Promedio total de los pedidos = ".$promedioTotalPedidos;
    $textoNumeroPedidos ="Número de pedidos recibidos= ".$numeroPedidosTotal;
    $textoInforme = $dni."\n".$textoFacturacionTotal."\n".$textoPromedioPedidos."\n".$textoNumeroPedidos."\n";

    if (fwrite($informe, $dniLog . PHP_EOL) !== false && //EOL es end of line, vamos que hace un break line
        fwrite($informe, $textoFacturacionTotal . PHP_EOL) !== false &&
        fwrite($informe, $textoPromedioPedidos . PHP_EOL) !== false &&
        fwrite($informe, $textoNumeroPedidos . PHP_EOL) !== false) {
        $_SESSION["InformePedidoGenerado"] = true;
        return $textoInforme;
    }
    fclose($informe);
    

    header("Content-Disposition: attachment; filename=".basename($rutaArchivo)); // con attachement el browser sabe que debe descargar, cojemos solo el nombre del archivo con basename
    header("Content-Length: " . filesize($rutaArchivo)); // Configura el tamaño del archivo,necesario para que se pueda ver una barra de progreso de la descarga
    header("Content-Type: application/octet-stream;"); // Establece el tipo MIME de contenido a octet-stream, esto ayuda a que se descarge y no se intente sacar por pantalla
    readfile($rutaArchivo); // Lee y envía el archivo al cliente

}

function EstadisticasPedidosRangoFechas($dni, $fechaInicio, $fechaFin){


try{
    $con= contectarBbddPDO();
    $sql="SELECT SUM(total) FROM pedidos WHERE fecha >= :fechaInicio AND fecha <= :fechaFin;";
    $statement=$con->prepare($sql);
    $statement->execute();
    $facturacionTotal=$statement->fetch();
    $facturacionTotal=round(floatval($facturacionTotal),2);
} catch (Exception $e) {
    $_SESSION['BadPedido'] = true;
    return false;
}

try{
    $con= contectarBbddPDO();
    $sql="SELECT AVG(total) FROM pedidos  WHERE fecha >= :fechaInicio AND fecha <= :fechaFin;";
    $statement=$con->prepare($sql);
    $statement->execute();
    $promedioTotalPedidos=$statement->fetch();
    $promedioTotalPedidos=round(floatval($promedioTotalPedidos),2);
} catch (Exception $e) {
    $_SESSION['BadPedido'] = true;
    return false;
}

try{
    $con= contectarBbddPDO();
    $sql="  SELECT COUNT(*) FROM pedidos  WHERE fecha >= :fechaInicio AND fecha <= :fechaFin;;";
    $statement=$con->prepare($sql);
    $statement->execute();
    $numeroPedidosTotal=$statement->fetch();
    $numeroPedidosTotal=intval($numeroPedidosTotal);
} catch (Exception $e) {
    $_SESSION['BadPedido'] = true;
    return false;
}

$carpeta = DirectorioInformes();
$nombreArchivo='estadisticasPPedidosAllTime'.date("Y-m-d").".txt";
$rutaArchivo = $carpeta.$nombreArchivo;
$informe = fopen($rutaArchivo, "w");//esto también intenta crearla
$dniLog ="Informe generado por consulta de adminitrador con $dni";
$textoFacturacionTotal = "Facturacion total = ".$facturacionTotal;
$textoPromedioPedidos=" Promedio total de los pedidos = ".$promedioTotalPedidos;
$textoNumeroPedidos ="Número de pedidos recibidos= ".$numeroPedidosTotal;
$textoInforme = $dni."\n".$textoFacturacionTotal."\n".$textoPromedioPedidos."\n".$textoNumeroPedidos."\n";

if (fwrite($informe, $dniLog . PHP_EOL) !== false && //EOL es end of line, vamos que hace un break line
    fwrite($informe, $textoFacturacionTotal . PHP_EOL) !== false &&
    fwrite($informe, $textoPromedioPedidos . PHP_EOL) !== false &&
    fwrite($informe, $textoNumeroPedidos . PHP_EOL) !== false) {
    $_SESSION["InformeGenerado"] = true;
    return $textoInforme;
}
fclose($informe);


header("Content-Disposition: attachment; filename=".basename($rutaArchivo)); // con attachement el browser sabe que debe descargar, cojemos solo el nombre del archivo con basename
header("Content-Length: " . filesize($rutaArchivo)); // Configura el tamaño del archivo,necesario para que se pueda ver una barra de progreso de la descarga
header("Content-Type: application/octet-stream;"); // Establece el tipo MIME de contenido a octet-stream, esto ayuda a que se descarge y no se intente sacar por pantalla
readfile($rutaArchivo); // Lee y envía el archivo al cliente


}

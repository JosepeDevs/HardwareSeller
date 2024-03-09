<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

include_once("OperacionesSession.php");
$rolEsAdmin = AuthYRolAdmin();
if(!$rolEsAdmin) {
    session_destroy();
    echo "PedidoVALIDAR dice: no está user en session";
    header("Location: /index.php");
    exit;

}

include_once("OperacionesSession.php");
include_once("../Controllers/Directorio.php");

//todo  TCPDF  o FPDF u otro para poder descargarlo como pdf, por ahora tendrán que guardar la página en pdf si lo quieren como pdf


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
    $nombreArchivo='estadisticasClientes'.date("Y-m-d.H-i").".txt";
    $rutaArchivo = $carpeta.$nombreArchivo;
    $informe = fopen($rutaArchivo, "w");//esto también intenta crearla
    $dniLog ="Informe generado por consulta de adminitrador con $dni"."\n";
    $totalRegistrados = "Número de clientes registrados (activos e inactivos): ".$_SESSION['NumeroClientes']."\n";
    $activosRegistrados="Número de clientes registrados (activos): ".$_SESSION['ClientesActivos']."\n";
    $inactivosRegistrados="Número de clientes registrados (inactivos): ".$_SESSION['ClientesInactivos']."\n";

    if (fwrite($informe, $dniLog . PHP_EOL) !== false && //EOL es end of line, vamos que hace un break line
        fwrite($informe, $totalRegistrados . PHP_EOL) !== false &&
        fwrite($informe, $activosRegistrados . PHP_EOL) !== false &&
        fwrite($informe, $inactivosRegistrados . PHP_EOL) !== false) {
        $_SESSION["InformeClientesGenerado"] = true;
    }
    fclose($informe);

    $textoTotalInforme =  $dniLog.$totalRegistrados.$activosRegistrados.$inactivosRegistrados;
    $arrayConEnlaceYTexto = array($nombreArchivo, $textoTotalInforme);
    return $arrayConEnlaceYTexto;
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
            $articuloMasVendido = "\nEl artículo más vendido tiene el codigo ". $articuloCodigo .". con nombre: ". $nombreNombre .". Con categoria: ". $categoriaCategoria .". Con precio: ". $precioPrecio .". Vendido con descuento: ". $descuentoDescuento;
        }
    } catch (Exception $e) {
        $_SESSION['BadArticulos'] = true;
        return false;
    }
    
    $totalArticulos= $totalActivos + $totalInactivos;

    $carpeta = DirectorioInformes();
    $nombreArchivo='estadisticasArticulos'.date("Y-m-d.H-i").".txt";
    $rutaArchivo = $carpeta.$nombreArchivo;
    $informe = fopen($rutaArchivo, "w");//esto también intenta crearla
    $dniLog ="Informe generado por consulta de adminitrador con $dni"."\n";
    $textoNumeroTotal = "Número de artículos registrados (activos e inactivos): ".$totalArticulos."\n";
    $textoActivos="Número de artículos registrados (activos): ".$totalActivos."\n";
    $textoInactivos ="Número de artículos registrados (inactivos): ".$totalInactivos."\n";
    
    if (fwrite($informe, $dniLog . PHP_EOL) !== false && //EOL es end of line, vamos que hace un break line
        fwrite($informe, $textoNumeroTotal . PHP_EOL) !== false &&
        fwrite($informe, $textoActivos . PHP_EOL) !== false &&
        fwrite($informe, $textoInactivos . PHP_EOL) !== false &&
        fwrite($informe, $articuloMasVendido . PHP_EOL) !== false) {
        $_SESSION["InformeArticulosGenerado"] = true;
    }
    fclose($informe);
    
    $textoTotalInforme =  $dniLog.$textoNumeroTotal.$textoActivos.$textoInactivos.$articuloMasVendido;
    $arrayConEnlaceYTexto = array($nombreArchivo, $textoTotalInforme);
    return $arrayConEnlaceYTexto;

}
function EstadisticasPedidosWeb($dni){

    try{
        $con= contectarBbddPDO();
        $sql="SELECT SUM(total) FROM pedidos";
        $statement=$con->prepare($sql);
        $statement->execute();
        $facturacionTotal = $statement->fetch(PDO::FETCH_COLUMN);// queremos  la fila 
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
        $promedioTotalPedidos = $statement->fetch(PDO::FETCH_COLUMN);// queremos  la fila 
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
        $numeroPedidosTotal = $statement->fetch(PDO::FETCH_COLUMN);// queremos  la fila 
        $numeroPedidosTotal=intval($numeroPedidosTotal);
    } catch (Exception $e) {
        $_SESSION['BadPedido'] = true;
        return false;
    }
    
    $carpeta = DirectorioInformes();
    $nombreArchivo='estadisticasPedidosAllTime'.date("Y-m-d.H-i").".txt";
    $rutaArchivo = $carpeta.$nombreArchivo;
    $informe = fopen($rutaArchivo, "w");//esto también intenta crearla
    $dniLog ="Informe generado por consulta de adminitrador con $dni"."\n";
    $textoFacturacionTotal = "Facturacion total = ".$facturacionTotal."\n";
    $textoPromedioPedidos=" Promedio total de los pedidos = ".$promedioTotalPedidos."\n";
    $textoNumeroPedidos ="Número de pedidos recibidos= ".$numeroPedidosTotal."\n";

    if (fwrite($informe, $dniLog . PHP_EOL) !== false && //EOL es end of line, vamos que hace un break line
        fwrite($informe, $textoFacturacionTotal . PHP_EOL) !== false &&
        fwrite($informe, $textoPromedioPedidos . PHP_EOL) !== false &&
        fwrite($informe, $textoNumeroPedidos . PHP_EOL) !== false) {
        $_SESSION["InformePedidoGenerado"] = true;
    }
    fclose($informe);
    
    $textoTotalInforme =  $dniLog.$textoFacturacionTotal.$textoPromedioPedidos.$textoNumeroPedidos;
    $arrayConEnlaceYTexto = array($nombreArchivo, $textoTotalInforme);
    return $arrayConEnlaceYTexto;

}

function EstadisticasPedidosRangoFechas($dni, $fechaInicio, $fechaFin){


try{
    $con= contectarBbddPDO();
    $sql="SELECT SUM(total) FROM pedidos WHERE fecha >= :fechaInicio AND fecha <= :fechaFin;";
    $statement=$con->prepare($sql);
    $statement->bindParam(':fechaInicio',$fechaInicio);
    $statement->bindParam(':fechaFin',$fechaFin);
    $statement->execute();
    $facturacionTotal = $statement->fetch(PDO::FETCH_COLUMN);// queremos  la columna de total 
    $facturacionTotal=round(floatval($facturacionTotal),2);
} catch (Exception $e) {
    $_SESSION['BadPedido'] = true;
    return false;
}

try{
    $con= contectarBbddPDO();
    $sql="SELECT AVG(total) FROM pedidos  WHERE fecha >= :fechaInicio AND fecha <= :fechaFin;";
    $statement=$con->prepare($sql);
    $statement->bindParam(':fechaInicio',$fechaInicio);
    $statement->bindParam(':fechaFin',$fechaFin);
    $statement->execute();
    $promedioTotalPedidos = $statement->fetch(PDO::FETCH_COLUMN);// queremos  la columna de total 
    $promedioTotalPedidos=round(floatval($promedioTotalPedidos),2);
} catch (Exception $e) {
    $_SESSION['BadPedido'] = true;
    return false;
}

try{
    $con= contectarBbddPDO();
    $sql="  SELECT COUNT(*) FROM pedidos  WHERE fecha >= :fechaInicio AND fecha <= :fechaFin;;";
    $statement=$con->prepare($sql);
    $statement->bindParam(':fechaInicio',$fechaInicio);
    $statement->bindParam(':fechaFin',$fechaFin);
    $statement->execute();
    $numeroPedidosTotal = $statement->fetch(PDO::FETCH_COLUMN);// queremos  la columna de total 
    $numeroPedidosTotal=intval($numeroPedidosTotal);
} catch (Exception $e) {
    $_SESSION['BadPedido'] = true;
    return false;
}

$carpeta = DirectorioInformes();
$nombreArchivo='estadisticasPedidosAllTime'.date("Y-m-d.H-i").".txt";
$rutaArchivo = $carpeta.$nombreArchivo;
$informe = fopen($rutaArchivo, "w");//esto también intenta crearla
$dniLog ="Informe generado por consulta de adminitrador con $dni\n";
$fechasLog ="Periodo consultado entre $fechaInicio y $fechaFin.\n";
$textoFacturacionTotal = "Facturacion total = ".$facturacionTotal."\n";
$textoPromedioPedidos=" Promedio total de los pedidos = ".$promedioTotalPedidos."\n";
$textoNumeroPedidos ="Número de pedidos recibidos= ".$numeroPedidosTotal."\n";


if (fwrite($informe, $dniLog . PHP_EOL) !== false && //EOL es end of line
    fwrite($informe, $fechasLog . PHP_EOL) !== false &&
    fwrite($informe, $textoFacturacionTotal . PHP_EOL) !== false &&
    fwrite($informe, $textoPromedioPedidos . PHP_EOL) !== false &&
    fwrite($informe, $textoNumeroPedidos . PHP_EOL) !== false) {
    $_SESSION["InformePedidoGenerado"] = true;
}
fclose($informe);

$textoTotalInforme =  $dniLog.$fechasLog.$textoFacturacionTotal.$textoPromedioPedidos.$textoNumeroPedidos;
$arrayConEnlaceYTexto = array($nombreArchivo, $textoTotalInforme);
return $arrayConEnlaceYTexto;

}

<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

include_once("../Controllers/OperacionesSession.php");
$rolEsAdmin = AuthYRolAdmin();
if(!$rolEsAdmin) {
    session_destroy();
    header("Location: /index.php");
}

if (isset($_REQUEST["informe"])) {

  $informe = urldecode($_REQUEST["informe"]); // pasar a nombre normal

//validar con regex el nombre de un archivo
  if (preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $informe)) {
    $rutaArchivo = "../Reports/" . $informe;

    if (file_exists($rutaArchivo)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($rutaArchivo));
      flush(); // Flush system output buffer
      readfile($rutaArchivo);
      die();
    } else {
      http_response_code(404);
      die();
    }
  } else {
    include_once('header.php');
    print"<p> Nombre del archivo no válido</p>
    <a href='InformesLISTAR.php'>Volver al generador de informes</a>";
    include_once('footer.php');
    exit;
  }
}

?>
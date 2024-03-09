<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}

include_once("../Controllers/OperacionesSession.php");
$rolEsAdmin = AuthYRolAdmin();
if(!$rolEsAdmin) {
    session_destroy();
    header("Location: /index.php");
    exit;

}

if (isset($_REQUEST["informe"])) {

  $informe = urldecode($_REQUEST["informe"]); // pasar a nombre normal

//validar con regex el nombre de un archivo
  if (preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $informe)) {// que empiece por algo que no sea punto, lo que sea /i para evitar caracteres problematicos en nombre de archivo 
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
    $_SESSION['BadnombreArchivo'] =true;
    header("Location: ../Views/InformesLISTAR.php");
    exit;
  }
}

?>
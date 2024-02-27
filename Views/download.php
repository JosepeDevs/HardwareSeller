<?php

if (isset($_REQUEST["informe"])) {

  $informe = urldecode($_REQUEST["informe"]); // pasar a nombre normal

//validar con regex el nombre de un archivo
  if (preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $informe)) {
    $rutaArchivo = "Reports/" . $informe;

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
    die("Invalid file name!");
  }
}

?>
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
      header('Content-Description: File Transfer'); // para decirle al navegador que vamos a enviar un archivo
      header('Content-Type: application/octet-stream'); // Tipo de contenido: flujo binario
      header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"'); // Contenido como adjunto y el nombre del archivo
      header('Expires: 0'); // El contenido expira inmediatamente
      header('Cache-Control: must-revalidate'); // Para asegurar que el contenido se actualice correctamente en el navegador y no salga cache miss o cosas raras.
      header('Pragma: public'); // Pragma: el informe debe ser accesible  en la red, permitir  almacenamiento en caches públicos.
      header('Content-Length: ' . filesize($rutaArchivo)); // tamaño del archivo para ver % de descarga (es prácticamente instantaneo)
      flush(); // Vacia el buffer de salida del sistema. asegura  que  los datos se envien de forma inmediata y no espere a llenarse para mandarlo.
      readfile($rutaArchivo); // Lee (en este caso envía) el archivo al navegador
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
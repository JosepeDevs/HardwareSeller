  <?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include("header.php");
include_once("IndexMensajes.php");
  $arrayMensajes=getArrayMensajesIndex();
  if(is_array($arrayMensajes)){
      foreach($arrayMensajes as $mensaje) {
          echo "<h3>$mensaje</h3>";
      }
    };
  //  si hay una sesión al llegar a esta página, nos la cargaremos.
  //if (session_status() == PHP_SESSION_ACTIVE) {session_unset();}


  ?>

<section>
  <h1>Contenido</h1>
  <ul>
    <li>1</li>
    <li>2</li>
    <li>3</li>
    <li>4</li>
    <li>5</li>
  </ul>
</section>
<section>
  <h2> inscribete a nustra newsletter</h2>
  <div>
    <a href="#"><img src="https://cdn-icons-png.flaticon.com/128/2330/2330051.png" width="36px" alt="Logo"></a>
    <a href="#"><img src="https://cdn-icons-png.flaticon.com/128/2330/2330051.png" width="36px" alt="Logo"></a>
    <a href="#"><img src="https://cdn-icons-png.flaticon.com/128/2330/2330051.png" width="36px" alt="Logo"></a>
  </div>
</section>
<section>
  <h2>Posts</h2>
  <a href="#"><img src="https://cdn-icons-png.flaticon.com/128/2330/2330051.png" width="36px" alt="Logo"></a>
    <a href="#"><img src="https://cdn-icons-png.flaticon.com/128/2330/2330051.png" width="36px" alt="Logo"></a>
    <a href="#"><img src="https://cdn-icons-png.flaticon.com/128/2330/2330051.png" width="36px" alt="Logo"></a>
</section>
<?php
include("footer.php");
?>

  <?php
include("Views/header.php");
?>

<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("Controllers/IndexMensajes.php");
$arrayMensajes=getArrayMensajesIndex();
if(is_array($arrayMensajes)){
  foreach($arrayMensajes as $mensaje) {
    echo "<h3>$mensaje</h3>";
  }
};
?>
<h1>Where hardware feels like home</h1>

<section>
  <h2>Browse parts</h2>
  <ul>
    <li><a href="#">CPU</a></li>
    <li><a href="#">GPU</a></li>
    <li><a href="#">Mother Boards</a></li>
    <li><a href="#">RAM</a></li>
  </ul>
</section>
<section>
  <h2> Build your own PC</h2>
  <div class="carousel">
    <a href="#"><img src="https://cdn-icons-png.flaticon.com/128/2330/2330051.png" width="36px" alt="Log1o"></a>
    <a href="#"><img src="https://cdn-icons-png.flaticon.com/128/2330/2330051.png" width="36px" alt="Log2o"></a>
    <a href="#"><img src="https://cdn-icons-png.flaticon.com/128/2330/2330051.png" width="36px" alt="Lo2go"></a>
  </div>
</section>
<section>
  <h2>Posts and tips</h2>
    <div id="IndexPosts">
      <a href="#"><img src="https://cdn-icons-png.flaticon.com/128/2330/2330051.png" width="36px" alt="Logo"></a>
      <a href="#"><img src="https://cdn-icons-png.flaticon.com/128/2330/2330051.png" width="36px" alt="Logo"></a>
      <a href="#"><img src="https://cdn-icons-png.flaticon.com/128/2330/2330051.png" width="36px" alt="Logo"></a>
    </div>
  </section>
<?php
include("Views/footer.php");
?>

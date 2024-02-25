<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    include("Views/header.php");
    include_once("Controllers/IndexMensajes.php");
    $arrayMensajes=getArrayMensajesIndex();
    if(is_array($arrayMensajes)){
        foreach($arrayMensajes as $mensaje) {
            echo "<h3>$mensaje</h3>";
        }
    };
    ResetearSesion();
    
    if(isset($_GET["Destroy"]) && $_GET["Destroy"] =="Y" ){
        session_destroy();
        if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    }
   // print_r($_SESSION);
?>
    <!-- Start Hero Area -->
    <section id="hero-area" class="hero-area">
        <!-- Single Slider -->
        <div class="hero-inner">
            <div class="container">
                <div class="row ">
                    <div class="col-lg-6 co-12">
                        <div class="home-slider">
                            <div class="hero-text">
                                <h5 class="wow fadeInUp" data-wow-delay=".3s">¡Bienvenido a nuestra tienda de hardware!</h5>
                                <h1 class="wow fadeInUp" data-wow-delay=".5s">Encuentra tus piezas de hardware por el mejor precio, <br> ¡también ofrecemos construcción a medida de PC!</h1>
                                <p class="wow fadeInUp" data-wow-delay=".7s">Descubre cómo nuestros clientes han transformado sus oficinas gracias a nuestro servicio personalizado. Con  3000 visitantes diarios y muchas opiniones positivas, somos un vendedor de confianza. </p>
                                <div class="button wow fadeInUp" data-wow-delay=".9s">
                                    <a href="#" class="btn"><i class="lni lni-star"></i> Ver Opiniones</a>
                                    <a href="#" class="btn primary"><i class="lni lni-question-circle"></i> Consultar ahora</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 co-8">
                        <div class="hero-image">
                            <div class="waves-block">
                                <div class="waves wave-1"></div>
                                <div class="waves wave-2"></div>
                            </div><br><br>
                            <img src="https://cdn.pixabay.com/photo/2015/01/08/18/24/children-593313_1280.jpg" width="100%" alt="#">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ End Single Slider -->
    </section>
<!--/ End Hero Area -->

<!-- Start Features Area -->
<section id="features" class="features section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-title">
                    <h2 class="wow fadeInUp" data-wow-delay=".4s">Descubre las ventajas de nuestro servicio.</h2>
                    <ul class="wow fadeInUp"  data-wow-delay=".6s">
                        <li>Ofrecemos una garantía ampliada para que puedas tener la máxima confianza en tus compras.</li>
                        <li>Reponemos cualquier desperfecto sin coste adicional.</li>
                        <li>Para pedidos superiores a  50€, el envío es gratuito. </li>
                        <li>Además, por montajes de más de  500€, la mano de obra es gratuita</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- ... (rest of the features area content remains unchanged) ... -->
    </div>
</section>
<!-- /End Features Area -->

<!-- Start Cta Area -->
<section id="call-action" class="call-action section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 col-md-12 col-12">
                <div class="inner-content">
                    <h2 class="wow fadeInUp" data-wow-delay=".4s">¡Elige tu pieza de hardware hoy mismo!</h2>
                    <p class="wow fadeInUp" data-wow-delay=".6s">Visita nuestra tienda online y encuentra la mejor opción para tus necesidades. No esperes más, ¡comienza tu compra ya!</p>
                    <div class="button style1 wow fadeInUp" data-wow-delay=".8s">
                        <a href="https://josepedevs.infinityfreeapp.com/index.php" class="btn">Ver catálogo</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Cta Area -->

<!-- Start Pricing Table Area -->
<section id="pricing" class="pricing-table section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-title">
                    <span class="wow fadeInDown" data-wow-delay=".2s">Planes de precio</span>
                    <h2 class="wow fadeInUp" data-wow-delay=".4s">Adaptamos el presupuesto a tus necesidades y nunca al revés.</h2>
                    <p class="wow fadeInUp" data-wow-delay=".6s">Disponemos de multiples opciones para que encuentres la solución perfecta a tu presupuesto. No importa cuán grande sea tu proyecto, hay un plan que está hecho para ti, por el precio que estás buscando.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Start Clients Area -->
<section class="clients section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>Hardware seller en números</h2>
            </div>
        </div>
    </div>
</section>
<!-- /End Clients Area -->

<!-- Start Achivement Area -->
<section class="our-achievement section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="single-achievement wow fadeInUp" data-wow-delay=".2s">
                    <i class="lni lni-user"></i>
                    <h3 class="counter"><span id="logro1" class="countup" cup-end="264">264</span>K+</h3>
                    <p>visitantes web mensuales</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="single-achievement wow fadeInUp" data-wow-delay=".2s">
                    <i class=" lni lni-star-half"></i>
                    <h3 class="counter"><span id="logro2" class="countup" cup-end="264">3000</span></h3>
                    <p>Reseñas de  4 o más estrellas</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="single-achievement wow fadeInUp" data-wow-delay=".2s">
                    <i class="lni lni-star"></i>
                    <h3 class="counter"><span id="logro3" class="countup" cup-end="264">2000</span></h3>
                    <p>Reseñas de 5 estrellas</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="single-achievement wow fadeInUp" data-wow-delay=".2s">
                    <i class="lni lni-package"></i>
                    <h3 class="counter"><span id="logro4" class="countup" cup-end="264">0</span></h3>
                    <p>devoluciones. <br>¡No fallamos!</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Achivement Area -->

    <!-- ========================= scroll-top ========================= -->
    <a href="#" class="scroll-top btn-hover">
        <i class="lni lni-chevron-up"></i>
    </a>
    <!-- Keep the scroll-top button as it is, it doesn't need to change -->
    <!-- ========================= JS here ========================= -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/count-up.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/tiny-slider.js"></script>
    <script src="assets/js/glightbox.min.js"></script>
    <script src="assets/js/imagesloaded.min.js"></script>
    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/main.js"></script>
    <!-- Keep the scripts as they are, they don't need to change -->
</html>
<?php
include("Views/footer.php");
?>
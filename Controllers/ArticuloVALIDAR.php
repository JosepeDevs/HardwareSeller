<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");

checkAdminOEmpleado();

$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticuloVALIDAR dice: no está user en session";
   header("Location: /index.php");
   exit;
}

include_once("../Models/Articulo.php");
print_r($_SESSION);

$nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : null;
$descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : null;//por post llegaría el posible nuevo código
$categoria = isset($_POST["categoria"]) ? $_POST["categoria"] : null;
$precio = isset($_POST["precio"]) ? $_POST["precio"] : null;
$codigo = isset($_POST["codigo"]) ? $_POST["codigo"] : null;
$codigoOriginal = isset($_SESSION["codigo"]) ? $_SESSION["codigo"] : null;
$descuento = isset($_POST["descuento"]) ? $_POST["descuento"] : null;
$activo = isset($_POST["activo"]) ? $_POST["activo"] : null;

if($codigo == $codigoOriginal ){//si el código escrito es el mismo --> es que estaban editando y no lo quieren cambiar
    $mantenemosCodigo= true;
} else{
    $mantenemosCodigo= false;
}

$nombreValido = Articulo::ComprobarLongitud($nombre,50);
if($nombreValido == false) {    $_SESSION['LongNombre']= true; }

$descripcionValida = Articulo::ComprobarLongitud($descripcion,200);
if($descripcionValida == false) { $_SESSION['LongDescripcion']= true;}

$categoriaValido = Articulo::ComprobarLongitud($categoria,30);
if($categoriaValido == false) { $_SESSION['LongCategoria']= true;}
$categoriaEsNumero = is_int(intval($categoria));///pasamos string a int y vemos si es int ¿overkill?
if($categoriaEsNumero == false) { $_SESSION['CategoriaNoEsNumero']= true;}

$precioValido = Articulo::ComprobarLongitud($precio,11);
if($precioValido == false) { $_SESSION['LongPrecio']= true;}

$descuento = Articulo::ValorFloat($descuento);
if($descuento == false) { $_SESSION['BadDescuento']= true;}

$activoValido = Articulo::ComprobarLongitud($activo,11);
if($activoValido == false) { $_SESSION['LongActivo']= true;}

if( isset($_SESSION["editandoArticulo"]) && $_SESSION["editandoArticulo"] == "true" ){
    if($_SESSION['codigo'] !== null){
        //no han escrito código, quieren que se mantega el que ya tenía
        $codigoOriginal = $_SESSION["codigo"];
        $codigoOriginal = Articulo::TransformarCodigo($codigoOriginal);
        $codigoOriginalLibre = Articulo::CodigoLibre($codigoOriginal);
        if($codigoOriginalLibre == true) {  $_SESSION['CodigoDeberiaExistir'] = true;}
    }

    if( !$mantenemosCodigo ){
        //entonces hay codigo nuevo, validamos formato y que esté libre (el nuevo)
        $codigo = $_POST["codigo"];
            $codigo = Articulo::TransformarCodigo($codigo);

            $codigoCorrecto = Articulo::EsFormatoCodigoCorrecto($codigo);
            if($codigoCorrecto == false) {  $_SESSION['BadCodigo']= true;}

            $codigoLibre = Articulo::CodigoLibre($codigo);
            if($codigoLibre == false) {  $_SESSION['CodigoAlreadyExists']= true;}
    }

}else if( isset($_SESSION["nuevoArticulo"]) && $_SESSION["nuevoArticulo"] == "true" ){

    if( isset($_POST['codigo']) ) {//codigo nuevo  articulo llega por POST, aqui codigo es obligatorio.
    $codigo = $_POST["codigo"];
    $codigo = Articulo::TransformarCodigo($codigo);

    $codigoCorrecto = Articulo::EsFormatoCodigoCorrecto($codigo);
    if($codigoCorrecto == false) {  $_SESSION['BadCodigo']= true;}

    $codigoLibre = Articulo::CodigoLibre($codigo);
    if($codigoLibre == false) {  $_SESSION['CodigoAlreadyExists']= true;}
    }

};

if(isset($_FILES["imagen"]) && $_FILES["imagen"]["size"] !== 0){
//si han subido algún archivo entonces...
    $imagen = $_FILES["imagen"];
    //echo"<br>____________si que hay una imagen:_________";
    $imagenValida = Articulo::ValidaImagen();//comprobamos peso, tamaño y formato aquí, se sube a session los errores encontrados

    $nombreArchivo = $_FILES['imagen']['name'];//este es el nombre con el que se sube el archivo (como lo nombra el usuario)
    $directorio=PrepararDirectorio();
    //$hemerotecaBD="hemerotecaBD/";
    $directorio= $directorio.$nombreArchivo;
    //$nombreArchivoDestino = $hemerotecaBD . $nombreArchivo;//esto es lo que vamos a guardar en BBDD
    $nombreArchivoDestino =  $nombreArchivo;//ya dejo la carpeta bien identificada en prepararDirectorio

    $nombreDirectorioValido = Articulo::ComprobarLongitud($nombreArchivoDestino,260);
    if($nombreDirectorioValido == false) { $_SESSION['LongImagen']= true;}
    //print"nombre directorio valido =$nombreDirectorioValido";
}else{
    //no han subido imagen, necesitamos el nombre del archivo ya subido
    $articulo = Articulo::GetArticuloByCodigo($codigoOriginal);
    //si no seleccionan imagen, entonces quieren conservar la que tenían, recuperar de BBDD (ya se comprobó, así que no hay nada que comprobar)
    if($articulo !== false){
        if($_SESSION['codigo'] == null){
            $codigoOriginal= $articulo->getCodigo();
            $_SESSION['codigo'] = $codigoOriginal;
        }
        $imagen= $articulo->getImagen();
        $_SESSION['imagenReciclada'] = $imagen;
        $nombreArchivoDestino=$imagen;
        echo "<br>articuloValidar dice: imagen vale= ".$imagen;
    } else{
        $_SESSION['CodigoNotFound'] = true;
      //  print"<br>no se encontro el codigo <br>";
        echo "<script>history.back();</script>";
        exit;
    }
}


if(
    ( isset($_SESSION['LongNombre']) && $_SESSION['LongNombre'] == true) ||
    ( isset($_SESSION['BadCodigo']) && $_SESSION['BadCodigo'] == true ) ||
    ( isset($_SESSION['CodigoAlreadyExists']) && $_SESSION['CodigoAlreadyExists'] == true )||
    ( isset($_SESSION['LongDescripcion'] ) && $_SESSION['LongDescripcion'] == true ) ||
    ( isset($_SESSION['LongCategoria'] ) && $_SESSION['LongCategoria'] == true ) ||
    ( isset($_SESSION['LongPrecio'] ) && $_SESSION['LongPrecio'] == true ) ||
    ( isset($_SESSION['LongImagen']) && $_SESSION['LongImagen']== true ) ||
    ( isset($_SESSION['ImagenPesada']) && $_SESSION['ImagenPesada'] == true ) ||
    ( isset( $_SESSION['FileAlreadyExists']) && $_SESSION['FileAlreadyExists']== true ) ||
    ( isset( $_SESSION['ImagenGrande']) && $_SESSION['ImagenGrande']== true ) ||
    ( isset( $_SESSION['ActivoGrande']) && $_SESSION['ActivoGrande']== true ) ||
    ( isset( $_SESSION['CategoriaNoEsNumero']) && $_SESSION['CategoriaNoEsNumero']== true ) ||
    ( isset( $_SESSION['BadDescuento']) && $_SESSION['BadDescuento']== true ) ||
    ( isset( $_SESSION['FileBadFormat']) && $_SESSION['FileBadFormat']== true )
){
    //algo dio error, go back para que allí de donde venga se muestre el error
   // print"nombre ={$_SESSION['LongNombre']},BadCodigo ={$_SESSION['BadCodigo']},CodigoAlreadyExists ={$_SESSION['CodigoAlreadyExists']},LongDescripcion ={$_SESSION['LongDescripcion']},LongCategoria ={$_SESSION['LongCategoria']},LongPrecio ={$_SESSION['LongPrecio']},LongImagen ={$_SESSION['LongImagen']},ImagenPesada ={$_SESSION['ImagenPesada']},FileAlreadyExists ={$_SESSION['FileAlreadyExists']},ImagenGrande ={$_SESSION['ImagenGrande']},ActivoGrande ={$_SESSION['ActivoGrande']},FileBadFormat ={$_SESSION['FileBadFormat']},,BadDescuento ={$_SESSION['BadDescuento']},";

    //   echo "<script>history.back();</script>";
        exit;
} else {
    $_SESSION["nombre"] = $nombre;
    $_SESSION["descripcion"] = $descripcion;
    $_SESSION["categoria"] = $categoria;
    $_SESSION["precio"] = $precio;
    $_SESSION["descuento"] = $descuento;
    $_SESSION["activo"] = $activo;
}

if( isset($_SESSION["editandoArticulo"]) && $_SESSION["editandoArticulo"] == "true"){
    //all OK y estamos editando
    if ( isset($_FILES['imagen']) && $_FILES["imagen"]["size"] == 0){
        //estan reciclando la imagen
        $_SESSION['MoveDone']= true;
        $_SESSION["imagen"] = $nombreArchivoDestino; //guardamos el nombre relativo, estilo "hemerotecaBD\tacocat--a-cat-made-of-taco-body.png"
    }else{
        //estan subiendo una imagen nueva, hay que moverla
        if ( move_uploaded_file($_FILES['imagen']['tmp_name'], $directorio) ) { //esto es ruta absoluta porque lo guardamos en el pc
            $_SESSION['MoveDone']= true;
            $_SESSION["imagen"] = $nombreArchivoDestino; //guardamos el nombre relativo, estilo "hemerotecaBD\tacocat--a-cat-made-of-taco-body.png"
            echo "<br>la imagen se movió a $directorio";
        } else {
            $_SESSION['MoveFailed']= true;
            echo "<br>la imagen __NO__ se movió al directorio";
        }
    }

    $_SESSION["codigo"]=$codigoOriginal;

    //rescatamos de session los datos subidos por ValidarDatos
    $nombre = ( isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : null );
    $codigoOriginal = ( isset($_SESSION["codigo"]) ? $_SESSION["codigo"] : null );//por session llega el código ORIGINAL
    $descripcion = ( isset($_SESSION["descripcion"]) ? $_SESSION["descripcion"] : null );
    $categoria = ( isset($_SESSION["categoria"]) ? $_SESSION["categoria"] : null );
    $precio = ( isset($_SESSION["precio"]) ? $_SESSION["precio"] : null );
    $imagen = ( isset($_SESSION["imagen"]) ? $_SESSION["imagen"] : null ); //nueva imagen o la imagen vieja
    $imagenReciclada = ( isset($_SESSION["imagenReciclada"]) ? $_SESSION["imagenReciclada"] : null ); //nueva imagen
    $descuento = ( isset($_SESSION["descuento"]) ? $_SESSION["descuento"] : 0 );
    $activo = ( isset($_SESSION["activo"]) ? $_SESSION["activo"] : 1 );

    $codigo = ( isset($_GET["codigo"]) ? $_GET["codigo"] : null ); //por la URL llega el código NUEVO
    $operacionExitosa = $articulo->updateArticulo($nombre, $codigo, $codigoOriginal, $descripcion, $categoria, $precio, $imagen, $imagenReciclada, $descuento, $activo);
    if($operacionExitosa){
        $_SESSION['GoodUpdateArticulo']= true;
    }
    header("Location: ../Views/ArticulosLISTAR.php");
    exit;
}else if( isset($_SESSION["nuevoArticulo"]) && $_SESSION["nuevoArticulo"] == "true" && $codigoLibre == true){

    //all good y estamos añadiendo artículo nuevo
        if ( isset($_FILES['imagen']) && $_FILES["imagen"]["size"] !== 0 && move_uploaded_file($_FILES['imagen']['tmp_name'], $directorio) ) { //ruta absoluta lo guardamos en el pc
            $_SESSION['MoveDone']= true;
            $_SESSION["imagen"] = $nombreArchivoDestino; //guardamos el nombre relativo, estilo "hemerotecaBD\tacocat--a-cat-made-of-taco-body.png"
            echo "<br>la imagen se movió a $directorio";
        } else {
            $_SESSION['MoveFailed']= true;
            echo "<br>la imagen __NO__ se movió a $directorio";
        }

    $_SESSION["codigo"]=$codigo;
    $articulo=new Articulo($codigo, $nombre,$descripcion, $categoria, $precio, $nombreArchivoDestino, $descuento, $activo);
    //$operacionExitosa = Articulo::AltaArticulo($articulo);
    $operacionExitosa = Articulo::AltaArticulo($codigo, $nombre,$descripcion, $categoria, $precio, $nombreArchivoDestino, $descuento, $activo);
    if($operacionExitosa){
        $_SESSION['GoodInsertArticulo']= true;
        print"all good $operacionExitosa";
    } else{
        print"all bad $operacionExitosa";
    }
   header("Location: ../Views/ArticulosLISTAR.php");
    exit;
};

?>
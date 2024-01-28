<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");
$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    echo "ArticuloVALIDAR dice: no está user en session";
    header("Location: index.php");
}
include_once("conectarBD.php");
include_once("Articulo.php");
include_once("Directorio.php");
include_once("ValidaCodigoArticulo.php");
include_once("ValidaImagenes.php");
include_once("ValidaLongitudes.php");
print_r($_SESSION);

$nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : null;
$descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : null;//por post llegaría el posible nuevo código
$categoria = isset($_POST["categoria"]) ? $_POST["categoria"] : null;
$precio = isset($_POST["precio"]) ? $_POST["precio"] : null;
$codigo = isset($_POST["codigo"]) ? $_POST["codigo"] : null;
$codigoOriginal = isset($_SESSION["codigo"]) ? $_SESSION["codigo"] : null;

if($codigo == $codigoOriginal || $codigo == null ){//si el código escrito es el mismo //si no hay código en POST --> es que estaban editando y no lo quieren cambiar
    $mantenemosCodigo= true;
} else{
    $mantenemosCodigo= false;
}

$nombreValido = ComprobarLongitud($nombre,50);
if($nombreValido == false) {    $_SESSION['LongNombre']= true; }

$descripcionValida = ComprobarLongitud($descripcion,60);
if($descripcionValida == false) { $_SESSION['LongDescripcion']= true;}

$categoriaValido = ComprobarLongitud($categoria,30);
if($categoriaValido == false) { $_SESSION['LongCategoria']= true;}

$precioValido = ComprobarLongitud($precio,11);
if($precioValido == false) { $_SESSION['LongPrecio']= true;}

if( isset($_SESSION["editandoArticulo"]) && $_SESSION["editandoArticulo"] == "true" ){
    if($_SESSION['codigo'] !== null){
        //tanto si mantienen codigo como si lo cambian hay que comprobar que el original exista
        $codigoOriginal = $_SESSION["codigo"];
        $codigoOriginal =TransformarCodigo($codigoOriginal);
        $codigoOriginalLibre = CodigoLibre($codigoOriginal);
        if($codigoOriginalLibre == true) {  $_SESSION['CodigoDeberiaExistir'] = true;}
    }

    if( !$mantenemosCodigo ){
        //entonces hay codigo nuevo, validamos formato y que esté libre (el nuevo)
        $codigo = $_POST["codigo"];
            $codigo = TransformarCodigo($codigo);

            $codigoCorrecto = EsFormatoCodigoCorrecto($codigo);
            if($codigoCorrecto == false) {  $_SESSION['BadCodigo']= true;}

            $codigoLibre = CodigoLibre($codigo);
            if($codigoLibre == false) {  $_SESSION['CodigoAlreadyExists']= true;}
    }

}else if( isset($_SESSION["nuevoArticulo"]) && $_SESSION["nuevoArticulo"] == "true" ){

    if( isset($_POST['codigo']) ) {//codigo nuevo  articulo llega por POST, aqui codigo es obligatorio.
    $codigo = $_POST["codigo"];
    $codigo = TransformarCodigo($codigo);

    $codigoCorrecto = EsFormatoCodigoCorrecto($codigo);
    if($codigoCorrecto == false) {  $_SESSION['BadCodigo']= true;}

    $codigoLibre = CodigoLibre($codigo);
    if($codigoLibre == false) {  $_SESSION['CodigoAlreadyExists']= true;}
    }

};

if(isset($_FILES["imagen"]) && $_FILES["imagen"]["size"] !== 0){
//si han subido algún archivo entonces...
    $imagen = $_FILES["imagen"];
    echo"<br>____________si que hay una imagen:_________";
    $imagenValida=ValidaImagen();//comprobamos peso, tamaño y formato aquí, se sube a session los errores encontrados

    $nombreArchivo = $_FILES['imagen']['name'];//este es el nombre con el que se sube el archivo (como lo nombra el usuario)
    $directorio=PrepararDirectorio();
    $hemerotecaBD="hemerotecaBD/";
    $directorio= $directorio.$nombreArchivo;
    $nombreArchivoDestino = $hemerotecaBD . $nombreArchivo;//esto es lo que vamos a guardar en BBDD

    $nombreDirectorioValido = ComprobarLongitud($nombreArchivoDestino,260);
    if($nombreDirectorioValido == false) { $_SESSION['LongImagen']= true;}
}else{
    //no han subido imagen, necesitamos el nombre del archivo ya subido
    print_r($_FILES);
    if(isset($_FILES['imagen'])){
        //si suben una imagen con formato correcto pero el peso=0 hay que controlarlo.
        if($arrayInfoImagen == false) {
            $imagenValida=ValidaImagen();//comprobamos peso, tamaño y formato aquí, se sube a session los errores encontrados
            echo "<script>history.back();</script>";
            exit;
        }
    }

    if( $_FILES["imagen"]["size"] == 0 ){
        //si no seleccionan imagen, entonces quieren conservar la que tenían, recuperar de BBDD (ya se comprobó, así que no hay nada que comprobar)
        try{
            $conPDO=contectarBbddPDO();
            $verQuery=("select * from articulos WHERE codigo=:codigoOriginal");//puede que estén cambiando el codigo cuando no suben imagen, hay que mirar el original.
            $statement= $conPDO->prepare($verQuery);
            $statement->bindValue(':codigoOriginal', $codigoOriginal);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,'Articulo');
            $articulo=$statement->fetch();

            if($_SESSION['codigo'] == null){
                $codigoOriginal= $articulo->getCodigo();
                $_SESSION['codigo'] = $codigoOriginal;
            }

            $imagen= $articulo->getImagen();
            $_SESSION['imagenReciclada'] = $imagen;
            $nombreArchivoDestino=$imagen;
            echo "<br>articuloValidar dice: imagen vale= ".$imagen;
        }catch(PDOException $e) {
            $_SESSION["BadUpdateArticulo"]=true;
            header("Location: ArticuloEDITAR.php");
        };
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
    ( isset($_SESSION['ImagenPesada']) && $_SESSION['ImagenPesada'] == true ) ||//este error se genera en ValidarImagen
    ( isset( $_SESSION['FileAlreadyExists']) && $_SESSION['FileAlreadyExists']== true ) ||//este error se genera en ValidarImagen
    ( isset( $_SESSION['ImagenGrande']) && $_SESSION['ImagenGrande']== true ) ||//este error se genera en ValidarImagen
    ( isset( $_SESSION['FileBadFormat']) && $_SESSION['FileBadFormat']== true ) //este error se genera en ValidarImagen
){
    //algo dio error, go back para que allí de donde venga se muestre el error
       echo "<script>history.back();</script>";
        exit;
} else {
    $_SESSION["nombre"] = $nombre;
    $_SESSION["descripcion"] = $descripcion;
    $_SESSION["categoria"] = $categoria;
    $_SESSION["precio"] = $precio;
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
    header("location:UpdateArticulo.php?codigo=$codigo");//mandamos por url el código nuevo recibido por POST
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

    header("location:InsertarArticulo.php");
    exit;
};

?>
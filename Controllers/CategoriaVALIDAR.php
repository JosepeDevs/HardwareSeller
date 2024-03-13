<?php
if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
include_once("OperacionesSession.php");

checkAdminOEmpleado();

$usuarioLogeado = UserEstablecido();
if( $usuarioLogeado == false){
    session_destroy();
    print "CategoriaVALIDAR dice: no está user en session";
   header("Location: /index.php");
   exit;
}

include_once("../Models/Categoria.php");
////print_r($_SESSION);;

$codigo = isset($_POST["codigo"]) ? $_POST["codigo"] : null;
$codigoOriginal = isset($_SESSION["codigo"]) ? $_SESSION["codigo"] : null;
$nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : null;
$activo = isset($_POST["activo"]) ? $_POST["activo"] : null;
$codCategoriaPadre = isset($_POST["codCategoriaPadre"]) ? $_POST["codCategoriaPadre"] : null;

if($codigo == $codigoOriginal ){//si el código escrito es el mismo --> es que estaban editando y no lo quieren cambiar
    $mantenemosCodigo= true;
} else{
    $mantenemosCodigo= false;
}

$nombreValido = Categoria::ComprobarLongitud($nombre,50);
if($nombreValido == false) {    $_SESSION['LongNombre']= true; }

$codPadreValido = Categoria::ComprobarLongitud($codCategoriaPadre,50);
if($codPadreValido == false) {    $_SESSION['LongPadre']= true; }

$activoValido = Categoria::ComprobarLongitud($activo,11);
if($activoValido == false) { $_SESSION['LongActivo']= true;}

if( isset($_SESSION["editandoCategoria"]) && $_SESSION["editandoCategoria"] == "true" ){
    
    if( $mantenemosCodigo == false ){
        //entonces hay codigo nuevo, validamos formato y que esté libre (el nuevo codigo)
        $codigo = $_POST["codigo"];
        $codigoLibre = Categoria::CodigoLibre($codigo);
        if($codigoLibre == false) {  $_SESSION['CodigoAlreadyExists']= true;}
    } else{
        //entonces el codigo recibido es el mismo, no hacen falta comprobaciones de si esta libre o no (no lo estará porque lo están reutilizando)
    }

}else if( isset($_SESSION["nuevoCategoria"]) && $_SESSION["nuevoCategoria"] == "true" ){
    //el codigo que será el padre debe existir previamente como código de categoría
    $codPadreExiste = Categoria::CodigoLibre($codCategoriaPadre);
    //si no existe el código (está libre), entonces subir error a session, porque para hacer un codigo de categoria como codPadre, debe existir previamente el código
    if($codPadreExiste == true) { $_SESSION['codPadreNoExiste']= true; }

    //lo que sí debo comprobar es que el código que quieren ponerle no esté ya ocupado 
    if( isset($_POST['codigo']) ) {//codigo nuevo  Categoria llega por POST, aqui codigo es obligatorio.
    $codigo = $_POST["codigo"];
    $codigoLibre = Categoria::CodigoLibre($codigo);
    if($codigoLibre == false) {  $_SESSION['CodigoAlreadyExists']= true;}
    }

};



//////////////sección comprobación de errores
if(
    ( isset($_SESSION['LongNombre']) && $_SESSION['LongNombre'] == true) ||
    ( isset($_SESSION['LongPadre']) && $_SESSION['LongPadre'] == true) ||
    ( isset($_SESSION['BadCodigo']) && $_SESSION['BadCodigo'] == true ) ||
    ( isset($_SESSION['CodigoAlreadyExists']) && $_SESSION['CodigoAlreadyExists'] == true )||
    ( isset($_SESSION['CodigoDeberiaExistir']) && $_SESSION['CodigoDeberiaExistir'] == true )||
    ( isset( $_SESSION['ActivoGrande']) && $_SESSION['ActivoGrande']== true ) ||
    ( isset( $_SESSION['codPadreNoExiste']) && $_SESSION['codPadreNoExiste']== true )
){
    //algo dio error, go back para que allí de donde venga se muestre el error
    print "<script>history.back();</script>";
    exit;
} else {
    //no han habido errores
    $_SESSION["nombre"] = $nombre;
    $_SESSION["activo"] = $activo;
    $_SESSION["codCategoriaPadre"] = $codCategoriaPadre;
}

if( isset($_SESSION["editandoCategoria"]) && $_SESSION["editandoCategoria"] == "true"){
    //llegamos aquí si está todo OK y estamos editando
    $_SESSION["codigo"]=$codigoOriginal;

    //rescatamos de session los datos subidos por ValidarDatos
    $nombre = ( isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : null );
    $codigoOriginal = ( isset($_SESSION["codigo"]) ? $_SESSION["codigo"] : null );//por session llega el código ORIGINAL

    $activo = ( isset($_SESSION["activo"]) ? $_SESSION["activo"] : 1 );
    $codCategoriaPadre = ( isset($_SESSION["codCategoriaPadre"]) ? $_SESSION["codCategoriaPadre"] : null );//por session llega el código ORIGINAL

    $codigo = ( isset($_POST["codigo"]) ? $_POST["codigo"] : null ); //por la URL llega el código NUEVO

    $categoria = new Categoria();
    print($nombre. $codigo. $codigoOriginal. $activo. $codCategoriaPadre);
    $operacionExitosa = $categoria->updateCategoria($nombre, $codigo, $codigoOriginal, $activo, $codCategoriaPadre);
    if($operacionExitosa){
        $_SESSION['GoodUpdateCategoria']= true;
    }
    header("Location: ../Views/CategoriasLISTAR.php");
    exit;
}else if( isset($_SESSION["nuevoCategoria"]) && $_SESSION["nuevoCategoria"] == "true" && $codigoLibre == true){

    //all good y estamos añadiendo artículo nuevo

    $_SESSION["codigo"]=$codigo;
    $operacionExitosa = Categoria::AltaCategoria($nombre, $codigo, $codigoOriginal, $activo, $codCategoriaPadre);
    if($operacionExitosa){
        $_SESSION['GoodInsertCategoria']= true;
        print"all good $operacionExitosa";
    } else{
        print"all bad $operacionExitosa";
    }
   header("Location: ../Views/CategoriasLISTAR.php");
    exit;
};

?>
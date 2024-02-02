<?php
include_once("header.php");
?>
<h1>Resultados de la búsqueda</h1>
<br>
<h2>Todavía en construcción ¡Lo sentimos!</h2>
<br>

<?php
$textoBuscado = $_POST['textoBuscado'];
print("<br><h3>Ha buscado: $textoBuscado</h3><br>");
//esta no hace falta protegerla, aquí mostraremos artículos/posts que tengan coicindencia con el término buscado

//todo

print("<br><h3>Coincidencias encontradas en ARTÍCULOS:</h3><br>");
//ARTICULOS ENCONTRADOS QUE CONTIENEN EL TÉRMINO BUSCADO:
//función que expondrá contorler que llamara a artículos y que revisará todos sus miembros devolverá coincidencias (solo los que esten ACTIVOS)-->riesgo de que tarde mucho la búsqueda, poner algo de JS para que salga que está cargando o algo y sepan que no se ha colgado


print("<br><h3>Coincidencias encontradas en CATEGORÍAS:</h3><br>");
//CATEGORIAS ENCONTRADAS QUE CONTIENE EL TÉRMINO BUSCADO:
//función que expondrá categorias que revisará sus miembros para los articulos y devolverá coincidencias (solo los que estén ACTIVOS)-->riesgo de que tarde mucho la búsqueda, poner algo de JS para que salga que está cargando o algo y sepan que no se ha colgado


print("<br><h3>Coincidencias encontradas en POSTS:</h3><br>");
//POSTS ENCONTRADOS QUE CONTIENEN EL TÉRMINO BUSCADO:
//funcion que expone posts (por crear que buscará el texto especificado en los titulos y tags)
//montaremos con un foreach enlaces para ver la ficha de cada artículo (ArticuloEditar como user)

include_once("footer.php");
?>
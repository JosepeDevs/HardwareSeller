<?php

//NO PUEDO PROTEGERLO SIN ROMPER EL ACCESO, AUNQUE EN PRODUCCIÓN ESTO NO EXISTIRÍA.
//si pudiera usar SQLI en lugar de PDO podría proteger más.

/**
 * @return PDO devuelve conexión PDO con la base de datos
 */

function contectarBbddPDO(){

    if (!defined('USER_DB')) {
        define("USER_DB", "if0_35787488");//esto para probar que funciona, nunca usar root
    }
    if (!defined('PASSWORD')) {
        define("PASSWORD", "popolan92");
    }
    if (!defined('DBTABLA')) {
        define("DBTABLA", "mysql:host=sql107.infinityfree.com;dbname=if0_35787488_clientes_db");//DSN significa $DataSourceName
    }

    try {
        $con = new PDO(DBTABLA, USER_DB, PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    } catch(PDOException $e) {
        die("error en la conexión");
    };
}

?>
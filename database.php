<?php

ini_set("upload_max_filesize", "1024M");
ini_set("post_max_size", "1024M");
ini_set("max_execution_time", 3000);
ini_set("max_input_time", 3000);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require 'pagination.php';
require 'mysqlManager.php';

date_default_timezone_set('America/Mexico_City');

// *****************************
// Ingresar al servidor correspondiente
$servidor = $_SERVER['SERVER_NAME'];

if ($servidor == "www.jjcorp.com.mx" || $servidor == 'jjcorp.com.mx') {
    // Pruebas
    define('DB_PORT', '3306');
    define('DB_HOST', 'www.jjcorp.com.mx');
    define('DB_USER', 'juancho_presupuestos');
    define('DB_PASS', '!Fc1oNCE0L,l');
    define('DB_NAME', 'juancho_presupuestos');
} else {
    // Producción
    define('DB_PORT', '3306');
    define('DB_HOST', 'www.jjcorp.com.mx');
    define('DB_USER', 'juancho_presupuestos');
    define('DB_PASS', '!Fc1oNCE0L,l');
    define('DB_NAME', 'juancho_presupuestos');
}

// *****************************

class Enum {

    private $m_valueName = NULL;

    private function __construct($valueName) {
        $this->m_valueName = $valueName;
    }

    public static function __callStatic($methodName, $arguments) {
        $className = get_called_class();
        return new $className($methodName);
    }

    function __toString() {
        return $this->m_valueName;
    }

}

class SelectType extends Enum {

    const NONE = "NONE";
    const SELECT = "SELECT";
    const SELECT_WITH_PAGINATION = "SELECT_WITH_PAGINATION";

}

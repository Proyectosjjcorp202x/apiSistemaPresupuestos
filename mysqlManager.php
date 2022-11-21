<?php


/**
 * Description of MysqlManager
 *
 * @author jjcor
 */

class MysqlManager
{

    /**
     * @property resultsForPage: Resultados a mostrar por pagina
     */
    public $resultsForPage = 10;

    /**
     * @property currentPage Pagina a seleccionar al ejecutar la consulta (por default es 1, si no se pasa 
     * parametro)
     */
    public $currentPage = 1;

    /**
     * @property tableMoreCondition Nombre de la tabla a consultar + [(condicion) opcional]
     * Ejemplo: personal where nombre = 1 ... etc... incluso se puede poner los join
     * Ejemplo2: personal p inner join personal2 p2 on p2.id=p.id ... etc..   
     */
    public $tableMoreCondition = "";

    private $limit = "";
    private $pag;
    private $conn;

    /**
     * Conecta a la base de datos en base a la configuracion
     *@param DB_NAME Se establece el nombre de la base de datos (por default '' osea se conecta en base a la configuracion de conexion)
     */
    public function connect($DB_NAME = '')
    {
        // Create connection
        if (trim($DB_NAME) == '' || $DB_NAME == null) {
            $DB_NAME = DB_NAME;
        }

        $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, $DB_NAME, DB_PORT);

        // Check connection
        if (mysqli_connect_errno($connect)) {
            $Hora = date("d-m-Y H:i:s");                   
            $file = fopen("log_error_" . date("dmY") . ".txt", "a");
            fwrite($file,"[". $Hora . "] " .  "Connection failed: " . mysqli_connect_error() . PHP_EOL);            
            fclose($file);
            die("Connection failed: " . mysqli_connect_error());
        }
        mysqli_set_charset($connect, "utf8");
        return $connect;
    }

    /**
     * remueve caracteres especiales de la cadena
     * @param field Nombre del campo
     * @param DB_NAME Se establece el nombre de la base de datos (por default '' osea se conecta en base a la configuracion de conexion) 
     */
    public function real_escape_string($field, $DB = '')
    {
        $this->conn = $conn = $this->connect($DB);

        $str = mysqli_real_escape_string($conn, $field);
        $this->Close($conn);
        return $str;
    }

    /**
     * @param sql: comando a ejecutar
     * @param SELECT: Establece si la consulta se realizara con paginacion o sin paginacion, por default es NONE
     * 1.- Opcion NONE omite los resultados del resulset (aplicado para insert, update, delete,etc...
     * (osea todas las consultas que no sean select)).
     * 2.- Opcion SELECT ejecuta una consulta (select) sin paginacion
     * 3.- Opcion SELECT_WITH_PAGINATION ejecuta una consulta (select) con paginacion.
     * @param DB_NAME Se establece el nombre de la base de datos (por default '' osea se conecta en base a la configuracion de conexion)
     * @return array|boolean|string Retorna un arreglo con todos los registros obtenidos solamente si es select, de lo contrario retornara un booleano(o una cadena en case de error)     
     */
    public function Query($sql, $SELECT = SelectType::SELECT, $DB_NAME = '')
    {
        $cat = [];
        $this->conn = $conn = $this->connect($DB_NAME);
        switch ($SELECT) {
            case SelectType::SELECT:
                if ($result = mysqli_query($conn, $sql)) {
                    $f = 0;

                    $fields = [];

                    while ($info = mysqli_fetch_field($result)) {
                        $fields[$f] = $info->name;
                        $f++;
                    }
                    $f = 0;
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $field = [];
                        foreach ($fields as $_field) {
                            $field[$_field] = $row[$_field];
                        }
                        $cat[$f] = $field;
                        $f++;
                    }
                    $this->Close($conn);
                } else {
                    $Hora = date("d-m-Y H:i:s"); 
                    $cat = "Error: " + mysqli_error($conn);
                    $file = fopen("log_error_" . date("dmY") . ".txt", "a");
                    fwrite($file, "[". $Hora . "] " .$cat . PHP_EOL);
                    fclose($file);
                    $cat = false;
                    $this->Close($conn);
                }
                break;
            case SelectType::SELECT_WITH_PAGINATION:
                $pag = new Pagination();

                $mysql = new MysqlManager();
                $count = $mysql->Query("Select count(*) as cta from " . $this->tableMoreCondition, SelectType::SELECT, $DB_NAME);
                $limit = "limit " . $pag->setConfig($this->resultsForPage, intval($count[0]['cta']), $this->currentPage);
                $this->pag = $pag;

                if ($result = mysqli_query($conn, $sql . " " . $limit)) {
                    $f = 0;

                    $fields = [];

                    while ($info = mysqli_fetch_field($result)) {
                        $fields[$f] = $info->name;
                        $f++;
                    }
                    $f = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $field = [];
                        foreach ($fields as $_field) {
                            $field[$_field] = $row[$_field];
                        }
                        $cat[$f] = $field;
                        $f++;
                    }
                    $this->Close($conn);
                } else {
                    
                    $cat = "Error: " + mysqli_error($conn);
                    $file = fopen("log_error_" . date("dmY") . ".txt", "a");
                    fwrite($file, "[". $Hora . "] " .$cat . PHP_EOL);
                    fclose($file);
                    $cat = false;
                    $this->Close($conn);
                }
                $pag = null;
                $mysql = null;
                break;
            case SelectType::NONE:
                if ($result = mysqli_query($conn, $sql)) {
                    $cat = true;
                    $this->Close($conn);
                } else {
                    $Hora = date("d-m-Y H:i:s");
                    $cat = "Error: " . mysqli_error($conn);
                    $file = fopen("log_error_" . date("dmY") . ".txt", "a");
                    fwrite($file, "[". $Hora . "] " .$cat . PHP_EOL);
                    fclose($file);
                    $cat = false;
                    $this->Close($conn);
                }
                break;
        }

        $f = 0;
        return $cat;
    }

    /**
     * Coloca la paginacion
     * @param DB_NAME Se establece el nombre de la base de datos (por default '' osea se conecta en base a la configuracion de conexion)
     */
    public function setPagination($DB_NAME = '')
    {
        $mysql = new MysqlManager();
        $pag = new Pagination();

        $count = $mysql->Query("Select count(*) as cta from " . $this->tableMoreCondition, SelectType::SELECT, $DB_NAME);
        $this->limit = "limit " . $pag->setConfig($this->resultsForPage, intval($count[0]['cta']), $this->currentPage);
        $this->pag = $pag;
        $mysql = null;
        $pag = null;
    }

    /**
     * Obtiene la paginacion 
     * @return limit Retorna una cadena como esta por
     *  ejemplo: limit 0,9 
     */
    public function getPagination()
    {
        return $this->limit;
    }

    /**
     * Obtiene la instancia del paginador
     * @return Pagination instancia del paginador
     */
    public function getPaginator(): Pagination
    {
        return $this->pag;
    }

    /**
     * Ejecuta una query de la forma tradicional
     * @param sql consulta a ejecutar
     * @param DB_NANE Se establece el nombre de la base de datos (por default '' osea se conecta en base a la configuracion de conexion)
     */
    public function QueryAsNormal($sql, $DB_NAME = '')
    {
        $result = "";
        $this->conn = $conn = $this->connect($DB_NAME);
        if ($result = mysqli_query($conn, $sql)) {
            $result = $result;
        } else {
            $Hora = date("d-m-Y H:i:s");               
            $error = "Error: " . mysqli_error($conn);
            $file = fopen("log_" . date("dmY") . ".txt", "a");
            fwrite($file,"[". $Hora . "] " .  $error . PHP_EOL);
            fclose($file);
            $result = false;
        }
        return $result;
    }

    
    /**
     * Cierra la conexion
     * @param conn Conexion actual 
     */
    public function Close($conn)
    {
        mysqli_close($conn);
    }

    /**
     * Obtiene la conexion
     * @return mysqli objeto de conexion
     */
    public function getConnection()
    {
        return $this->conn;
    }
    
     /**
     * Genera un log con los datos de la cadena
     */
    public function Bitacora($cadena)
    {   
        $Hora = date("d-m-Y H:i:s");
        $file = fopen("log_" . date("dmY") . ".txt", "a");
        fwrite($file,"[". $Hora . "] " .  $cadena . PHP_EOL);
        fclose($file);
    }
}

<?php
//error_reporting(0);
require 'database.php';

// Sector de ejemplos de uso de la funcion Query

/* Ejemplo 1 uso de la funcion Query(Consulta con paginacion)
Nota: La funcion Query tiene un mecanismo interno que con solo pasarle los 3 parametros referentes a la 
paginacion y en el segundo parametro de la funcion pararle la instruccion siguiente 'SelectType::SELECT_WITH_PAGINATION' devuelve 
unicamente los resultados del rango solicitado
*/
echo "Este es el primer ejemplo<br>";
$mysql = new MysqlManager();

// Parametros necesarios para la paginacion
$mysql->resultsForPage = 2; //El valor se puede pasar por parametro
$mysql->currentPage = 1; //El valor se puede pasar por parametro
$mysql->tableMoreCondition = "presupuesto where idpresupuesto > 0"; //No se debe pasar por parametro
// ******************************************************************

//Ejecuta la consulta y retorna una matriz de registros listos para usarlos 
$res = $mysql->Query("select * from presupuesto where idpresupuesto > 0", SelectType::SELECT_WITH_PAGINATION);

echo "Total de registros: " . $mysql->getPaginator()->getNResults() . "<br>";
echo "Pagina actual: " . $mysql->getPaginator()->getCurrentPage() . "<br>";
echo "Resultados a mostrar por pagina: " . $mysql->getPaginator()->getResultsForPage() . "<br>";
echo "Total de paginas: " . $mysql->getPaginator()->getTotalPages() . "<br>";

//En este ejemplo se le pasan los resultados para un correcto despliegue de informacion del lado del front.
$res = [
    'resultsForPage' => $mysql->getPaginator()->getResultsForPage(),
    'currentPage' => $mysql->getPaginator()->getCurrentPage(),
    'totalPages' => $mysql->getPaginator()->getTotalPages(),
    'totalRecords' => $mysql->getPaginator()->getNResults(),
    'regs' => $res
];
echo json_encode($res);

/*
  Ejemplo 2 uso de la funcion Query(Consulta sin paginacion)
*/
echo "Este es el segundo ejemplo<br>";
$mysql1 = new MysqlManager();

//Ejecuta la consulta y retorna una matriz de registros listos para usarlos 
$res = $mysql1->Query("select * from presupuesto where idpresupuesto > 0");

echo "Total de registros: " . sizeof($res) . "<br>";
echo "Pagina actual: " . 0 . "<br>";
echo "Resultados a mostrar por pagina: " . sizeof($res) . "<br>";
echo "Total de paginas: " . 1 . "<br>";
//En este ejemplo se le pasan los resultados para un correcto despliegue de informacion del lado del front.
$res = [
    'resultsForPage' => sizeof($res),
    'currentPage' => 0,
    'totalPages' => 1,
    'totalRecords' => sizeof($res),
    'regs' => $res
];

echo json_encode($res);

/*
 Ejemplo 3 uso de la funcion Query(Insert)
 Nota: Tambien se puede usar esta misma forma para update, delete, etc... (Excepto Select obviamente)
*/
echo "Este es el tercer ejemplo<br>";
$mysql2 = new MysqlManager();

//Ejecuta la consulta y retorna un booleano pero en caso de error devuelbe una cadena.   
if ($res = $mysql2->Query("insert into estatus_presupuestos(estatus,Color) values('prueba de la funcion query','#------');", SelectType::NONE)) {
    echo "Insertado correctamente";
}

/****************************************************************************************/

//Sector de ejemplos de uso de la funcion QueryAsNormal

// Ejemplo 4 uso de la funcion QueryAsNormal(con paginacion)
echo "Este es el cuarto ejemplo<br>";
$mysql3 = new MysqlManager();

// Parametros necesarios para la paginacion
$mysql3->resultsForPage = 2; //El valor se puede pasar por parametro
$mysql3->currentPage = 1; //El valor se puede pasar por parametro
$mysql3->tableMoreCondition = "presupuesto where idpresupuesto > 0"; //No se debe pasar por parametro
// ******************************************************************

$mysql3->setPagination();

$result1 = $mysql3->QueryAsNormal("select * from presupuesto where idpresupuesto > 0 " . $mysql3->getPagination());

$cat = [];
$i = 0;
while ($row = mysqli_fetch_assoc($result1)) {
    $cat[$i]['idpresupuesto'] = $row['idpresupuesto'];
    $i++;
}
$mysql3->Close($mysql3->getConnection());

echo "Total de registros: " . $mysql3->getPaginator()->getNResults() . "<br>";
echo "Pagina actual: " . $mysql3->getPaginator()->getCurrentPage() . "<br>";
echo "Resultados a mostrar por pagina: " . $mysql3->getPaginator()->getResultsForPage() . "<br>";
echo "Total de paginas: " . $mysql3->getPaginator()->getTotalPages() . "<br>";

//En este ejemplo se le pasan los resultados para un correcto despliegue de informacion del lado del front.
$res = [
    'resultsForPage' => $mysql3->getPaginator()->getResultsForPage(),
    'currentPage' => $mysql3->getPaginator()->getCurrentPage(),
    'totalPages' => $mysql3->getPaginator()->getTotalPages(),
    'totalRecords' => $mysql3->getPaginator()->getNResults(),
    'regs' => $cat
];
echo json_encode($res);


// Ejemplo 5 uso de la funcion QueryAsNormal(sin paginacion)
echo "Este es el quinto ejemplo<br>";
$mysql4 = new MysqlManager();

$result1 = $mysql4->QueryAsNormal("select * from presupuesto where idpresupuesto > 0 ");

$cat = [];
$i = 0;
while ($row = mysqli_fetch_assoc($result1)) {
    $cat[$i]['idpresupuesto'] = $row['idpresupuesto'];
    $i++;
}
$mysql4->Close($mysql4->getConnection());
echo "Total de registros: " . sizeof($cat) . "<br>";
echo "Pagina actual: " . 0 . "<br>";
echo "Resultados a mostrar por pagina: " . sizeof($cat) . "<br>";
echo "Total de paginas: " . 1 . "<br>";
//En este ejemplo se le pasan los resultados para un correcto despliegue de informacion del lado del front.
$res = [
    'resultsForPage' => sizeof($cat),
    'currentPage' => 0,
    'totalPages' => 1,
    'totalRecords' => sizeof($cat),
    'regs' => $cat
];
echo json_encode($res);

/* Ejemplo 6 uso de la funcion QueryAsNormal(insert) 
*  Nota: De la misma forma se puede usar con update, delete, etc... (exepto select obviamente)
*/

echo "Este es el sexto ejemplo<br>";
$mysql5 = new MysqlManager();

if ($res = $mysql5->QueryAsNormal("insert into estatus_presupuestos(estatus,Color) values('prueba de la funcion query6','#-----6');")) {
    echo "Insertado correctamente";
}

$mysql5->Close($mysql5->getConnection());

/********************************************************************************/

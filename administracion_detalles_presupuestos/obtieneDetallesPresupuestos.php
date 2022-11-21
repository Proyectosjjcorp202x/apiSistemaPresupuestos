<?php

/**
 * Regresa lista de clientes.
 */
require '../database.php';

date_default_timezone_set('America/Mexico_City');

$condicion = "";
$cat = [];

// Get the posted data.

$postdata = json_encode([
    'page' => $_POST['page'],
    'resultsForPage' => (isset($_POST['resultsForPage'])) ? $_POST['resultsForPage'] : '',
    'cond' => $_POST['cond'],
    'modulo' => $_POST['modulo'],
    'idpresupuesto' => $_POST['idpresupuesto']
        ]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $mysql = new MysqlManager();

    // Sanitize.
    $cond = $mysql->real_escape_string((string) $request->cond);

    // ****************************
    // Armado de consulta
    $sqlConsulta = "SELECT dp.*,-1 as operacion,-1 as editar FROM vw_detalles_presupuesto dp "
            . "     WHERE dp.activo = 1 and dp.modulo = '" . $request->modulo . "' and dp.idpresupuesto = '" . $request->idpresupuesto . "' ";
    $sqlCond = " vw_detalles_presupuesto dp "
            . "     WHERE dp.activo = 1 and dp.modulo = '" . $request->modulo . "' and dp.idpresupuesto = '" . $request->idpresupuesto . "' ";
    if (strlen($cond) > 0) {
        $sqlConsulta .= " "
                . " AND trim(lower(dp.concepto)) LIKE trim(lower('" . $cond . "%'))";
        $sqlCond .= " AND trim(lower(dp.concepto)) LIKE trim(lower('" . $cond . "%'))";
    }

    $sqlConsulta .= " order by 1 desc ";
    $sqlCond .= " order by 1 desc ";
    // ****************************

    $Select_Type = SelectType::SELECT_WITH_PAGINATION;
    if (trim($request->resultsForPage) == '') {
        $Select_Type = SelectType::SELECT;
    }

    $mysql->resultsForPage = $request->resultsForPage;
    $mysql->currentPage = $request->page;
    $mysql->tableMoreCondition = $sqlCond;
    // SelectType::NONE se establece para cuando solo se require jacer una modificiacion insert,update, delete
    $detalles_presupuestos = $mysql->Query($sqlConsulta, $Select_Type);

    if ($Select_Type == SelectType::SELECT_WITH_PAGINATION) {
        $res = [
            'resultsForPage' => $mysql->getPaginator()->getResultsForPage(),
            'currentPage' => $mysql->getPaginator()->getCurrentPage(),
            'totalPages' => $mysql->getPaginator()->getTotalPages(),
            'totalRecords' => $mysql->getPaginator()->getNResults(),
            'sql' => $sqlConsulta,
            'regs' => $detalles_presupuestos
        ];
    } else {
        $res = [
            'regs' => $detalles_presupuestos
        ];
    }
    echo json_encode($res);

    /*
     * Ejemplo de uso del queryNormal
     * 
      $mysql->setPagination();

      if ($result = $mysql->QueryAsNormal($sqlConsulta." ". $mysql->getPagination())) {

      $cat = [];
      $i = 0;
      while ($row = mysql_fetch_assoc($result)) {
      $cat[]['idUsuario'] = $row["idUsuario"];
      $i++;
      }

      //cuando se usa paginacion
      $res = [
      'resultsForPage' => $mysql->getPaginator()->getResultsForPage(),
      'currentPage' => $mysql->getPaginator()->getCurrentPage() ,
      'totalPages' => $mysql->getPaginator()->getTotalPages(),
      'totalRecords' => $mysql->getPaginator()->getNResults(),
      'regs' => $cat
      ];

      //Cuando no se usa paginacion
      $res = [
      'resultsForPage' => sizeof($cat),
      'currentPage' => 1,
      'totalPages' => 1,
      'totalRecords' => sizeof($rcat),
      'regs' => $cat
      ];

      echo json_encode($res); */
}

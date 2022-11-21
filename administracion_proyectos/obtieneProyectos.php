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
    'cond' => $_POST['cond']
        ]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $mysql = new MysqlManager();

    // Sanitize.
    $cond = $mysql->real_escape_string((string) $request->cond);

    // ****************************
    // Armado de consulta
    $sqlConsulta = "SELECT p.* FROM vw_proyectos p "
            . "     WHERE p.activo = 1 ";
    $sqlCond = " vw_proyectos p "
            . "     WHERE p.activo = 1";
    if (strlen($cond) > 0) {
        $sqlConsulta .= " "
                . " AND trim(lower(p.proyecto)) LIKE trim(lower('" . $cond . "%'))";
        $sqlCond .= " AND trim(lower(p.proyecto)) LIKE trim(lower('" . $cond . "%'))";
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
    $proyectos = $mysql->Query($sqlConsulta, $Select_Type);

    if ($Select_Type == SelectType::SELECT_WITH_PAGINATION) {
        $res = [
            'resultsForPage' => $mysql->getPaginator()->getResultsForPage(),
            'currentPage' => $mysql->getPaginator()->getCurrentPage(),
            'totalPages' => $mysql->getPaginator()->getTotalPages(),
            'totalRecords' => $mysql->getPaginator()->getNResults(),
            'sql' => $sqlConsulta,
            'regs' => $proyectos
        ];
    } else {
        $res = [
            'regs' => $proyectos
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

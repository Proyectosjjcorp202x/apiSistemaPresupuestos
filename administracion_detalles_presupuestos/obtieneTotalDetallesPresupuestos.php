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
    'modulo' => $_POST['modulo'],
    'idpresupuesto' => $_POST['idpresupuesto']
        ]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $mysql = new MysqlManager();

    // ****************************
    // Armado de consulta
    $sqlConsulta = "SELECT count(*) as cta FROM vw_detalles_presupuesto dp "
            . "     WHERE dp.activo = 1 and dp.modulo = '" . $request->modulo . "' and dp.idpresupuesto = '" . $request->idpresupuesto . "' ";

    $sqlConsulta .= " order by 1 desc ";
    // ****************************
    // SelectType::NONE se establece para cuando solo se require jacer una modificiacion insert,update, delete
    $total_detalles_presupuestos = $mysql->Query($sqlConsulta, SelectType::SELECT);

    $res = [
    'cta' => $total_detalles_presupuestos[0]['cta']
    ];
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

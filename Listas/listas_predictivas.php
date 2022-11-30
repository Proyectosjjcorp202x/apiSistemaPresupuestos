<?php

/**
 * Regresa lista de clientes.
 */
require '../database.php';

date_default_timezone_set('America/Mexico_City');

$condicion = "";
$resultado = [];

// Get the posted data.
$postdata = json_encode([
    'catalogo' => filter_input(INPUT_POST, 'catalogo', FILTER_SANITIZE_SPECIAL_CHARS),
    'cond' => filter_input(INPUT_POST, 'cond', FILTER_SANITIZE_SPECIAL_CHARS)
        ]);

if (isset($postdata) && !empty($postdata)) {
// Extract the data.
    $request = json_decode($postdata);

    $mysql = new MysqlManager();

    // Sanitize.
    $catalogo = $request->catalogo;
    $condicion = $request->cond;
    $sqlCond = "";

    switch ($catalogo) {
        case "unidades":
            // Unidades
            $sqlConsulta = 'Select unidad valor from vw_unidades where trim(lower(unidad)) LIKE trim(lower(\'' . $condicion . '%\')) or trim(lower(abreviatura)) LIKE trim(lower(\'' . $condicion . '%\')) order by 1 asc ';
            break;
        case "rubros":
            // Rubros
            $sqlConsulta = 'Select rubro valor from vw_rubros where trim(lower(rubro)) LIKE trim(lower(\'' . $condicion . '%\')) order by 1 asc ';
            break;
        case "conceptos":
            // Conceptos
            $sqlConsulta = 'Select concepto valor,concepto label from vw_conceptos where trim(lower(concepto)) LIKE trim(lower(\'' . $condicion . '%\')) order by 1 asc ';
            break;
        case "conceptos_por_rubro":
            // Conceptos por rubro
            $sqlConsulta = 'Select concepto valor,concepto label from vw_conceptos where idrubro = (Select cr.idrubro from cat_rubro cr where trim(lower(cr.rubro)) = trim(lower(\'' . $condicion . '\'))) order by 1 asc ';
            break;
        case "costo_carga_social":
            $sqlConsulta = 'Select valor from vw_cargas_sociales where idcargasocial = \'' . $condicion . '\' order by 1 asc ';
            break;
    }

    // Consulta de datos
    if ($result = $mysql->QueryAsNormal($sqlConsulta)) {
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $resultado[$i]['valor'] = $row['valor'];
            $resultado[$i]['texto'] = $row['valor'];
            $i++;
        }
        echo json_encode(["regs" => $resultado]);
    }
}

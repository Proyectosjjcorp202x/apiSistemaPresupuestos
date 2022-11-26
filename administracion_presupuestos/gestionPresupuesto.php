<?php

/**
 * Regresa lista de clientes.
 */
require '../database.php';

date_default_timezone_set('America/Mexico_City');

$sql = '';

$idpresupuesto = '';
$folio = '';
$idcliente = '';
$idcat_o_div = '';
$proyecto = '';
$idplaza = '';
$periodo_nv = '';
$duracion_nv = '';
$idcargasocial = '';
$idestatus = '';
$fechainicial = '';
$fechafinal = '';
$fechaalta = '';
$objetivo = '';
$comision_agencia = '';
$uda = '';
$fda = '';
$operacion = '';

// Get the posted data.
$postdata = json_encode([
    'idpresupuesto' => $_POST['idpresupuesto'],
    'folio' => $_POST['folio'],
    'idcliente' => $_POST['idcliente'],
    'idcat_o_div' => $_POST['idcat_o_div'],
    'proyecto' => $_POST['proyecto'],
    'idplaza' => $_POST['idplaza'],
    'periodo_nv' => $_POST['periodo'],
    'duracion_nv' => $_POST['descripcion_duracion'],
    'idcargasocial' => $_POST['idcargasocial'],
    'idestatus' => $_POST['idestatus'],
    'fechainicial' => $_POST['fechainicial'],
    'fechafinal' => $_POST['fechafinal'],
    'fechaalta' => $_POST['fechaalta'],
    'objetivo' => $_POST['objetivo'],
    'comision_agencia' => $_POST['comision_agencia'],
    'uda' => $_POST['uda'],
    'fda' => $_POST['fda'],
    'operacion' => (isset($_POST['operacion'])) ? $_POST['operacion'] : '0'
        ]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $mysql = new MysqlManager();

    // Sanitize.
    $idpresupuesto = $mysql->real_escape_string((string) $request->idpresupuesto);
    $folio = $mysql->real_escape_string((string) $request->folio);
    $idcliente = $mysql->real_escape_string((string) $request->idcliente);
    $idcat_o_div = $mysql->real_escape_string((string) $request->idcat_o_div);
    $proyecto = $mysql->real_escape_string((string) $request->proyecto);
    $idplaza = $mysql->real_escape_string((string) $request->idplaza);
    $periodo_nv = $mysql->real_escape_string((string) $request->periodo_nv);
    $duracion_nv = $mysql->real_escape_string((string) $request->duracion_nv);
    $idcargasocial = $mysql->real_escape_string((string) $request->idcargasocial);
    $idestatus = $mysql->real_escape_string((string) $request->idestatus);
    $fechainicial = $mysql->real_escape_string((string) $request->fechainicial);
    $fechafinal = $mysql->real_escape_string((string) $request->fechafinal);
    $fechaalta = $mysql->real_escape_string((string) $request->fechaalta);
    $objetivo = $mysql->real_escape_string((string) $request->objetivo);
    $comision_agencia = $mysql->real_escape_string((string) $request->comision_agencia);
    $uda = $mysql->real_escape_string((string) $request->uda);
    $fda = $mysql->real_escape_string((string) $request->fda);
    $operacion = $mysql->real_escape_string((string) $request->operacion);

    //Si esta vacio o es nulo le asigna la palabra null
    $_idcat_o_div = ($idcat_o_div == '' || $idcat_o_div == null) ? 'null' : "'" . $idcat_o_div . "'";
    $_idcargasocial = ($idcargasocial == '' || $idcargasocial == null) ? 'null' : "'" . $idcargasocial . "'";

    // *************************
    // Inserción, actualización o modificacion
    $sql = "CALL `proc_inserta_actualiza_elimina_presupuestos`(
  '{$idpresupuesto}',-- <{idpresupuesto INT}>
  '{$folio}',-- <{folio VARCHAR(11)}>
  '{$idcliente}',-- <{idcliente INT}>
  {$_idcat_o_div},-- <{idcat_o_div INT}>
  '{$proyecto}',-- <{proyecto VARCHAR(200)}>
  '{$idplaza}',-- <{idplaza INT}>
  '{$periodo_nv}',-- <{periodo_nv VARCHAR(500)}>
  '{$duracion_nv}',-- <{duracion_nv VARCHAR(500)}>
  {$_idcargasocial},-- <{idcargasocial INT}>
  '{$idestatus}',-- <{idestatus INT}>
  '{$fechainicial}',-- <{fechainicial DATETIME}>
  '{$fechafinal}',-- <{fechafinal DATETIME}>
  '{$fechaalta}',-- <{fechaalta DATETIME}>
  '{$objetivo}',-- <{objetivo VARCHAR(5000)}>
  '{$comision_agencia}',-- <{comision_agencia DECIMAL(18,2)}>,
  '{$uda}',-- <{uda VARCHAR(200)}>
  '{$fda}',-- <{fda DATETIME}>
  '{$operacion}' -- <{poperacion INT}> 0: Inserta, 1: Modifica, 2: Elimina
  );";
    // *************************
    if ($result = $mysql->QueryAsNormal($sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $res['idRes'] = $row['idRes'];
            $res['Mensaje'] = $row['Mensaje'];
        }
    }

    //Cierra la conexion
    $mysql->Close($mysql->getConnection());

    echo json_encode($res);
} 

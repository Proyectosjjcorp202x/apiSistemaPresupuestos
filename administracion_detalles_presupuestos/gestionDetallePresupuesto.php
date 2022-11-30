<?php

/**
 * Regresa lista de clientes.
 */
require '../database.php';

header('Content-Type: application/json');

date_default_timezone_set('America/Mexico_City');

$sql = '';

$iddetallepresupuesto = '';
$idpresupuesto = '';
$cantidad = '';
$unidad = '';
$rubro = '';
$concepto = '';
$costo_unitario = '';
$diario_integrado = '';
$no_dias = '';
$costo_total = '';
$modulo = '';
$uda = '';
$fda = '';
$operacion = '';

// Get the posted data.
$postdata = json_encode([
    'detalles' => $_POST['detalles']
        ]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode(json_decode($postdata)->detalles);

    foreach ($request as $request) {
        $mysql = new MysqlManager();

        // Sanitize.
        $iddetallepresupuesto = $mysql->real_escape_string((string) $request->iddetallepresupuesto);
        $idpresupuesto = $mysql->real_escape_string((string) $request->idpresupuesto);
        $cantidad = $mysql->real_escape_string((string) $request->cantidad);
        $unidad = $mysql->real_escape_string((string) $request->unidad);
        $rubro = $mysql->real_escape_string((string) $request->rubro);
        $concepto = $mysql->real_escape_string((string) $request->concepto);
        $costo_unitario = str_replace(array('$', ','), array(''), $mysql->real_escape_string((string) $request->costo_unitario));
        $diario_integrado = str_replace(array('$', ','), array(''), $mysql->real_escape_string((string) $request->diario_integrado));
        $no_dias = $mysql->real_escape_string((string) $request->no_dias);
        $costo_total = str_replace(array('$', ','), array(''), $mysql->real_escape_string((string) $request->costo_total));
        $modulo = $mysql->real_escape_string((string) $request->modulo);
        $uda = $mysql->real_escape_string((string) $request->uda);
        $fda = $mysql->real_escape_string((string) $request->fda);
        $operacion = $mysql->real_escape_string((string) $request->operacion);

        if (trim($iddetallepresupuesto) != '') {
            $unidad = (trim($unidad) == '') ? 'null' : '\'' . $unidad . '\'';
            $diario_integrado = (trim($diario_integrado) == '') ? 'null' : '\'' . $diario_integrado . '\'';
            $no_dias = (trim($no_dias) == '') ? 'null' : '\'' . $no_dias . '\'';
        }

        // *************************
        // Inserción, actualización o modificacion 
        $sql = "CALL `proc_inserta_actualiza_elimina_detalles_presupuestos`(
                   '{$iddetallepresupuesto}',-- <{iddetallepresupuesto INT}>
                   '{$idpresupuesto}',-- <{idpresupuesto INT}>
                   '{$cantidad}',-- <{cantidad INT}>
                   {$unidad},-- <{unidad VARCHAR(245)}>
                   '{$rubro}',-- <{rubro VARCHAR(200)}>
                   '{$concepto}',-- <{concepto VARCHAR(300)}>
                   '{$costo_unitario}',-- <{costo_unitario DECIMAL}>
                   {$diario_integrado},-- <{diario_integrado DECIMAL}>
                   {$no_dias},-- <{no_dias DECIMAL}>
                   '{$costo_total}',-- <{costo_total DECIMAL}>
                   '{$modulo}',-- <{modulo ENUM(1)}>
                   '{$uda}',-- <{uda VARCHAR(200)}>
                   '{$fda}',-- <{fda TIMESTAMP}>
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
    }

    echo json_encode($res);
}

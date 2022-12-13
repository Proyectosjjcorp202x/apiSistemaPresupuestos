<?php

/**
 * Regresa lista de clientes.
 */
require '../database.php';

date_default_timezone_set('America/Mexico_City');

$idcliente = '';

// Get the posted data.

$postdata = json_encode([
    'idcliente' => $_POST['idcliente']
        ]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $mysql = new MysqlManager();

    $idcliente = $mysql->real_escape_string((string) $request->idcliente);

    $sql = "select fn_foliopresupuesto('{$request->idcliente}') as folio,fn_cargasocial('{$request->idcliente}') as cargasocial;";

    $folio = $mysql->Query($sql, SelectType::SELECT);

    if (sizeof($folio) > 0) {

        //*******************Inserta carga social si no existe*****************************************/
        $mysql = new MysqlManager();

        $sql = "CALL Proc_InsertaCargaSocialCuandoNoExiste('" . $folio[0]['cargasocial'] . "');";

        $res = $mysql->Query($sql, SelectType::NONE);

        //*********************************************************************************************/


        echo json_encode(['folio' => $folio[0]['folio'], 'cargasocial' => $folio[0]['cargasocial']]);
    } else {
        echo json_encode(['folio' => '', 'cargasocial' => '']);
    }
}
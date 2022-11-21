<?php

/**
 * Regresa lista de clientes.
 */
require '../database.php';

date_default_timezone_set('America/Mexico_City');

$sql = '';
$idcargasocial = '';
$descripcion = '';
$valor = '';
$uda = '';
$fda = '';
$operacion = '';

// Get the posted data.
$postdata = json_encode([
    'idcargasocial' => $_POST['idcargasocial'],
    'descripcion' => $_POST['descripcion'],
    'valor' => $_POST['valor'],
    'uda' => $_POST['uda'],
    'fda' => $_POST['fda'],
    'operacion' => (isset($_POST['operacion'])) ? $_POST['operacion'] : '0'
        ]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $mysql = new MysqlManager();

    // Sanitize.
    $idcargasocial = $mysql->real_escape_string((string) $request->idcargasocial);
    $descripcion = $mysql->real_escape_string((string) $request->descripcion);
    $valor = $mysql->real_escape_string((string) $request->valor);
    $uda = $mysql->real_escape_string((string) $request->uda);
    $fda = $mysql->real_escape_string((string) $request->fda);
    $operacion = $mysql->real_escape_string((string) $request->operacion);

    // *************************
    // Inserción, actualización o modificacion 
    $sql = "CALL `proc_inserta_actualiza_elimina_carga_social`(
                    '{$idcargasocial}',
                    '{$descripcion}',
                    '{$valor}',
		    '{$uda}',
		    '{$fda}',
		    '{$operacion}'
                   )";
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

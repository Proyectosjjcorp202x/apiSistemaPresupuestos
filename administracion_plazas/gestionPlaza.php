<?php

/**
 * Regresa lista de clientes.
 */
require '../database.php';

date_default_timezone_set('America/Mexico_City');

$sql = '';

$idplaza = '';
$clave_depto = '';
$descripcion = '';
$uda = '';
$fda = '';
$operacion = '';

// Get the posted data.
$postdata = json_encode([
    'idplaza' => $_POST['idplaza'],
    'clave_depto' => $_POST['clave_depto'],
    'descripcion' => $_POST['descripcion'],
    'uda' => $_POST['uda'],
    'fda' => $_POST['fda'],
    'operacion' => (isset($_POST['operacion'])) ? $_POST['operacion'] : '0'
        ]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $mysql = new MysqlManager();

    // Sanitize.
    $idplaza = $mysql->real_escape_string((string) $request->idplaza);
    $clave_depto = $mysql->real_escape_string((string) $request->clave_depto);
    $descripcion = $mysql->real_escape_string((string) $request->descripcion);
    $uda = $mysql->real_escape_string((string) $request->uda);
    $fda = $mysql->real_escape_string((string) $request->fda);
    $operacion = $mysql->real_escape_string((string) $request->operacion);

    // *************************
    // Inserción, actualización o modificacion 

    $sql = "CALL `proc_inserta_actualiza_elimina_plazas`(
                    '{$idplaza}',
                    '{$clave_depto}',
                    '{$descripcion}',
                    '{$uda}',
		    '{$fda}',
		    '{$operacion}' 
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

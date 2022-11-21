<?php

/**
 * Regresa lista de clientes.
 */
require '../database.php';

date_default_timezone_set('America/Mexico_City');

$sql = '';

$idcliente = '';
$nombre = '';
$idcat_o_div = '';
$idplaza = '';
$idperiodo = '';
$idduracion = '';
$idproveedor = '';
$carga = '';
$comicionaf = '';
$comicionav = '';
$rfc = '';
$correo = '';
$telefono = '';
$uda = '';
$fda = '';
$operacion = '';

// Get the posted data.
$postdata = json_encode([
    'idcliente' => $_POST['idcliente'],
    'nombre' => $_POST['nombre'],
    'idcat_o_div' => $_POST['idcat_o_div'],
    'idplaza' => $_POST['idplaza'],
    'idperiodo' => $_POST['idperiodo'],
    'idduracion' => $_POST['idduracion'],
    'idproveedor' => $_POST['idproveedor'],
    'carga' => $_POST['carga'],
    'comicionaf' => $_POST['comicionaf'],
    'comicionav' => $_POST['comicionav'],
    'rfc' => $_POST['rfc'],
    'correo' => $_POST['correo'],
    'telefono' => $_POST['telefono'],
    'uda' => $_POST['uda'],
    'fda' => $_POST['fda'],
    'operacion' => (isset($_POST['operacion'])) ? $_POST['operacion'] : '0'
        ]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $mysql = new MysqlManager();

    // Sanitize.
    $idcliente = $mysql->real_escape_string((string) $request->idcliente);
    $nombre = $mysql->real_escape_string((string) $request->nombre);
    $idcat_o_div = $mysql->real_escape_string((string) $request->idcat_o_div);
    $idplaza = $mysql->real_escape_string((string) $request->idplaza);
    $idperiodo = $mysql->real_escape_string((string) $request->idperiodo);
    $idduracion = $mysql->real_escape_string((string) $request->idduracion);
    $idproveedor = $mysql->real_escape_string((string) $request->idproveedor);
    $carga = $mysql->real_escape_string((string) $request->carga);
    $comicionaf = $mysql->real_escape_string((string) $request->comicionaf);
    $comicionav = $mysql->real_escape_string((string) $request->comicionav);
    $rfc = $mysql->real_escape_string((string) $request->rfc);
    $correo = $mysql->real_escape_string((string) $request->correo);
    $telefono = $mysql->real_escape_string((string) $request->telefono);
    $uda = $mysql->real_escape_string((string) $request->uda);
    $fda = $mysql->real_escape_string((string) $request->fda);
    $operacion = $mysql->real_escape_string((string) $request->operacion);

    // *************************
    // Inserción, actualización o modificacion 
    $sql = "CALL `proc_inserta_actualiza_elimina_clientes`(
                    '{$idcliente}',
                    '{$nombre}',
                    '{$idcat_o_div}',
                    '{$idplaza}',
                    '{$idperiodo}',
                    '{$idduracion}',
                    '{$idproveedor}',
                    '{$carga}',
                    '{$comicionaf}',
                    '{$comicionav}',
                    '{$rfc}',
                    '{$correo}',
                    '{$telefono}',
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

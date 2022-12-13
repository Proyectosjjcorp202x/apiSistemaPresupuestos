<?php

/**
 * Regresa lista de clientes.
 */
require '../database.php';

date_default_timezone_set('America/Mexico_City');

$sql = '';

$idcliente = '';
$nombre = '';
$cat_o_div_nv = '';
$idplaza = '';
$idperiodo = '';
$idduracion = '';
$proveedor_nv = '';
$carga = '';
$comicionaf = '';
$comicionav = '';
$rfc = '';
$correo = '';
$telefono = '';
$contacto = '';
$correo1 = '';
$contacto2 = '';
$correo2 = '';
$telefono1 = '';
$administracion = '';
$reporteo_telefonia = '';
$jefe_plan = '';
$uda = '';
$fda = '';
$operacion = '';

// Get the posted data.
$postdata = json_encode([
    'idcliente' => $_POST['idcliente'],
    'nombre' => $_POST['nombre'],
    'cat_o_div_nv' => $_POST['cat_o_div_nv'],
    'idplaza' => $_POST['idplaza'],
    'idperiodo' => $_POST['idperiodo'],
    'idduracion' => $_POST['idduracion'],
    'proveedor_nv' => $_POST['proveedor_nv'],
    'carga' => $_POST['carga'],
    'comicionaf' => $_POST['comicionaf'],
    'comicionav' => $_POST['comicionav'],
    'rfc' => $_POST['rfc'],
    'correo' => $_POST['correo'],
    'telefono' => $_POST['telefono'],
    'contacto' => $_POST['contacto'],
    'correo1' => $_POST['correo1'],
    'contacto2' => $_POST['contacto2'],
    'correo2' => $_POST['correo2'],
    'telefono1' => $_POST['telefono1'],
    'administracion' => $_POST['administracion'],
    'reporteo_telefonia' => $_POST['reporteo_telefonia'],
    'jefe_plan' => $_POST['jefe_plan'],
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
    $cat_o_div_nv = $mysql->real_escape_string((string) $request->cat_o_div_nv);
    $idplaza = $mysql->real_escape_string((string) $request->idplaza);
    $idperiodo = $mysql->real_escape_string((string) $request->idperiodo);
    $idduracion = $mysql->real_escape_string((string) $request->idduracion);
    $proveedor_nv = $mysql->real_escape_string((string) $request->proveedor_nv);
    $carga = $mysql->real_escape_string((string) $request->carga);
    $comicionaf = $mysql->real_escape_string((string) $request->comicionaf);
    $comicionav = $mysql->real_escape_string((string) $request->comicionav);
    $rfc = $mysql->real_escape_string((string) $request->rfc);
    $correo = $mysql->real_escape_string((string) $request->correo);
    $telefono = $mysql->real_escape_string((string) $request->telefono);
    $contacto = $mysql->real_escape_string((string) $request->contacto);
    $correo1 = $mysql->real_escape_string((string) $request->correo1);
    $contacto2 = $mysql->real_escape_string((string) $request->contacto2);
    $correo2 = $mysql->real_escape_string((string) $request->correo2);
    $telefono1 = $mysql->real_escape_string((string) $request->telefono1);
    $administracion = str_replace(array('$', ','), array(''), $mysql->real_escape_string((string) $request->administracion));
    $reporteo_telefonia = str_replace(array('$', ','), array(''), $mysql->real_escape_string((string) $request->reporteo_telefonia));
    $jefe_plan = str_replace(array('$', ','), array(''), $mysql->real_escape_string((string) $request->jefe_plan));
    $uda = $mysql->real_escape_string((string) $request->uda);
    $fda = $mysql->real_escape_string((string) $request->fda);
    $operacion = $mysql->real_escape_string((string) $request->operacion);

    // *************************
    // Inserción, actualización o modificacion 
    $sql = "CALL `proc_inserta_actualiza_elimina_clientes`(
                    '{$idcliente}',
                    '{$nombre}',
                    '{$cat_o_div_nv}',
                    '{$idplaza}',
                    '{$idperiodo}',
                    '{$idduracion}',
                    '{$proveedor_nv}',
                    '{$carga}',
                    '{$comicionaf}',
                    '{$comicionav}',
                    '{$rfc}',
                    '{$correo}',
                    '{$telefono}',
                    '{$contacto}',
                    '{$correo1}',
                    '{$contacto2}',
                    '{$correo2}',
                    '{$telefono1}',
                    '{$administracion}',
                    '{$reporteo_telefonia}',
                    '{$jefe_plan}',    
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

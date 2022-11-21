<?php

/**
 * Regresa lista de clientes.
 */
require '../../database.php';

date_default_timezone_set('America/Mexico_City');

$sql = '';

$idusuario = '';
$usuario = '';
$clave = '';
$idperfil = '';
$idasignacion_idcategoria = '';
$idcategoria = '';
$nombrecompleto = '';
$correo = '';
$uda = '';
$fda = '';
$operacion = '';

// Get the posted data.
$postdata = json_encode
([
    'usuario' => $_POST['usuario'],
    'clave' => $_POST['clave'],
    'idperfil' => $_POST['idperfil'],
    'idasignacion_idcategoria' => (isset($_POST['idasignacion_idcategoria'])) ? $_POST['idasignacion_idcategoria'] : '',
	'idcategoria' => $_POST['idcategoria'],
    'nombrecompleto' => $_POST['nombrecompleto'],
	'correo' => $_POST['correo'],
    'uda' => $_POST['uda'],
    'fda' => $_POST['fda'],
    'idusuario' => (intval($_POST['idReg']) == 0) ? '' : $_POST['idReg'],
	'operacion' => (isset($_POST['operacion'])) ? $_POST['operacion'] : '1,2'	
]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $mysql = new MysqlManager();

    // Sanitize.
    $usuario = $mysql->real_escape_string((string) $request->usuario);
    $clave = $mysql->real_escape_string((string) $request->clave);
    $idperfil = $mysql->real_escape_string((string) $request->idperfil);
    $idasignacion_idcategoria = $mysql->real_escape_string((string) $request->idasignacion_idcategoria);
	$idcategoria = $mysql->real_escape_string((string) $request->idcategoria);
    $nombrecompleto = $mysql->real_escape_string((string) $request->nombrecompleto);
    $correo = $mysql->real_escape_string((string) $request->correo);
    $uda = $mysql->real_escape_string((string) $request->uda);
    $fda = $mysql->real_escape_string((string) $request->fda);
    $idusuario = $mysql->real_escape_string((string) $request->idusuario);	
    $operacion = $mysql->real_escape_string((string) $request->operacion);

    // *************************
    // Inserción o actualización
    if ($operacion == '1,2') {
        if ($idusuario == '') {
            $sql = "CALL proc_inserta_actualiza_elimina_usuarios(
                '" . $usuario . "',                                               /*usuario*/
                '" . $clave . "',                                                 /*clave*/
                '" . $idperfil . "',                                              /*idperfil*/
				'" . $idasignacion . "',                                          /*idasignacion*/
				'" . $idcategoria . "',                                           /*idcategoria*/
				'" . $nombrecompleto . "',                                        /*nombrecompleto*/
				'" . $correo . "',                                                /*correo*/								
                '" . $uda . "',                                                   /*uda*/
                '" . $fda . "',                                             	  /*fda*/
                -1,                                                               /*idusuario*/
                1                       /*ope: (inserta: 1, modifica: 2, elimina: 3)*/
                 );";
        } else {
            $sql = "CALL proc_inserta_actualiza_elimina_usuarios(
                '" . $usuario . "',                                               /*usuario*/
                '" . $clave . "',                                                 /*clave*/
                '" . $idperfil . "',                                              /*idperfil*/
				'" . $idasignacion . "',                                          /*idasignacion*/
				'" . $idcategoria . "',                                           /*idcategoria*/
				'" . $nombrecompleto . "',                                        /*nombrecompleto*/
				'" . $correo . "',                                                /*correo*/								
                '" . $uda . "',                                                   /*uda*/
                CURRENT_TIMESTAMP,                                                /*fda*/
                '" . $idusuario . "',                                             /*idusuario*/
                2                       /*ope: (inserta: 1, modifica: 2, elimina: 3)*/
                );";
        }
    } else {
        $sql = "CALL proc_inserta_actualiza_elimina_usuarios(
                '" . $usuario . "',                                               /*usuario*/
                '" . $clave . "',                                                 /*clave*/
                '" . $idperfil . "',                                              /*idperfil*/
				'" . $idasignacion . "',                                          /*idasignacion*/
				'" . $idcategoria . "',                                           /*idcategoria*/
				'" . $nombrecompleto . "',                                        /*nombrecompleto*/
				'" . $correo . "',                                                /*correo*/								
                '" . $uda . "',                                                   /*uda*/
                CURRENT_TIMESTAMP,                                                /*fda*/
                '" . $idusuario . "',                                             /*idusuario*/
	            3                       /*ope: (inserta: 1, modifica: 2, elimina: 3)*/
                );";
    }
    // *************************

    $mysql->query($sql, function ($sql, $result, $idasignacion_idcategoria) {

        while ($row = mysqli_fetch_assoc($result)) {
            $res['idres'] = $row['idRes'];
            $res['mensaje'] = $row['Mensaje'];
            $res['idusuario'] = $row['idusuario'];
        }

        if ($idasignacion_idcategoria != "null") {
            if ($idasignacion_idcategoria != '') {
                $r_cat = explode(';', $idasignacion_idcategoria);
                foreach ($r_cat as $c) {
                    $c_cat =    explode(':', $c);

                    $sql =  "CALL proc_asigna_categoria_a_usuario('" . $res['idusuario'] . "',              -- idusuario --
                    '" . $c_cat[0] . "',                  -- idasignacion --
                      '" . $c_cat[1] . "'            -- idcategoria --
                             );";
                    $mysql = new MysqlManager();
                    $mysql->query($sql, function ($sql, $result) {
                    }, null, '');
                }
            }
        }

        echo json_encode($res);
    }, null, '', $idasignacion_idcategoria);
}

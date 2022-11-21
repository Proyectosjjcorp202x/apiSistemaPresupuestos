<?php

/**
 * Inserta, Actualiza y/o Elimina Usuarios.
 */
require '../../database.php';

date_default_timezone_set('America/Mexico_City');

$sql = '';

$idusuario = '';
$nombre = '';
$usuario = '';
$apaterno = '';
$amaterno = '';
$correo = '';
$idperfil = '';
$clave = '';
$uda = '';
$fda = '';
$operacion = '';

// Get the posted data.
$postdata = json_encode([
     
    'idusuario' => filter_input(INPUT_POST, 'idusuario', FILTER_SANITIZE_SPECIAL_CHARS),
    'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS),
    'usuario' => filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_SPECIAL_CHARS),
    'apaterno' => filter_input(INPUT_POST, 'apaterno', FILTER_SANITIZE_SPECIAL_CHARS),
    'amaterno' => filter_input(INPUT_POST, 'amaterno', FILTER_SANITIZE_SPECIAL_CHARS),
    'correo' => filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_SPECIAL_CHARS),
    'idperfil' => filter_input(INPUT_POST, 'idperfil', FILTER_SANITIZE_SPECIAL_CHARS),
    'clave' => filter_input(INPUT_POST, 'clave', FILTER_SANITIZE_SPECIAL_CHARS),
    'uda' =>  filter_input(INPUT_POST, 'uda', FILTER_SANITIZE_SPECIAL_CHARS),
    'fda' => filter_input(INPUT_POST, 'fda', FILTER_SANITIZE_SPECIAL_CHARS),
    'operacion' => (isset($_POST['operacion'])) ? filter_input(INPUT_POST, 'operacion', FILTER_SANITIZE_SPECIAL_CHARS) : '0'
        ]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $mysql = new MysqlManager();

    // Sanitize.
    $idusuario = $mysql->real_escape_string((string) $request->idusuario);
    $nombre = $mysql->real_escape_string((string) $request->nombre);
    $usuario = $mysql->real_escape_string((string) $request->usuario);
    $apaterno = $mysql->real_escape_string((string) $request->apaterno);
    $amaterno = $mysql->real_escape_string((string) $request->amaterno);
    $correo = $mysql->real_escape_string((string) $request->correo);
    $idperfil = $mysql->real_escape_string((string) $request->idperfil);
    $clave = $mysql->real_escape_string((string) $request->clave);
    $uda = $mysql->real_escape_string((string) $request->uda);
    $fda = $mysql->real_escape_string((string) $request->fda);
    $operacion = $mysql->real_escape_string((string) $request->operacion);

    // *************************
    // Inserción, actualización o modificacion 
    $sql = "CALL `proc_inserta_actualiza_elimina_usuarios`(
                    '{$idusuario}',
                    '{$usuario}',
		    '{$nombre}',
                    '{$apaterno}',
		    '{$amaterno}',
                    '{$correo}',
                    '{$idperfil}',
		    '{$clave}',
		    '{$uda}',
		    '{$fda}',
		    '{$operacion}' -- <{poperacion INT}> 0: Inserta, 1: Modifica, 2: Elimina
                   );";
                    
    $mysql->Bitacora($sql);                
    // *************************
    // Obtiene los resultados
    $result = $mysql->QueryAsNormal($sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $res['idRes'] = $row['idRes'];
            $res['Mensaje'] = $row['Mensaje'];
        }
    }
    // *************************

    //Cierra la conexion
    $mysql->Close($mysql->getConnection());

    echo json_encode($res);
}

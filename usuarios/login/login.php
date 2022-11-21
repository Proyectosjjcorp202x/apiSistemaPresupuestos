<?php

require '../../database.php'; // Si funciono colocando database.php y pagination.php en la ruta /

date_default_timezone_set('America/Mexico_City');

$mysql = new MysqlManager();
//$postdata = file_get_contents("php://input"); //Para formData no funciona esta linea debido a la sintaxis
// incorrecta en el retorno de datos.

$postdata = json_encode([
    'usuario' => filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_SPECIAL_CHARS),
      'clave' => filter_input(INPUT_POST, 'clave', FILTER_SANITIZE_SPECIAL_CHARS)
        ]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $sql = " SELECT idusuario, usuario, nombre, apaterno, amaterno, correo, idperfil,perfil, clave, uda, fda, activo "
            . " FROM vw_usuarios u "
            . " WHERE trim(lower(u.usuario)) = trim(lower('" . $request->usuario . "')) "
            . " AND trim(lower(u.clave)) = trim(lower('" . $request->clave . "')) "
            . " AND u.activo = 1;";
    
    $mysql->Bitacora("*****************");
    $mysql->Bitacora($sql);

    $usr = $mysql->Query($sql, SelectType::SELECT);

   // echo json_encode($usr);
    if (sizeof($usr) > 0) 
    {
      echo json_encode($usr[0]);        
    } 
    else 
    {
      $usr['idusuario'] = 0;
      $usr['usuario'] = '';
      $usr['idperfil'] = 0;
      $usr['idperfil'] = '0';
      $usr['activo'] = 0;
      $usr['activo'] = "Usuario no Permitido";
      echo json_encode($usr);      
    }
    $mysql->Bitacora(" Fin de proceso");

    //Para usar con la forma tradicional
    /*    if ($result = $mysql->QueryAsNormal($sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $usr['idusuario'] = $row['idusuario'];
            $usr['usuario'] = $row['usuario'];
            $usr['idperfil'] = $row['idperfil'];
            $usr['idperfil'] = $row['idperfil'];
            $usr['activo'] = $row['activo'];
            $usr['activo'] = "Acceso Permitido";
        }
        if (sizeof($usr) == 0) {
            $usr['idusuario'] = 0;
            $usr['usuario'] = '';
            $usr['idperfil'] = 0;
            $usr['idperfil'] = '0';
            $usr['activo'] = 0;
            $usr['activo'] = "Usuario no Permitido";
        }
    }*/

}
?>

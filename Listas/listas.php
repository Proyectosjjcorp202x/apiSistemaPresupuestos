<?php

/**
 * Regresa lista de clientes.
 */
require '../database.php';

date_default_timezone_set('America/Mexico_City');

$condicion = "";
$resultado = [];

// Get the posted data.
$postdata = json_encode([
    'catalogo' => filter_input(INPUT_POST, 'catalogo', FILTER_SANITIZE_SPECIAL_CHARS),
        ]);

if (isset($postdata) && !empty($postdata)) {
// Extract the data.
    $request = json_decode($postdata);

    $mysql = new MysqlManager();

    // Sanitize.
    $catalogo = $request->catalogo;
    $sqlCond = "";

    switch ($catalogo) {
        case "perfiles":
            // Perfiles
            $sqlConsulta = 'Select idperfil id, perfil valor from perfiles order by 1 asc ';
            break;
        case "cat_o_div":
            // Categorias o divisiones
            $sqlConsulta = 'Select idcat_o_div id,cat_o_div valor from vw_catodiv order by 1 asc ';
            break;
        case "plazas":
            // Plazas
            $sqlConsulta = 'Select idplaza id,concat(clave_depto,\' - \',descripcion) valor from vw_plazas order by 1 asc ';
            break;
        case "cargas_sociales":
            // Cargas sociales
            $sqlConsulta = 'Select idcargasocial id,descripcion valor from vw_cargas_sociales order by 1 asc ';
            break;
        case "periodos":
            // Periodos
            $sqlConsulta = 'Select idperiodo id, periodo valor from vw_periodos order by 1 asc ';
            break;
        case "duraciones":
            // Duraciones
            $sqlConsulta = 'Select idduracion id, duracion valor from vw_duraciones order by 1 asc ';
            break;
        case "proveedores":
            // Proveedores
            $sqlConsulta = 'Select idproveedor id,nombre valor from vw_proveedores order by 1 asc ';
            break;
        case "rubros":
            // Rubros
            $sqlConsulta = 'Select idrubro id,rubro valor from vw_rubros order by 1 asc ';
            break;
        case "unidades":
            // Unidades
            $sqlConsulta = 'Select idunidad id,unidad valor from cat_unidades order by 1 asc ';
            break;
        case "clientes":
            $sqlConsulta = 'Select idcliente id,nombre valor from vw_clientes order by 1 asc ';
            break;
        case "estatus_presupuestos":
            $sqlConsulta = 'Select idestatuspresupuestos id,estatus valor from estatus_presupuestos order by 1 asc ';
            break;
        case "lista_colores":
            $sqlConsulta = 'Select estatus,Color from estatus_presupuestos order by 1 asc ';
            break;
    }

    // Consulta de datos
    if ($result = $mysql->QueryAsNormal($sqlConsulta)) {

        if (trim(strtolower($catalogo)) != "lista_colores") {
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $resultado[$i]['id'] = $row['id'];
                $resultado[$i]['valor'] = $row['valor'];
                $i++;
            }
        } else {
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $resultado[$i]['estatus'] = $row['estatus'];
                $resultado[$i]['Color'] = $row['Color'];
                $i++;
            }
        }
        echo json_encode(["regs" => $resultado]);
    }
}

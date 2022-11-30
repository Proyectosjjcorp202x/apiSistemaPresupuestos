<?php

require './ObtieneInformacionAExportar.php';
require './../PHPExcel/Classes/PHPExcel.php';

if (!isset($_POST['idpresupuesto'])) {
    return;
}

$postdata = json_encode([
    'idpresupuesto' => $_POST['idpresupuesto']]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $archivo = 'archivos-excel/leidos/reporte_creacion_presupuesto.xlsx';

    $extencion = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

    $encabezado_personal = [];

    $encabezado_degustacion_hasta_servicios = [];

    $obtieneInfo = new ObtieneInformacionAExportar();

    $obtieneInfo->ObtenerInformacion($request->idpresupuesto);

    $inputFileType = PHPExcel_IOFactory::identify($archivo);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($archivo);
    $sheet = $objPHPExcel->getSheet(1);

//Crea un nuevo archivo excel
    $archivo_generado = str_replace(array('.' . $extencion, 'leidos'), array('', 'generados'), $archivo) . '_' . str_replace(array('{', '}'), array(''), uniqid()) . '.' . $extencion;
    $objPHPExceln = new PHPExcel();

    $sheetn = $objPHPExceln->getSheet(0);

//Coloca nombre de la hoja
    $sheetn->setTitle('Presupuesto');

// Copia los nombres de los campos de creacion de presupuesto (Columna A de la A2 a la A9)
    for ($i = 2; $i <= 8; $i++) {
        colocarValor($sheetn, $sheet, $i, 'A', $i, 'A');
        copiarEstilos($sheetn, $sheet, $i, 'A', $i, 'A');
    }
// Extrae informacion de creacion de presupuesto (Columna B)
    colocarValor($sheetn, null, 2, 'B', null, null, date('Y-m-d H:i:s'));
    colocarValor($sheetn, null, 3, 'B', null, null, $obtieneInfo->No_Ppto);
    colocarValor($sheetn, null, 4, 'B', null, null, $obtieneInfo->Cliente);
    colocarValor($sheetn, null, 5, 'B', null, null, $obtieneInfo->Proyecto);
    colocarValor($sheetn, null, 6, 'B', null, null, $obtieneInfo->Plazas);
    colocarValor($sheetn, null, 7, 'B', null, null, $obtieneInfo->Periodos);
    colocarValor($sheetn, null, 8, 'B', null, null, $obtieneInfo->Duracion);
    //colocarValor($sheetn, null, 9, 'B', null, null, $obtieneInfo->Objetivo);
// Copia el formato de las celdas de la B2 a la B9 que son las celdas con la informacion de creacion de presupuesto
    for ($i = 2; $i <= 8; $i++) {
        copiarEstilos($sheetn, $sheet, $i, 'B', $i, 'B');
    }

    $renglon = 11;

    if (sizeof($obtieneInfo->personal) > 0) {

        // Ajusta el titulo de la tabla de personal

        $renglon += 2;
        colocarValor($sheetn, null, $renglon, 'A', null, null, 'Personal');
        copiarEstilos($sheetn, $sheet, $renglon, 'A', 11, 'A');

        $renglon += 1;
        // Ajusta los encabezados de la tabla del rubro personal
        for ($i = 65; $i <= 70; $i++) {
            colocarValor($sheetn, $sheet, $renglon, chr($i), 12, chr($i));
            copiarEstilos($sheetn, $sheet, $renglon, chr($i), 12, chr($i));
            colocarBordes($sheetn, $renglon, chr($i));
        }

        // Asigna la información del rubro de personal
        $renglon += 1;
        foreach ($obtieneInfo->personal as $personal) {
            colocarValor($sheetn, null, $renglon, 'A', null, null, $personal['numero_de_personal']);
            colocarValor($sheetn, null, $renglon, 'B', null, null, $personal['descripcion']);
            colocarValor($sheetn, null, $renglon, 'C', null, null, $personal['costo_unitario_pdv']);
            colocarValor($sheetn, null, $renglon, 'D', null, null, $personal['costo_cuota_diaria_fiscal']);
            colocarValor($sheetn, null, $renglon, 'E', null, null, $personal['numero_de_dias']);
            colocarValor($sheetn, null, $renglon, 'F', null, null, $personal['total']);
            copiarEstilos($sheetn, $sheet, $renglon, 'A', 13, 'A');
            copiarEstilos($sheetn, $sheet, $renglon, 'B', 13, 'B');
            copiarEstilos($sheetn, $sheet, $renglon, 'C', 13, 'C');
            copiarEstilos($sheetn, $sheet, $renglon, 'D', 13, 'D');
            copiarEstilos($sheetn, $sheet, $renglon, 'E', 13, 'E');
            copiarEstilos($sheetn, $sheet, $renglon, 'F', 13, 'F');
            colocarBordes($sheetn, $renglon, 'A', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'B', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'C', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'D', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'E', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'F', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            $renglon++;
        }

        // Ajusta doble border en la parte de abajo de la ultima fila insertada
        for ($i = 65; $i <= 70; $i++) {
            colocarBordes($sheetn, $renglon - 1, chr($i), PHPExcel_Style_Border::BORDER_DOUBLE, 'bottom');
        }

        // Asigna el subtotal del rubro de personal
        colocarValor($sheetn, $sheet, $renglon, 'E', 31, 'E');
        copiarEstilos($sheetn, $sheet, $renglon, 'E', 31, 'E');
        colocarValor($sheetn, null, $renglon, 'F', null, null, $obtieneInfo->subtotal_personal);
        copiarEstilos($sheetn, $sheet, $renglon, 'F', 15, 'F');
    }

    if (sizeof($obtieneInfo->degustacion) > 0) {
        // Ajusta el titulo de la tabla de degustacion

        $renglon += 3;
        colocarValor($sheetn, null, $renglon, 'A', null, null, 'Degustacion');
        copiarEstilos($sheetn, $sheet, $renglon, 'A', 28, 'A');

        $renglon += 1;
        // Ajusta los encabezados de la tabla del rubro degustacion
        for ($i = 65; $i <= 70; $i++) {
            colocarValor($sheetn, $sheet, $renglon, chr($i), 29, chr($i));
            copiarEstilos($sheetn, $sheet, $renglon, chr($i), 29, chr($i));
            colocarBordes($sheetn, $renglon, chr($i));
        }

        // Asigna la informacion del rubro de degustacion
        $renglon += 1;
        foreach ($obtieneInfo->degustacion as $degustacion) {
            colocarValor($sheetn, null, $renglon, 'A', null, null, $degustacion['numero']);
            colocarValor($sheetn, null, $renglon, 'B', null, null, $degustacion['descripcion']);
            colocarValor($sheetn, null, $renglon, 'C', null, null, $degustacion['costo_unitario']);
            colocarValor($sheetn, null, $renglon, 'E', null, null, $degustacion['numero_de_dias']);
            colocarValor($sheetn, null, $renglon, 'F', null, null, $degustacion['total']);
            copiarEstilos($sheetn, $sheet, $renglon, 'A', 13, 'A');
            copiarEstilos($sheetn, $sheet, $renglon, 'B', 13, 'B');
            copiarEstilos($sheetn, $sheet, $renglon, 'C', 13, 'C');
            copiarEstilos($sheetn, $sheet, $renglon, 'D', 13, 'D');
            copiarEstilos($sheetn, $sheet, $renglon, 'E', 13, 'E');
            copiarEstilos($sheetn, $sheet, $renglon, 'F', 13, 'F');
            colocarBordes($sheetn, $renglon, 'A', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'B', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'C', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'D', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'E', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'F', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            $renglon++;
        }

        // Ajusta doble border en la parte de abajo de la ultima fila insertada
        for ($i = 65; $i <= 70; $i++) {
            colocarBordes($sheetn, $renglon - 1, chr($i), PHPExcel_Style_Border::BORDER_DOUBLE, 'bottom');
        }

        // Asigna el subtotal del rubro de degustacion
        colocarValor($sheetn, $sheet, $renglon, 'E', 31, 'E');
        copiarEstilos($sheetn, $sheet, $renglon, 'E', 31, 'E');
        colocarValor($sheetn, null, $renglon, 'F', null, null, $obtieneInfo->subtotal_degustacion);
        copiarEstilos($sheetn, $sheet, $renglon, 'F', 31, 'F');
    }

    if (sizeof($obtieneInfo->viaticos) > 0) {
        // Ajusta el titulo de la tabla de viaticos

        $renglon += 3;
        colocarValor($sheetn, null, $renglon, 'A', null, null, 'Viaticos');
        copiarEstilos($sheetn, $sheet, $renglon, 'A', 28, 'A');

        $renglon += 1;
        // Ajusta los encabezados de la tabla del rubro viaticos
        for ($i = 65; $i <= 70; $i++) {
            colocarValor($sheetn, $sheet, $renglon, chr($i), 29, chr($i));
            copiarEstilos($sheetn, $sheet, $renglon, chr($i), 29, chr($i));
            colocarBordes($sheetn, $renglon, chr($i));
        }

        // Asigna la información del rubro de viaticos
        $renglon += 1;
        foreach ($obtieneInfo->viaticos as $viaticos) {
            colocarValor($sheetn, null, $renglon, 'A', null, null, $viaticos['numero']);
            colocarValor($sheetn, null, $renglon, 'B', null, null, $viaticos['descripcion']);
            colocarValor($sheetn, null, $renglon, 'C', null, null, $viaticos['costo_unitario']);
            colocarValor($sheetn, null, $renglon, 'E', null, null, $viaticos['numero_de_dias']);
            colocarValor($sheetn, null, $renglon, 'F', null, null, $viaticos['total']);
            copiarEstilos($sheetn, $sheet, $renglon, 'A', 13, 'A');
            copiarEstilos($sheetn, $sheet, $renglon, 'B', 13, 'B');
            copiarEstilos($sheetn, $sheet, $renglon, 'C', 13, 'C');
            copiarEstilos($sheetn, $sheet, $renglon, 'D', 13, 'D');
            copiarEstilos($sheetn, $sheet, $renglon, 'E', 13, 'E');
            copiarEstilos($sheetn, $sheet, $renglon, 'F', 13, 'F');
            colocarBordes($sheetn, $renglon, 'A', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'B', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'C', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'D', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'E', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'F', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            $renglon++;
        }

        // Ajusta doble border en la parte de abajo de la ultima fila insertada
        for ($i = 65; $i <= 70; $i++) {
            colocarBordes($sheetn, $renglon - 1, chr($i), PHPExcel_Style_Border::BORDER_DOUBLE, 'bottom');
        }

        // Asigna el subtotal del rubro de viaticos
        colocarValor($sheetn, $sheet, $renglon, 'E', 31, 'E');
        copiarEstilos($sheetn, $sheet, $renglon, 'E', 31, 'E');
        colocarValor($sheetn, null, $renglon, 'F', null, null, $obtieneInfo->subtotal_viaticos);
        copiarEstilos($sheetn, $sheet, $renglon, 'F', 31, 'F');
    }

    if (sizeof($obtieneInfo->uniformes) > 0) {
        // Ajusta el titulo de la tabla de uniformes

        $renglon += 3;
        colocarValor($sheetn, null, $renglon, 'A', null, null, 'Uniformes');
        copiarEstilos($sheetn, $sheet, $renglon, 'A', 28, 'A');

        $renglon += 1;
        // Ajusta los encabezados de la tabla del rubro uniformes
        for ($i = 65; $i <= 70; $i++) {
            colocarValor($sheetn, $sheet, $renglon, chr($i), 29, chr($i));
            copiarEstilos($sheetn, $sheet, $renglon, chr($i), 29, chr($i));
            colocarBordes($sheetn, $renglon, chr($i));
        }

        // Asigna la información del rubro de uniformes
        $renglon += 1;
        foreach ($obtieneInfo->uniformes as $uniformes) {
            colocarValor($sheetn, null, $renglon, 'A', null, null, $uniformes['numero']);
            colocarValor($sheetn, null, $renglon, 'B', null, null, $uniformes['descripcion']);
            colocarValor($sheetn, null, $renglon, 'C', null, null, $uniformes['costo_unitario']);
            colocarValor($sheetn, null, $renglon, 'E', null, null, $uniformes['numero_de_dias']);
            colocarValor($sheetn, null, $renglon, 'F', null, null, $uniformes['total']);
            copiarEstilos($sheetn, $sheet, $renglon, 'A', 13, 'A');
            copiarEstilos($sheetn, $sheet, $renglon, 'B', 13, 'B');
            copiarEstilos($sheetn, $sheet, $renglon, 'C', 13, 'C');
            copiarEstilos($sheetn, $sheet, $renglon, 'D', 13, 'D');
            copiarEstilos($sheetn, $sheet, $renglon, 'E', 13, 'E');
            copiarEstilos($sheetn, $sheet, $renglon, 'F', 13, 'F');
            colocarBordes($sheetn, $renglon, 'A', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'B', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'C', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'D', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'E', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'F', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            $renglon++;
        }

        // Ajusta doble border en la parte de abajo de la ultima fila insertada
        for ($i = 65; $i <= 70; $i++) {
            colocarBordes($sheetn, $renglon - 1, chr($i), PHPExcel_Style_Border::BORDER_DOUBLE, 'bottom');
        }

        // Asigna el subtotal del rubro de uniformes
        colocarValor($sheetn, $sheet, $renglon, 'E', 31, 'E');
        copiarEstilos($sheetn, $sheet, $renglon, 'E', 31, 'E');
        colocarValor($sheetn, null, $renglon, 'F', null, null, $obtieneInfo->subtotal_uniformes);
        copiarEstilos($sheetn, $sheet, $renglon, 'F', 31, 'F');
    }

    if (sizeof($obtieneInfo->seguridad_y_trabajo) > 0) {
        // Ajusta el titulo de la tabla de seguridad y trabajo

        $renglon += 3;
        colocarValor($sheetn, null, $renglon, 'A', null, null, 'Seguridad y trabajo');
        copiarEstilos($sheetn, $sheet, $renglon, 'A', 28, 'A');

        $renglon += 1;
        // Ajusta los encabezados de la tabla del rubro seguridad y trabajo
        for ($i = 65; $i <= 70; $i++) {
            colocarValor($sheetn, $sheet, $renglon, chr($i), 29, chr($i));
            copiarEstilos($sheetn, $sheet, $renglon, chr($i), 29, chr($i));
            colocarBordes($sheetn, $renglon, chr($i));
        }

        // Asigna la información del rubro de seguridad y trabajo
        $renglon += 1;
        foreach ($obtieneInfo->seguridad_y_trabajo as $seguridad_y_trabajo) {
            colocarValor($sheetn, null, $renglon, 'A', null, null, $seguridad_y_trabajo['numero']);
            colocarValor($sheetn, null, $renglon, 'B', null, null, $seguridad_y_trabajo['descripcion']);
            colocarValor($sheetn, null, $renglon, 'C', null, null, $seguridad_y_trabajo['costo_unitario']);
            colocarValor($sheetn, null, $renglon, 'E', null, null, $seguridad_y_trabajo['numero_de_dias']);
            colocarValor($sheetn, null, $renglon, 'F', null, null, $seguridad_y_trabajo['total']);
            copiarEstilos($sheetn, $sheet, $renglon, 'A', 13, 'A');
            copiarEstilos($sheetn, $sheet, $renglon, 'B', 13, 'B');
            copiarEstilos($sheetn, $sheet, $renglon, 'C', 13, 'C');
            copiarEstilos($sheetn, $sheet, $renglon, 'D', 13, 'D');
            copiarEstilos($sheetn, $sheet, $renglon, 'E', 13, 'E');
            copiarEstilos($sheetn, $sheet, $renglon, 'F', 13, 'F');
            colocarBordes($sheetn, $renglon, 'A', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'B', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'C', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'D', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'E', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'F', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            $renglon++;
        }

        // Ajusta doble border en la parte de abajo de la ultima fila insertada
        for ($i = 65; $i <= 70; $i++) {
            colocarBordes($sheetn, $renglon - 1, chr($i), PHPExcel_Style_Border::BORDER_DOUBLE, 'bottom');
        }

        // Asigna el subtotal del rubro de seguridad y trabajo
        colocarValor($sheetn, $sheet, $renglon, 'E', 31, 'E');
        copiarEstilos($sheetn, $sheet, $renglon, 'E', 31, 'E');
        colocarValor($sheetn, null, $renglon, 'F', null, null, $obtieneInfo->subtotal_seguridad_y_trabajo);
        copiarEstilos($sheetn, $sheet, $renglon, 'F', 31, 'F');
    }

    if (sizeof($obtieneInfo->materiales_POP) > 0) {
        // Ajusta el titulo de la tabla de materiales POP

        $renglon += 3;
        colocarValor($sheetn, null, $renglon, 'A', null, null, 'Materiales POP');
        copiarEstilos($sheetn, $sheet, $renglon, 'A', 28, 'A');

        $renglon += 1;
        // Ajusta los encabezados de la tabla del rubro materiales POP
        for ($i = 65; $i <= 70; $i++) {
            colocarValor($sheetn, $sheet, $renglon, chr($i), 29, chr($i));
            copiarEstilos($sheetn, $sheet, $renglon, chr($i), 29, chr($i));
            colocarBordes($sheetn, $renglon, chr($i));
        }

        // Asigna la información del rubro de materiales POP
        $renglon += 1;
        foreach ($obtieneInfo->materiales_POP as $materialesPOP) {
            colocarValor($sheetn, null, $renglon, 'A', null, null, $materialesPOP['numero']);
            colocarValor($sheetn, null, $renglon, 'B', null, null, $materialesPOP['descripcion']);
            colocarValor($sheetn, null, $renglon, 'C', null, null, $materialesPOP['costo_unitario']);
            colocarValor($sheetn, null, $renglon, 'E', null, null, $materialesPOP['numero_de_dias']);
            colocarValor($sheetn, null, $renglon, 'F', null, null, $materialesPOP['total']);
            copiarEstilos($sheetn, $sheet, $renglon, 'A', 13, 'A');
            copiarEstilos($sheetn, $sheet, $renglon, 'B', 13, 'B');
            copiarEstilos($sheetn, $sheet, $renglon, 'C', 13, 'C');
            copiarEstilos($sheetn, $sheet, $renglon, 'D', 13, 'D');
            copiarEstilos($sheetn, $sheet, $renglon, 'E', 13, 'E');
            copiarEstilos($sheetn, $sheet, $renglon, 'F', 13, 'F');
            colocarBordes($sheetn, $renglon, 'A', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'B', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'C', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'D', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'E', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'F', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            $renglon++;
        }

        // Ajusta doble border en la parte de abajo de la ultima fila insertada
        for ($i = 65; $i <= 70; $i++) {
            colocarBordes($sheetn, $renglon - 1, chr($i), PHPExcel_Style_Border::BORDER_DOUBLE, 'bottom');
        }

        // Asigna el subtotal del rubro de materiales POP
        colocarValor($sheetn, $sheet, $renglon, 'E', 31, 'E');
        copiarEstilos($sheetn, $sheet, $renglon, 'E', 31, 'E');
        colocarValor($sheetn, null, $renglon, 'F', null, null, $obtieneInfo->subtotal_materiales_POP);
        copiarEstilos($sheetn, $sheet, $renglon, 'F', 31, 'F');
    }

    if (sizeof($obtieneInfo->servicios) > 0) {
        // Ajusta el titulo de la tabla de servicios

        $renglon += 3;
        colocarValor($sheetn, null, $renglon, 'A', null, null, 'Servicios');
        copiarEstilos($sheetn, $sheet, $renglon, 'A', 28, 'A');

        $renglon += 1;
        // Ajusta los encabezados de la tabla del rubro servicios
        for ($i = 65; $i <= 70; $i++) {
            colocarValor($sheetn, $sheet, $renglon, chr($i), 29, chr($i));
            copiarEstilos($sheetn, $sheet, $renglon, chr($i), 29, chr($i));
            colocarBordes($sheetn, $renglon, chr($i));
        }

        // Asigna la información del rubro de servicios    
        $renglon += 1;
        foreach ($obtieneInfo->servicios as $servicios) {
            colocarValor($sheetn, null, $renglon, 'A', null, null, $servicios['numero']);
            colocarValor($sheetn, null, $renglon, 'B', null, null, $servicios['descripcion']);
            colocarValor($sheetn, null, $renglon, 'C', null, null, $servicios['costo_unitario']);
            colocarValor($sheetn, null, $renglon, 'E', null, null, $servicios['numero_de_dias']);
            colocarValor($sheetn, null, $renglon, 'F', null, null, $servicios['total']);
            copiarEstilos($sheetn, $sheet, $renglon, 'A', 13, 'A');
            copiarEstilos($sheetn, $sheet, $renglon, 'B', 13, 'B');
            copiarEstilos($sheetn, $sheet, $renglon, 'C', 13, 'C');
            copiarEstilos($sheetn, $sheet, $renglon, 'D', 13, 'D');
            copiarEstilos($sheetn, $sheet, $renglon, 'E', 13, 'E');
            copiarEstilos($sheetn, $sheet, $renglon, 'F', 13, 'F');
            colocarBordes($sheetn, $renglon, 'A', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'B', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'C', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'D', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'E', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            colocarBordes($sheetn, $renglon, 'F', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
            $renglon++;
        }

        // Ajusta doble border en la parte de abajo de la ultima fila insertada
        for ($i = 65; $i <= 70; $i++) {
            colocarBordes($sheetn, $renglon - 1, chr($i), PHPExcel_Style_Border::BORDER_DOUBLE, 'bottom');
        }

        // Asigna el subtotal del rubro de servicios
        colocarValor($sheetn, $sheet, $renglon, 'E', 31, 'E');
        copiarEstilos($sheetn, $sheet, $renglon, 'E', 31, 'E');
        colocarValor($sheetn, null, $renglon, 'F', null, null, $obtieneInfo->subtotal_servicios);
        copiarEstilos($sheetn, $sheet, $renglon, 'F', 31, 'F');
    }

    $renglon += 3;

// Asigna el titulo: GRAN TOTAL ANTES DE I.V.A
    colocarValor($sheetn, $sheet, $renglon, 'E', 44, 'E');
    copiarEstilos($sheetn, $sheet, $renglon, 'E', 44, 'E');

    $renglon += 2;

// Asigna el nombre de los campos de los calculos de subtotales e I.V.A de las celdas: E46 a la E48
    for ($i = 46; $i <= 48; $i++) {
        colocarValor($sheetn, $sheet, $renglon, 'E', $i, 'E');
        copiarEstilos($sheetn, $sheet, $renglon, 'E', $i, 'E');
        $renglon++;
    }

// Retorna la variable $renglon a la posicion asignada en la linea 325 de este codigo
    $renglon -= 3;

// Asigna subtotal general
    colocarValor($sheetn, null, $renglon, 'F', null, null, $obtieneInfo->subtotal_general);
    copiarEstilos($sheetn, $sheet, $renglon, 'F', 46, 'F');
    colocarBordes($sheetn, $renglon, 'D', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
    colocarBordes($sheetn, $renglon, 'E', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
    colocarBordes($sheetn, $renglon, 'F', PHPExcel_Style_Border::BORDER_DOUBLE, 'bottom');

// Asigna comision de agencia servicio
    $renglon += 1;
    colocarValor($sheetn, null, $renglon, 'F', null, null, $obtieneInfo->comision_agencia_servicio);
    copiarEstilos($sheetn, $sheet, $renglon, 'F', 47, 'F');
    colocarBordes($sheetn, $renglon, 'D', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
    colocarBordes($sheetn, $renglon, 'E', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
    colocarBordes($sheetn, $renglon, 'F', PHPExcel_Style_Border::BORDER_DOUBLE, 'bottom');

// Asigna subtotal antes de I.V.A
    $renglon += 1;
    colocarValor($sheetn, null, $renglon, 'F', null, null, $obtieneInfo->subtotal_antes_de_iva);
    copiarEstilos($sheetn, $sheet, $renglon, 'F', 48, 'F');
    colocarBordes($sheetn, $renglon, 'D', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
    colocarBordes($sheetn, $renglon, 'E', PHPExcel_Style_Border::BORDER_THIN, 'bottom');
    colocarBordes($sheetn, $renglon, 'F', PHPExcel_Style_Border::BORDER_DOUBLE, 'bottom');

    ob_start();
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExceln, 'Excel2007');
    $objWriter->save('php://output');
    $xlsData = ob_get_contents();
    ob_end_clean();

    // Obtiene solo el nombre corto del archivo
    $a = explode('/', $archivo_generado);
    $nombre_corto = $a[sizeof($a) - 1];

    $response = array(
        'status' => TRUE,
        'url' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData),
        'nombre_archivo' => $nombre_corto
    );

    die(json_encode($response));

    // echo json_encode(['url' => $archivo_generado]);
}

function colocarValor($sheetdest, $sheetorig, $renglondest, $coldest, $renglonorig, $colorig, $value = '') {
    if ($sheetorig != null) {
        $sheetdest->setCellValue($coldest . $renglondest, $sheetorig->getCell($colorig . $renglonorig)->getValue());
    } else {
        $sheetdest->setCellValue($coldest . $renglondest, $value);
    }
}

function colocarBordes($sheetdest, $renglondest, $coldest, $style = PHPExcel_Style_Border::BORDER_THIN, $border = 'allborders', $color = '000000') {
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->applyFromArray(array(
        'borders' => array(
            $border => array(
                'style' => $style,
                'color' => array('rgb' => $color)
            )
        )
    ));
}

function copiarEstilos($sheetdest, $sheetorig, $renglondest, $coldest, $renglonorig, $colorig) {
    $sheetdest->getRowDimension($renglondest)->setRowHeight($sheetorig->getRowDimension($renglonorig)->getRowHeight());
    $sheetdest->getColumnDimension($coldest)->setAutoSize($sheetorig->getColumnDimension($colorig)->getAutoSize());
    $sheetdest->getColumnDimension($coldest)->setWidth($sheetorig->getColumnDimension($colorig)->getWidth());
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->getAlignment()->setWrapText($sheetorig->getCell($colorig . $renglonorig)->getStyle()->getAlignment()->getWrapText());
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->getAlignment()->setVertical($sheetorig->getCell($colorig . $renglonorig)->getStyle()->getAlignment()->getVertical());
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->getAlignment()->setHorizontal($sheetorig->getCell($colorig . $renglonorig)->getStyle()->getAlignment()->getHorizontal());
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->getFont()->setName($sheetorig->getCell($colorig . $renglonorig)->getStyle()->getFont()->getName());
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->getFont()->setBold($sheetorig->getCell($colorig . $renglonorig)->getStyle()->getFont()->getBold());
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->getFont()->setItalic($sheetorig->getCell($colorig . $renglonorig)->getStyle()->getFont()->getItalic());
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->getFont()->setSize($sheetorig->getCell($colorig . $renglonorig)->getStyle()->getFont()->getSize());
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->getFont()->setColor($sheetorig->getCell($colorig . $renglonorig)->getStyle()->getFont()->getColor());
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->getFill()->setFillType($sheetorig->getCell($colorig . $renglonorig)->getStyle()->getFill()->getFillType());
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->getFill()->setStartColor($sheetorig->getCell($colorig . $renglonorig)->getStyle()->getFill()->getStartColor());
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->getFill()->setEndColor($sheetorig->getCell($colorig . $renglonorig)->getStyle()->getFill()->getEndColor());
    $sheetdest->getCell($coldest . $renglondest)->getStyle()->getNumberFormat()->setFormatCode($sheetorig->getCell($colorig . $renglonorig)->getStyle()->getNumberFormat()->getFormatCode());
}

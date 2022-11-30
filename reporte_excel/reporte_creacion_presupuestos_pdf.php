<?php

require './ObtieneInformacionAExportar.php';
require './../PHPExcel/Classes/PHPExcel.php';
require_once '../mpdf/vendor/autoload.php';

if (!isset($_POST['idpresupuesto'])) {
    return;
}

$postdata = json_encode([
    'idpresupuesto' => $_POST['idpresupuesto']]);

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    $archivo = 'archivos-excel/leidos/reporte_creacion_presupuesto.xlsx';

    $extencion = 'pdf';

    //Crea un nuevo archivo excel
    $archivo_generado = str_replace(array('.xlsx', 'leidos'), array('', 'generados'), $archivo) . '_' . str_replace(array('{', '}'), array(''), uniqid()) . '.' . $extencion;

    $encabezado_personal = [];

    $encabezado_degustacion_hasta_servicios = [];

    $obtieneInfo = new ObtieneInformacionAExportar();

    $obtieneInfo->ObtenerInformacion($request->idpresupuesto);

    $inputFileType = PHPExcel_IOFactory::identify($archivo);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($archivo);
    $sheet = $objPHPExcel->getSheet(1);

// Extrae informacion de creacion de presupuesto (Columna B)
    $mpdf = new \Mpdf\Mpdf();

    $tabla = '<table>';

    for ($i = 2; $i <= 8; $i++) {
        $tabla .= '<tr>'
                . '<td align="right"><b>' . ExtraerValor($sheet, 'A' . $i) . '</b></td>'
                . '<td style="color:' . ExtraerEstilos($sheet, 'B' . $i)['FontColor'] . '; font-size:' . ExtraerEstilos($sheet, 'B' . $i)['FontSize'] . 'pt" align="left">' . ExtraerInformacionPrincipalPresupuesto($i - 1, $obtieneInfo) . '</td>'
                . '</tr>';
    }

    $tabla .= '</table>';

    if (sizeof($obtieneInfo->personal) > 0) {

        $cta = sizeof($obtieneInfo->personal);

        $tabla .= '<br><br><b>Personal</b><br><table style="width:100%;border-spacing: 0px;">';

        // Ajusta los encabezados de la tabla del rubro personal
        $tabla .= '<tr>';
        for ($i = 65; $i <= 70; $i++) {
            $tabla .= '<th style="border:1px solid #000000;background-color:' . ExtraerEstilos($sheet, chr($i) . '12')['StartColor'] . ';color:' . ExtraerEstilos($sheet, chr($i) . '12')['FontColor'] . '"  align="center">' . ExtraerValor(($i == 68) ? 'Costo con carga' : $sheet, chr($i) . '12', ($i == 68) ? false : true) . '</th>';
        }

        $tabla .= '</tr>';

        $i = 1;
        foreach ($obtieneInfo->personal as $personal) {
            $border_bottom = ($i < $cta) ? 'border-bottom:1px solid #000000' : 'border-bottom:4px double #000000';
            $tabla .= '<tr>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($personal['numero_de_personal'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">' . $personal['descripcion'] . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($personal['costo_unitario_pdv'], 2, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($personal['costo_cuota_diaria_fiscal'], 2, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($personal['numero_de_dias'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($personal['total'], 2, '.', ',') . '</td>';
            $tabla .= '</tr>';
            $i++;
        }

        // Asigna el subtotal del rubro de personal
        $tabla .= '<tr>'
                . '<td style="padding-left:15px;padding-right:15px;" align="right" colspan="5"><b>' . ExtraerValor($sheet, 'E31') . '</b></td>'
                . '<td style="padding-left:15px;padding-right:15px;" align="left"><b>$&nbsp;&nbsp;&nbsp;' . number_format($obtieneInfo->subtotal_personal, 2, '.', ',') . '</b></td>';
        $tabla .= '</tr>';
        $tabla .= '</table>';
    }

    if (sizeof($obtieneInfo->degustacion) > 0) {
        $cta = sizeof($obtieneInfo->degustacion);

        $tabla .= '<br><br><b>Degustación</b><br><table style="width:100%;border-spacing: 0px;">';

        // Ajusta los encabezados de la tabla del rubro degustacion
        $tabla .= '<tr>';
        for ($i = 65; $i <= 70; $i++) {
            $tabla .= '<th style="border:1px solid #000000;background-color:' . ExtraerEstilos($sheet, chr($i) . '29')['StartColor'] . ';color:' . ExtraerEstilos($sheet, chr($i) . '29')['FontColor'] . '"  align="center">' . ExtraerValor($sheet, chr($i) . '29') . '</th>';
        }

        $tabla .= '</tr>';

        $i = 1;
        foreach ($obtieneInfo->degustacion as $degustacion) {
            $border_bottom = ($i < $cta) ? 'border-bottom:1px solid #000000' : 'border-bottom:4px double #000000';
            $tabla .= '<tr>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($degustacion['numero'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">' . $degustacion['descripcion'] . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($degustacion['costo_unitario'], 2, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left"></td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($degustacion['numero_de_dias'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($degustacion['total'], 2, '.', ',') . '</td>';
            $tabla .= '</tr>';
            $i++;
        }
        // Asigna el subtotal del rubro de degustacion
        $tabla .= '<tr>'
                . '<td style="padding-left:15px;padding-right:15px;" align="right" colspan="5"><b>' . ExtraerValor($sheet, 'E31') . '</b></td>'
                . '<td style="padding-left:15px;padding-right:15px;" align="left"><b>$&nbsp;&nbsp;&nbsp;' . number_format($obtieneInfo->subtotal_degustacion, 2, '.', ',') . '</b></td>';
        $tabla .= '</tr>';
        $tabla .= '</table>';
    }

    if (sizeof($obtieneInfo->viaticos) > 0) {
        $cta = sizeof($obtieneInfo->viaticos);

        $tabla .= '<br><br><b>Viaticos</b><br><table style="width:100%;border-spacing: 0px;">';

        // Ajusta los encabezados de la tabla del rubro viaticos
        $tabla .= '<tr>';
        for ($i = 65; $i <= 70; $i++) {
            $tabla .= '<th style="border:1px solid #000000;background-color:' . ExtraerEstilos($sheet, chr($i) . '29')['StartColor'] . ';color:' . ExtraerEstilos($sheet, chr($i) . '29')['FontColor'] . '"  align="center">' . ExtraerValor($sheet, chr($i) . '29') . '</th>';
        }

        $tabla .= '</tr>';

        $i = 1;
        foreach ($obtieneInfo->viaticos as $viaticos) {
            $border_bottom = ($i < $cta) ? 'border-bottom:1px solid #000000' : 'border-bottom:4px double #000000';
            $tabla .= '<tr>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($viaticos['numero'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">' . $viaticos['descripcion'] . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($viaticos['costo_unitario'], 2, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left"></td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($viaticos['numero_de_dias'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($viaticos['total'], 2, '.', ',') . '</td>';
            $tabla .= '</tr>';
            $i++;
        }

        // Asigna el subtotal del rubro de viaticos
        $tabla .= '<tr>'
                . '<td style="padding-left:15px;padding-right:15px;" align="right" colspan="5"><b>' . ExtraerValor($sheet, 'E31') . '</b></td>'
                . '<td style="padding-left:15px;padding-right:15px;" align="left"><b>$&nbsp;&nbsp;&nbsp;' . number_format($obtieneInfo->subtotal_viaticos, 2, '.', ',') . '</b></td>';
        $tabla .= '</tr>';
        $tabla .= '</table>';
    }

    if (sizeof($obtieneInfo->uniformes) > 0) {
        $cta = sizeof($obtieneInfo->uniformes);

        $tabla .= '<br><br><b>Uniformes</b><br><table style="width:100%;border-spacing: 0px;">';

        // Ajusta los encabezados de la tabla del rubro uniformes
        $tabla .= '<tr>';
        for ($i = 65; $i <= 70; $i++) {
            $tabla .= '<th style="border:1px solid #000000;background-color:' . ExtraerEstilos($sheet, chr($i) . '29')['StartColor'] . ';color:' . ExtraerEstilos($sheet, chr($i) . '29')['FontColor'] . '"  align="center">' . ExtraerValor($sheet, chr($i) . '29') . '</th>';
        }

        $tabla .= '</tr>';

        $i = 1;
        foreach ($obtieneInfo->uniformes as $uniformes) {
            $border_bottom = ($i < $cta) ? 'border-bottom:1px solid #000000' : 'border-bottom:4px double #000000';
            $tabla .= '<tr>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($uniformes['numero'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">' . $uniformes['descripcion'] . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($uniformes['costo_unitario'], 2, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left"></td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($uniformes['numero_de_dias'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($uniformes['total'], 2, '.', ',') . '</td>';
            $tabla .= '</tr>';
            $i++;
        }
        // Asigna el subtotal del rubro de uniformes        
        $tabla .= '<tr>'
                . '<td style="padding-left:15px;padding-right:15px;" align="right" colspan="5"><b>' . ExtraerValor($sheet, 'E31') . '</b></td>'
                . '<td style="padding-left:15px;padding-right:15px;" align="left"><b>$&nbsp;&nbsp;&nbsp;' . number_format($obtieneInfo->subtotal_uniformes, 2, '.', ',') . '</b></td>';
        $tabla .= '</tr>';
        $tabla .= '</table>';
    }

    if (sizeof($obtieneInfo->seguridad_y_trabajo) > 0) {
        $cta = sizeof($obtieneInfo->seguridad_y_trabajo);

        $tabla .= '<br><br><b>Seguridad y trabajo</b><br><table style="width:100%;border-spacing: 0px;">';

        // Ajusta los encabezados de la tabla del rubro seguridad y trabajo
        $tabla .= '<tr>';
        for ($i = 65; $i <= 70; $i++) {
            $tabla .= '<th style="border:1px solid #000000;background-color:' . ExtraerEstilos($sheet, chr($i) . '29')['StartColor'] . ';color:' . ExtraerEstilos($sheet, chr($i) . '29')['FontColor'] . '"  align="center">' . ExtraerValor($sheet, chr($i) . '29') . '</th>';
        }

        $tabla .= '</tr>';

        $i = 1;
        foreach ($obtieneInfo->seguridad_y_trabajo as $seguridad_y_trabajo) {
            $border_bottom = ($i < $cta) ? 'border-bottom:1px solid #000000' : 'border-bottom:4px double #000000';
            $tabla .= '<tr>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($seguridad_y_trabajo['numero'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">' . $seguridad_y_trabajo['descripcion'] . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($seguridad_y_trabajo['costo_unitario'], 2, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left"></td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($seguridad_y_trabajo['numero_de_dias'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($seguridad_y_trabajo['total'], 2, '.', ',') . '</td>';
            $tabla .= '</tr>';
            $i++;
        }
        // Asigna el subtotal del rubro de seguridad y trabajo
        $tabla .= '<tr>'
                . '<td style="padding-left:15px;padding-right:15px;" align="right" colspan="5"><b>' . ExtraerValor($sheet, 'E31') . '</b></td>'
                . '<td style="padding-left:15px;padding-right:15px;" align="left"><b>$&nbsp;&nbsp;&nbsp;' . number_format($obtieneInfo->subtotal_seguridad_y_trabajo, 2, '.', ',') . '</b></td>';
        $tabla .= '</tr>';
        $tabla .= '</table>';
    }

    if (sizeof($obtieneInfo->materiales_POP) > 0) {
        $cta = sizeof($obtieneInfo->materiales_POP);

        $tabla .= '<br><br><b>Materiales POP</b><br><table style="width:100%;border-spacing: 0px;">';

        // Ajusta los encabezados de la tabla del rubro materiales POP
        $tabla .= '<tr>';
        for ($i = 65; $i <= 70; $i++) {
            $tabla .= '<th style="border:1px solid #000000;background-color:' . ExtraerEstilos($sheet, chr($i) . '29')['StartColor'] . ';color:' . ExtraerEstilos($sheet, chr($i) . '29')['FontColor'] . '"  align="center">' . ExtraerValor($sheet, chr($i) . '29') . '</th>';
        }

        $tabla .= '</tr>';

        $i = 1;
        foreach ($obtieneInfo->materiales_POP as $materialesPOP) {
            $border_bottom = ($i < $cta) ? 'border-bottom:1px solid #000000' : 'border-bottom:4px double #000000';
            $tabla .= '<tr>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($materialesPOP['numero'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">' . $materialesPOP['descripcion'] . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($materialesPOP['costo_unitario'], 2, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left"></td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($materialesPOP['numero_de_dias'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($materialesPOP['total'], 2, '.', ',') . '</td>';
            $tabla .= '</tr>';
            $i++;
        }
        // Asigna el subtotal del rubro de materiales POP
        $tabla .= '<tr>'
                . '<td style="padding-left:15px;padding-right:15px;" align="right" colspan="5"><b>' . ExtraerValor($sheet, 'E31') . '</b></td>'
                . '<td style="padding-left:15px;padding-right:15px;" align="left"><b>$&nbsp;&nbsp;&nbsp;' . number_format($obtieneInfo->subtotal_materiales_POP, 2, '.', ',') . '</b></td>';
        $tabla .= '</tr>';
        $tabla .= '</table>';
    }

    if (sizeof($obtieneInfo->servicios) > 0) {
        $cta = sizeof($obtieneInfo->servicios);

        $tabla .= '<br><br><b>Servicios</b><br><table style="width:100%;border-spacing: 0px;">';

        // Ajusta los encabezados de la tabla del rubro servicios
        $tabla .= '<tr>';
        for ($i = 65; $i <= 70; $i++) {
            $tabla .= '<th style="border:1px solid #000000;background-color:' . ExtraerEstilos($sheet, chr($i) . '29')['StartColor'] . ';color:' . ExtraerEstilos($sheet, chr($i) . '29')['FontColor'] . '"  align="center">' . ExtraerValor($sheet, chr($i) . '29') . '</th>';
        }

        $tabla .= '</tr>';

        $i = 1;
        foreach ($obtieneInfo->servicios as $servicios) {
            $border_bottom = ($i < $cta) ? 'border-bottom:1px solid #000000' : 'border-bottom:4px double #000000';
            $tabla .= '<tr>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($servicios['numero'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">' . $servicios['descripcion'] . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($servicios['costo_unitario'], 2, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left"></td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="center">' . number_format($servicios['numero_de_dias'], 0, '.', ',') . '</td>';
            $tabla .= '<td style="' . $border_bottom . ';padding-left:15px;padding-right:15px;" align="left">$&nbsp;&nbsp;&nbsp;' . number_format($servicios['total'], 2, '.', ',') . '</td>';
            $tabla .= '</tr>';
            $i++;
        }
        // Asigna el subtotal del rubro de servicios
        $tabla .= '<tr>'
                . '<td style="padding-left:15px;padding-right:15px;" align="right" colspan="5"><b>' . ExtraerValor($sheet, 'E31') . '</b></td>'
                . '<td style="padding-left:15px;padding-right:15px;" align="left"><b>$&nbsp;&nbsp;&nbsp;' . number_format($obtieneInfo->subtotal_servicios, 2, '.', ',') . '</b></td>';
        $tabla .= '</tr>';
        $tabla .= '</table>';
    }

// Asigna el titulo: GRAN TOTAL ANTES DE I.V.A
    $tabla .= '<br><br><br><table style="width:100%;"><tr><td style="width:50%"></td><td style="width:50%"><table style="width:100%;">';
    $tabla .= '<tr><th colspan="2" align="right">' . ExtraerValor($sheet, 'E44') . '</th></tr>';
    $tabla .= '<tr><th colspan="2" style="height:30px" align="right"></th></tr>';
    // Asigna el nombre de los campos de los calculos de subtotales e I.V.A de las celdas: E46 a la E48
    for ($_i = 46; $_i <= 48; $_i++) {
        $tabla .= '<tr><th style="border-bottom:1px solid #000000;" align="right">' . ExtraerValor($sheet, 'E' . $_i) . '</th><th style="border-bottom:4px double #000000;">$&nbsp;&nbsp;&nbsp;' . ExtraerSubtotales($_i, $obtieneInfo) . '</th></tr>';
    }

    $tabla .= '</table></td></tr></table>';
// Asigna subtotal antes de I.V.A
    // Write some HTML code:
    $mpdf->WriteHTML($tabla);

// Output a PDF file directly to the browser
    $mpdf->Output($archivo_generado);

    $pdf = file_get_contents($archivo_generado);

    // Obtiene solo el nombre corto del archivo
    $a = explode('/', $archivo_generado);
    $nombre_corto = $a[sizeof($a) - 1];

    $response = array(
        'status' => TRUE,
        'url' => "data:application/pdf;base64," . base64_encode($pdf),
        'nombre_archivo' => $nombre_corto
    );

    die(json_encode($response));
}

function ExtraerSubtotales($opt, $obtieneInfo) {
    $val = '';
    switch (intval($opt)) {
        case 46:
            // Asigna subtotal general
            $val = number_format($obtieneInfo->subtotal_general, 2, '.', ',');
            break;
        case 47:
            // Asigna comision de agencia servicio
            $val = number_format($obtieneInfo->comision_agencia_servicio, 2, '.', ',');
            break;
        case 48:
            // Asigna subtotal antes de I.V.A
            $val = number_format($obtieneInfo->subtotal_antes_de_iva, 2, '.', ',');
            break;
    }
    return $val;
}

function ExtraerInformacionPrincipalPresupuesto($opt, $obtieneInfo) {
    $val = '';
    switch (intval($opt)) {
        case 1:
            $val = date('Y-m-d H:i:s');
            break;
        case 2:
            $val = $obtieneInfo->No_Ppto;
            break;
        case 3:
            $val = $obtieneInfo->Cliente;
            break;
        case 4:
            $val = $obtieneInfo->Proyecto;
            break;
        case 5:
            $val = $obtieneInfo->Plazas;
            break;
        case 6:
            $val = $obtieneInfo->Periodos;
            break;
        case 7:
            $val = $obtieneInfo->Duracion;
            break;
        case 8:
            $val = $obtieneInfo->Objetivo;
            break;
    }
    return $val;
}

function ExtraerValor($sheet, $cell, $extract = true) {
    return ($extract == true) ? $sheet->getCell($cell) : $sheet;
}

function ExtraerEstilos(PHPExcel_Worksheet $sheet, $cell) {
    return array(
        'FontColor' => '#' . $sheet->getCell($cell)->getStyle()->getFont()->getColor()->getRGB(),
        'FontSize' => $sheet->getCell($cell)->getStyle()->getFont()->getSize(),
        'StartColor' => '#' . $sheet->getCell($cell)->getStyle()->getFill()->getStartColor()->getRGB(),
        'EndColor' => '#' . $sheet->getCell($cell)->getStyle()->getFill()->getEndColor()->getRGB()
    );
}

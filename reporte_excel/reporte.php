<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../database.php';
require './../PHPExcel/Classes/PHPExcel.php';

if (isset($_FILES['archivo'])) {

    $directorio_subida = 'archivos-excel/leidos/';
    $archivo = $directorio_subida . basename($_FILES['archivo']['name']);

    $extencion = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

    if (strtolower($extencion) == 'xls' || strtolower($extencion) == 'xlsx' || strtolower($extencion) == 'csv') {
        if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $archivo)) {
            echo json_encode(['id' => 1, 'res' => 'Se produjo un error al subir el archivo, tal vez el archivo este dañado.']);
            return;
        }
    } else {
        echo json_encode(['id' => 1, 'res' => 'Este tipo de formato [' . $extencion . '] no es valido.']);
        return;
    }

    $inputFileType = PHPExcel_IOFactory::identify($archivo);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($archivo);
    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    //$sheet->setCellValue('A1', 'Esta es una prueba 1');
    
    $archivo_generado = str_replace(array('.' . $extencion, 'leidos'), array('', 'generados'), $archivo) . '_' . str_replace(array('{', '}'), array(''),     uniqid()) . '.' . $extencion;
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($archivo_generado);

    echo json_encode(['id' => 0, 'res' => $archivo_generado]);
} else {
    echo json_encode(['id' => 1, 'res' => 'no se selecciono ningun archivo']);
}
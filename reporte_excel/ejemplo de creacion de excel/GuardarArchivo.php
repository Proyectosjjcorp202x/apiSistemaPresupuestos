<?php

$nombre_archivo = 'creaExcel.php';
// Abrir el archivo, creándolo si no existe:
$archivo = fopen($nombre_archivo, "w+b");
if ($archivo == false) {
    echo "Error al crear el archivo";
} else {
    // Escribir en el archivo:
    fwrite($archivo, '<?php 
header("Pragma: public");
header("Expires: 0");
$filename = "nombreArchivoQueDescarga.xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
?>');
    fwrite($archivo, (isset($_POST['contenido'])) ? $_POST['contenido'] : '');
    // Fuerza a que se escriban los datos pendientes en el buffer:
    fflush($archivo);
}
// Cerrar el archivo:
fclose($archivo);

echo $nombre_archivo;

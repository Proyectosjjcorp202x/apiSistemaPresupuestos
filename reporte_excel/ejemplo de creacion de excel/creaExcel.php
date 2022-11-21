<?php 
header("Pragma: public");
header("Expires: 0");
$filename = "nombreArchivoQueDescarga.xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
?>
    <table>
        <tbody>
            <tr>
                <th colspan="10" style="background-color: red;color:white">
                    <b>Listado en tabla excel</b>
                </th>
            </tr>
            <tr>
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
                <td>5</td>
                <td>6</td>
                <td>7</td>
                <td>8</td>
                <td>9</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="10">
                    <img id="img" src="grafica_generada2.jpg">
                </td>
            </tr>
        </tbody>
    </table>    

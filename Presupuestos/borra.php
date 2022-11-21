<?php
/**
 * Regresa lista de clientes.
 */
require '../database.php';
date_default_timezone_set('America/Mexico_City');

$condicion 	= "";
$cat 		= [];
$sql 		= "";

$idpresupuesto		= "";

// Get the posted data.
$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	// Extract the data.
	$request = json_decode($postdata);	            
  
	// Validate.
	if (
		(string)$request->idpresupuesto == ''
		) 
	{
		return http_response_code(400);
	}

	// Sanitize.
	$idpresupuesto	  = mysqli_real_escape_string($con, (string)$request->idpresupuesto); 
		
	// *************************	
	// Inserción o actualización	
	if($idpresupuesto != "")
	{
		$sql = "UPDATE presupuesto set idestatus = 0
		WHERE idpresupuesto 	= '$idpresupuesto';";
	}
	// *************************
	
	// Inserción o actualización de datos
    if (mysqli_query($con, $sql)) {
    }
	
	// Si se trata de una inserción debe regresar el idprespuesto insertado
	if($idpresupuesto!="")
	{
		$b = true;
		while ($b) {
			$cat[0]['id']      	=   $idpresupuesto;
			$cat[0]['valor']		=   'Dato borrado';	
			//$cat[$i]['sql']		=   $sql;
			$b = false;
		}
	}
	//echo json_encode($cat);
	echo json_encode(array("RESPUESTA" => $cat));
}
else
{
	return http_response_code(404);
}
?>
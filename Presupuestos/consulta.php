<?php
/**
 * Regresa lista de clientes.
 */
require '../database.php';
date_default_timezone_set('America/Mexico_City');

$condicion = "";
$cat = [];

// Get the posted data.
$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	// Extract the data.
	$request = json_decode($postdata);	            
  
	// Validate.
	if ((int)$request->idpresupuesto == '') {
		return http_response_code(400);
	}

	// Sanitize.
	$idpresupuesto  = mysqli_real_escape_string($con, (int)$request->idpresupuesto);  
	$sql = "Select * from Vw_prespuesto where idpresupuesto  =  '" . $idpresupuesto . "';";
	
	if($result = mysqli_query($con,$sql))
	{
		$i = 0;
		while($row = mysqli_fetch_assoc($result))
		{
			$cat[$i]['idpresupuesto'] 	=	$row['idpresupuesto'];
			$cat[$i]['folio'] 			=	$row['folio'];
			$cat[$i]['idcliente'] 		=	$row['idcliente'];
			$cat[$i]['idcat_o_div'] 	=	$row['idcat_o_div'];
			$cat[$i]['proyecto'] 		=	$row['proyecto'];
			$cat[$i]['idplaza'] 		=	$row['idplaza'];
			$cat[$i]['idperiodo'] 		=	$row['idperiodo'];
			$cat[$i]['uda'] 			=	$row['uda'];
			$cat[$i]['fda'] 			=	$row['fda'];
			$cat[$i]['idestatus'] 		=	$row['idestatus'];
			$cat[$i]['fechainicial'] 	=	$row['fechainicial'];
			$cat[$i]['fechafinal'] 		=	$row['fechafinal'];
			$cat[$i]['fechaalta'] 		=	$row['fechaalta'];
			$cat[$i]['nombrecliente'] 	=	$row['nombrecliente'];
			$cat[$i]['categoria'] 		=	$row['categoria'];
			$cat[$i]['plaza'] 			=	$row['plaza'];
			$cat[$i]['periodo'] 		=	$row['periodo'];
			$i++;
		}
		
		//echo json_encode($cat);
		echo json_encode(array("RESPUESTA" => $cat));
	}
	else
	{
	  http_response_code(404);
	}
}
else
{
	return http_response_code(404);
}
?>
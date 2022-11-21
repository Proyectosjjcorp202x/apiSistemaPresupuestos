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
$folio		= "";
$idcliente  = "";
$cat_div	= "";
$proyecto	= "";
$plaza		= "";
$periodo	= "";
$uda		= "";
$fda		= "";
$idestatus	= "";
$fechainicial	= "";
$fechafinal	= ""; 
$fechaalta	= "";


// Get the posted data.
$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	// Extract the data.
	$request = json_decode($postdata);	            
  
	// Validate.
	if (
		(string)$request->folio 		== '' || 
		(string)$request->idcliente 	== '' || 
		(string)$request->cat_div	 	== '' || 
		(string)$request->proyecto 		== '' || 
		(string)$request->plaza	 		== '' || 
		(string)$request->periodo	 	== '' || 
		(string)$request->uda 			== '' || 
		(string)$request->fda 			== '' || 
		(string)$request->idestatus 	== '' || 
		(string)$request->fechainicial == '' || 
		(string)$request->fechafinal	== '' || 
		(string)$request->fechaalta	== ''  
		) 
	{
		return http_response_code(400);
	}

	// Sanitize.
	$idpresupuesto	  = mysqli_real_escape_string($con, (string)$request->idpresupuesto); 
	$folio		  = mysqli_real_escape_string($con, (string)$request->folio); 
	$idcliente	  = mysqli_real_escape_string($con, (string)$request->idcliente); 
	$cat_div	  = mysqli_real_escape_string($con, (string)$request->cat_div); 
	$proyecto  	  = mysqli_real_escape_string($con, (string)$request->proyecto); 
	$plaza		  = mysqli_real_escape_string($con, (string)$request->plaza); 
	$periodo	  = mysqli_real_escape_string($con, (string)$request->periodo); 
	$uda		  = mysqli_real_escape_string($con, (string)$request->uda); 
	$fda		  = mysqli_real_escape_string($con, (string)$request->fda); 
	$idestatus	  = mysqli_real_escape_string($con, (string)$request->idestatus); 
	$fechainicial = mysqli_real_escape_string($con, (string)$request->fechainicial); 
	$fechafinal   = mysqli_real_escape_string($con, (string)$request->fechafinal); 
	$fechaalta	  = mysqli_real_escape_string($con, (string)$request->fechaalta); 
		
	// *************************	
	// Inserción o actualización	
	if($idpresupuesto=="")
	{
		$sql = "INSERT INTO presupuesto " .
				"(folio, idcliente, idcat_o_div, proyecto, idplaza, idperiodo, 
				uda, fda, idestatus, fechainicial, fechafinal, fechaalta) " .
				"VALUES ('$folio', $idcliente, '$cat_div', '$proyecto', '$plaza', '$periodo', '$uda', '$fda', $idestatus, 
				'$fechainicial', '$fechafinal', '$fechaalta');";		
	}
	else
	{
		$sql = "UPDATE presupuesto " .
		"SET folio 				= '$folio' , 
		idcliente 				= $idcliente, 
		idcat_o_div				= '$cat_div', 
		proyecto 				= '$proyecto', 
		idplaza 				= '$plaza', 
		idperiodo 				= '$periodo', 
		uda 					= '$uda', 
		fda 					= '$fda', 
		idestatus 				= $idestatus, 
		fechainicial 			= '$fechainicial', 
		fechafinal 				= '$fechafinal', 
		fechaalta 				= '$fechaalta' 
		WHERE idpresupuesto 	= '$idpresupuesto';";
	}
	// *************************
	
	// Inserción o actualización de datos
    if (mysqli_query($con, $sql)) {
    }
	
	// Si se trata de una inserción deve regresar el idprespuesto insertado
	if($idpresupuesto=="")
	{
		// Consulta de datos
		$sql = "Select max(idpresupuesto) as Max from presupuesto;";		
		if($result = mysqli_query($con,$sql))
		{
			$i = 0;
			while($row = mysqli_fetch_assoc($result))
			{
				$cat[0]['id']      	=   $row['Max'];
				$cat[0]['valor']		=   'Dato insertado';
				//$cat[0]['sql']			=   $sql;
				$i++;
			}			
		}
	}
	else
	{
		$cat[0]['id']      	=   $idpresupuesto;
		$cat[0]['valor']		=   'Dato actualizado';	
		//$cat[0]['sql']			=   $sql;
	}
	echo json_encode(array("RESPUESTA" => $cat));
}
else
{
	return http_response_code(404);
}
?>
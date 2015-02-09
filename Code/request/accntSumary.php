<?php

	$result = array( 
		'totalDrugs'=>"4",
		'totalLabOP'=>"4",
		'totalLabAdmin'=>"5");

	$return_values = json_encode($result);
	header('Content-Type: application/json');
	echo $return_values;
?>
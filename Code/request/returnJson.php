<?php
//function to return result as a json
function returnJson($result){
	$return_values = json_encode($result);
	header('Content-Type: application/json');
	echo $return_values;
}
?>
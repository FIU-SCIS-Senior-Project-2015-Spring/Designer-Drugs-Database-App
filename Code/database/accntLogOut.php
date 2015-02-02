<?php
	session_start();
	session_destroy();

	$result = true;

	$return_values = json_encode($result);
	header('Content-Type: application/json');
	echo $return_values;
?>
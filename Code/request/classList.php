<?php

	$result = array(
		array('cid'=>"4",'class'=>"alpha-Pyrrolidinopropiophenone"),
		array('cid'=>"5",'class'=>"alpha-Pyrrolidinobutiophenone")
	);

	$return_values = json_encode($result);
	header('Content-Type: application/json');
	echo $return_values;
?>
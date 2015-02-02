<?php

	$result = array(
		array('cid'=>"4",'cName'=>"alpha-Pyrrolidinopropiophenone", 'cFormula'=>"C13H17NO"),
		array('cid'=>"5",'cName'=>"alpha-Pyrrolidinobutiophenone", 'cFormula'=>"C14H19NO")
	);

	$return_values = json_encode($result);
	header('Content-Type: application/json');
	echo $return_values;
?>
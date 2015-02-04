<?php

	$result = array( 'cid'=>"4",'cName'=>"alpha-Pyrrolidinopropiophenone",'cFormula'=>"C13H17NO",'cOName'=>"FIU_0278",'cMass'=>"203.1",'cPrecursor'=>"204.1",'cFrag'=>"98",'cRT'=>"5.10",'cCas'=>"19134-50-0");

	$return_values = json_encode($result);
	header('Content-Type: application/json');
	echo $return_values;
?>
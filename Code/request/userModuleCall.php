<?php
	//load database configuration
	include("dbConfig.php");

	// need to do this to convert the data received to $_REQUEST VARIABLE
	$_REQUEST = json_decode(file_get_contents('php://input'),true);	

	//load userModule class
	include("userModuleClass.php");		
	
	//create object and call its method
	$myauth = new userModule($_REQUEST, $dbConfig);
	$myauth->userRequest();
?>
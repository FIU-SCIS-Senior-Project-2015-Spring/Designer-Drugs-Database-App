<?php
	//load database configuration
	include("dbConfig.php");

	//load userModule class
	include("compModuleClass.php");		

	// need to do this to convert the data received to $_REQUEST VARIABLE
	$_REQUEST = json_decode(file_get_contents('php://input'),true);	
	
	$loggedIn = "";
	//create object and call its method
	$myauth = new compModule($loggedIn, $_REQUEST, $dbConfig);
	$myauth->compRequest();
?>
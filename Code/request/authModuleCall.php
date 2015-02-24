<?php
	//load database configuration
	include("dbConfig.php");

	// need to do this to convert the data received to $_REQUEST VARIABLE
	$_REQUEST = json_decode(file_get_contents('php://input'),true);	

	//load database configuration
	include("authModuleClass.php");
	
	//create object and call its method
	$loggedIn;
	$myauth = new authModule($_REQUEST, $dbConfig);
	$myauth->authRequest();
?>
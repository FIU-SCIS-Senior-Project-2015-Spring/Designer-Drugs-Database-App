<?php
	//load database configuration
	include("dbConfig.php");

	// need to do this to convert the data received to $_REQUEST VARIABLE
	$_REQUEST = json_decode(file_get_contents('php://input'),true);	

	//setup to call the authmodule
	$tempSection = $_REQUEST["section"];
	$_REQUEST["section"] = "testSesionInfo";
	
	//load authModule class
	include("authModuleClass.php");	
	
	//call authModule

	$myauth = new authModule($_REQUEST, $dbConfig);
	$loggedIn = $myauth->authRequest();
	$loggedIn = $loggedIn["loggedIn"];
	
	//setup to call user module
	$_REQUEST["section"] = $tempSection;

	//load userModule class
	include("userModuleClass.php");		
	
	//create object and call its method
	$myauth = new userModule($loggedIn, $_REQUEST, $dbConfig);
	$myauth->userRequest();
?>
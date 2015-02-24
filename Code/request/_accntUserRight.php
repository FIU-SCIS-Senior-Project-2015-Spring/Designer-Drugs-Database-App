<?php
	//load database configuration
	include("dbConfig.php");
	include("returnJson.php");

	// Start the session
	session_start();

	//Initialize as everything is ok
	//$result = array();
	$result["Code"] = 2000;

	//Enter the variable to my own variables
	if (isset ($_SESSION["userEmail"])) 
		$useremail = $_SESSION["userEmail"]; 
	else {
		$result["Code"] = 2001;
		returnJson($result);
		exit;
		}

	if (isset ($_SESSION["userID"])) 
		$userid = $_SESSION["userID"]; 
	else {
		$result["Code"] = 2002;
		returnJson($result);
		exit;
		}

	//Try and catch in case there is an error
	try {
		$conn = new PDO("mysql:host=$dbAddress;dbname=$dbName", $dbUser, $dbPassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT users.uid as userID, users.uEmail as userEmail,users.uPass as pass, role.role AS permission
								FROM users, role
								WHERE users.uEmail= ? AND users.uid= ? AND users.uRole=role.rid"); 
		$stmt->execute(array($useremail, $userid));

		// set the resulting array to associative
		$result["data"] = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
		//fetch result into $result
		$result["data"] = ($stmt->fetchAll());
		
		//Add codes 1005 if user was found.
		if ($result["data"]!=null) {
			$result["data"] = $result["data"][0];
		}else {$result["Code"] = 2003;returnJson($result);exit;}
	}
	catch(PDOException $e) {//if another error occurred, throw error code 1006
		$result["Code"] = 2004;
		$result["CodeDetails"] = $e->getMessage();
		returnJson($result);
		exit;
	}
?>
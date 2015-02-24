<?php
//load database configuration
include("dbConfig.php");
//check if proper rights are present
include("accntUserRight.php");


//check that user has admin rights
if ($result["data"]["permission"] != "admin") {$result["Code"] = 1008; returnJson($result);exit;}

// need to do this to convert the data received to $_REQUEST VARIABLE
$_REQUEST = json_decode(file_get_contents('php://input'),true);

//Enter the variable to my own variables
$email = $_REQUEST["email"];

//Checking that the variables are not empty
if (!$email || empty($email)) {$result["Code"] = 1009;returnJson($result); exit;}

//Try and catch in case there is an error
try {
	$conn = new PDO("mysql:host=$dbAddress;dbname=$dbName", $dbUser, $dbPassword);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $conn->prepare("SELECT users.uid as userID, users.uName AS Name, users.uEmail AS email, role.role AS permission
							FROM users, role
							WHERE users.uEmail= ? AND users.uRole=role.rid"); 
	$stmt->execute(array($email));

	// set the resulting array to associative
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
	//fetch result into $result
	$result = ($stmt->fetchAll());
	
	//Add codes 1000 and 1001 acording to whether the login and password where correct.
	if ($result!=null) {
		$result = $result[0];
		$result["Code"] = 1010;
		returnJson($result);
		
	}else{$result["Code"] = 1011;returnJson($result);exit;}
}
catch(PDOException $e) {//if another error occurred, throw error code 1002
	$result["Code"] = 1012;
	$result["CodeDetails"] = $e->getMessage();
	returnJson($result);
}
?>
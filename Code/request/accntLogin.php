<?php
include("dbConfig.php");
// Start the session
session_start();

// need to do this to convert the data received to $_REQUEST VARIABLE
$_REQUEST = json_decode(file_get_contents('php://input'),true);

//Enter the variable to my own variables
$email = $_REQUEST["email"];
$pass = $_REQUEST["password"];

//For security purposes the password in the database is encrypted
//thus, I need to encrypted when comparing to it.
$pass =  md5($pass);

//Checking that the variables are not empty
if (!$email || empty($email)) die("Email field is required.");
if (!$pass || empty($pass)) die("Password field is required.");

//Try and catch in case there is an error
try {
    $conn = new PDO("mysql:host=$dbAddress;dbname=$dbName", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT users.uid as userID, users.uName AS Name, users.uEmail AS email, role.role AS permission
							FROM users, role
							WHERE users.uEmail= ? AND users.uPass= ? AND users.uRole=role.rid"); 
    $stmt->execute(array($email, $pass));

    // set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
	//fetch result into $result
	$result = ($stmt->fetchAll());
	
	//Add codes 1000 and 1001 acording to whether the login and password where correct.
	if ($result!=null) {
		$result = $result[0];
		$result["Code"] = 1000;

		//Create Sesion Variables
		$_SESSION["userEmail"] =$result['email'];
		$_SESSION["userID"] =$result['userID'];
	}else $result["Code"] = 1001;
}
catch(PDOException $e) {//if another error occurred, throw error code 1002
	$result["Code"] = 1002;
    $result["CodeDetails"] = $e->getMessage();
}

//Return result as a json
$return_values = json_encode($result);
header('Content-Type: application/json');
echo $return_values;
?>
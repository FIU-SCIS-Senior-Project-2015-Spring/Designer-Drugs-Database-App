<?php
	//load database configuration
	include("dbConfig.php");

	// need to do this to convert the data received to $_REQUEST VARIABLE
	$_REQUEST = json_decode(file_get_contents('php://input'),true);	
	
	//if its not a login to continue this page need to certify that the user has appropriate rights
	include("accntUserRight.php");
	
	//check if section does not exist to return error CODE 1001, section missing.
	if (!(isset ($_REQUEST["section"]))) {
		$result["Code"] = 1001;
		$result["CodeDetails"] = "Error with request missing data, contact administrator.";
		returnJson($result);
		exit;
	}
	else $section = $_REQUEST["section"];

	//initialize variables that will be used later
	$arrayOfRequest = array();
	$sql = "";
	$counter = 1;


	//EMAIL IS ALWAYS requires in this module, thus, before continuing, need to check
	//Enter the variable to my own variables
	if (isset ($_REQUEST["email"])) $arrayOfRequest[0] = $_REQUEST["email"]; 
	else {
			$result["Code"] = 1003;
			$result["CodeDetails"] = "Email is not being received.";
			returnJson($result);
			exit;
	}

	//Checking that the variables are not empty
	if (!$arrayOfRequest[0] || empty($arrayOfRequest[0])){
		$result["Code"] = 1004;
		$result["CodeDetails"] = "email field cannot be empty";
		returnJson($result);
		exit;
	}

	//check if user has admin permission to continue unless requesting its own data
	if($section != "getMyInfo" && $section != "modifyMyInfo")
		if ($result["data"]["permission"] != "admin") {
			$result["Code"] = 1005; 
			$result["CodeDetails"] = "user has no permission for this module";
			returnJson($result);
			exit;
		}

		
		
	switch ($section) {
	   case 'findUser':					
			//sql need it
			$sql = "SELECT users.uid AS userID, users.uName AS Name, users.uEmail AS email, role.role AS permission
								FROM users, role
								WHERE users.uEmail= ? AND users.uRole=role.rid";
			break;
		case 'modifyUser':		
			//Enter the variable to my own variables
			if (isset ($_REQUEST["modifyAction"])) $modifyAction = $_REQUEST["modifyAction"]; 
			else {
				$result["Code"] = 1003;
				$result["CodeDetails"] = "variable modifyAction was not found";
				returnJson($result);
				exit;
			}
			//Checking that the variables are not empty
			if (!$modifyAction || empty($modifyAction)){
				$result["Code"] = 1004;
				$result["CodeDetails"] = "modifyAction cannot be empty";
				returnJson($result);
				exit;
			}			
						
			//sql need it
			if ($modifyAction == "update")		
				$sql = "UPDATE users SET uRole = '2' WHERE users.uEmail = ?";
			else if ($modifyAction == "downgrade")
				$sql = "UPDATE users SET uRole = '1' WHERE users.uEmail = ?";			
			break;
		case 'deleteUser':
								
			//sql need it
			$sql = "DELETE FROM users WHERE users.uEmail = ?";
			break;
		case 'addUser':
		
			$date = getdate();
			date_add($date,date_interval_create_from_date_string("2 days"));
			$msg = "You have been invited to join ".$dbAddress."/nPlease accept this offer before: ".date_format($date,"Y-m-d")."/n by accessing this link: ";
			$msg .= $dbAddress."/newCustomer?sesion=".md5($arrayOfRequest[0]);
			mail($arrayOfRequest[0],$dbAddress." Invite",$msg);
			$arrayOfRequest[1] = $date;
			
			//sql need it
			$sql = "INSERT INTO userinvite (`iemail`, `iexp`) VALUES (?,?)";
			$counter++;
			break;
		case 'getMyInfo':
			if($arrayOfRequest[0] != $result["data"]["userEmail"]){
			   $result["Code"] = 1008;
			   $result["CodeDetails"] = "attempting to get other than the logged user information";
			   returnJson($result);
			   exit; 
			}
			//sql need it
			$sql = "SELECT users.uid AS userID, users.uName AS Name, users.uEmail AS email, role.role AS permission
								FROM users, role
								WHERE users.uEmail= ? AND users.uRole=role.rid";
			break;
		case 'modifyMyInfo':
			if($arrayOfRequest[0] != $result["data"]["userEmail"]){
			   $result["Code"] = 1008;
			   $result["CodeDetails"] = "attempting to modify other than the logged user information";
			   returnJson($result);
			   exit; 
			}

			if (isset ($_REQUEST["modifyAction"])) $modifyAction = $_REQUEST["modifyAction"]; 
			else {
				$result["Code"] = 1003;
				$result["CodeDetails"] = "variable modifyAction was not found";
				returnJson($result);
				exit;
			}
			//Checking that the variables are not empty
			if (!$modifyAction || empty($modifyAction)){
				$result["Code"] = 1004;
				$result["CodeDetails"] = "modifyAction cannot be empty";
				returnJson($result);
				exit;
			}			
			
			switch ($modifyAction) {
			   case 'setName':	
					if (isset ($_REQUEST["newName"]) && (!(empty($_REQUEST["newName"])))){
						$arrayOfRequest[1] = $arrayOfRequest[0];
						$arrayOfRequest[0] = $_REQUEST["newName"];
						$sql = "UPDATE users SET uName = ? WHERE users.uEmail = ?";
						$counter++;
					}else if(!(isset ($_REQUEST["newName"]))){
						$result["Code"] = 1003;
						$result["CodeDetails"] = "newName not found";
						returnJson($result);
						exit;						
					}else{
						$result["Code"] = 1004;
						$result["CodeDetails"] = "newName cannot be empty";
						returnJson($result);
						exit;												
					}
					break;
			   case 'setPass':	
					if(!(isset ($_REQUEST["newPass"]))){
						$result["Code"] = 1003;
						$result["CodeDetails"] = "newPass not found";
						returnJson($result);
						exit;						
					}
					if(!(isset ($_REQUEST["confPass"]))){
						$result["Code"] = 1003;
						$result["CodeDetails"] = "confirm pass not found";
						returnJson($result);
						exit;						
					}
					if(!(isset ($_REQUEST["oldPass"]))){
						$result["Code"] = 1003;
						$result["CodeDetails"] = "old pass not found";
						returnJson($result);
						exit;						
					}
					if((empty($_REQUEST["newPass"]))){
						$result["Code"] = 1004;
						$result["CodeDetails"] = "newName cannot be empty";
						returnJson($result);
						exit;												
					}
					if((empty($_REQUEST["confPass"]))){
						$result["Code"] = 1004;
						$result["CodeDetails"] = "confPass cannot be empty";
						returnJson($result);
						exit;												
					}
					if((empty($_REQUEST["oldPass"]))){
						$result["Code"] = 1004;
						$result["CodeDetails"] = "oldPass cannot be empty";
						returnJson($result);
						exit;												
					}
					
					if($_REQUEST["confPass"] != $_REQUEST["newPass"]){
						$result["Code"] = 1009;
						$result["CodeDetails"] = "password mismatch";
						returnJson($result);
						exit;												
					}
					
					if(md5($_REQUEST["oldPass"]) != md5($result["data"]["userEmail"])){
						$result["Code"] = 1010;
						$result["CodeDetails"] = "old password is incorrect";
						returnJson($result);
						exit;												
					}

					$arrayOfRequest[1] = $arrayOfRequest[0];
					$arrayOfRequest[0] = md5($_REQUEST["confPass"]);
					$sql = "UPDATE users SET uPass = ? WHERE users.uEmail = ?";
					$counter++;
					break;
				default:
				   $result["Code"] = 1002;
				   $result["CodeDetails"] = "no valid action to modify logged user info";
				   returnJson($result);
				   exit;			
			}

		default:
		   $result["Code"] = 1002;
		   $result["CodeDetails"] = "wrong section";
		   returnJson($result);
		   exit; 
	}

	//Try and catch in case there is an error
	try {
		$conn = new PDO("mysql:host=$dbAddress;dbname=$dbName", $dbUser, $dbPassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare($sql);
		$stmt->execute($arrayOfRequest);
	    
		// set the resulting array to associative
		$temp = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
		//fetch result into $result
		$temp = ($stmt->fetchAll());	

		//fetch result into $result
		if(($temp)!= null){
			$result["data"] = ($temp);
			$result["data"] = $result["data"][0];
			returnJson($result);
		}else {
			$result["Code"] = 1006;
			$result["CodeDetails"] = "user not found";
			returnJson($result);
			exit;
		}
	}
	catch(PDOException $e) {//if another error occurred, throw error code 1006
		$result["Code"] = 1007;
		$result["CodeDetails"] = $e->getMessage();
		returnJson($result);
		exit;
	}
?>
<?php
	/* By convention the variable $request must come with an email ans with a section.
	* email specifies the user that is to be modify or changed
	* section specifies what is to be done to that user,
	* lastly also any other variable need it for each special case.
	*/	
	class userModule
	{
		private $sql;
		private $counter;
		private $result;
		private $arrayOfRequest;
		private $requests;
		private $dbConfig;
		private $userRequestType;

		public function __construct($theRequests, $dbConfig)
		{
			$this->sql = "";
			$this->counter = 1;
			$this->result = array();
			$this->result["Code"] = 2000;
			$this->arrayOfRequest = array();
			$this->checkVariableNotEmpty($theRequests["email"], "email");			
			$this->arrayOfRequest[0] = $theRequests["email"]; 
			$this->requests = $theRequests;
			$this->dbConfig = $dbConfig;
			$this->userRequestType = "";

			////////////check session///////////
			//setup to call the authmodule
			$tempSection = $theRequests["section"];
			$theRequests["section"] = "testSesionInfo";
			
			//load authModule class
			include("authModuleClass.php");	
			
			//call authModule
			$myauth = new authModule($theRequests, $dbConfig);
			$loggedIn = $myauth->authRequest();
			$this->result["loggedUser"] = $loggedIn["loggedIn"];
			
			//setup to call user module
			$_REQUEST["section"] = $tempSection;

			//////////////////////////////////
			
			$this->checkVariableNotEmpty($this->arrayOfRequest[0], "email");
			$this->checkVariableNotEmpty($this->result["loggedUser"], "logged user");
		}
	  
		public function userRequest()
		{
			$this->checkVariableNotEmpty($this->requests["section"], "Section");
			//check if user has admin permission to continue unless requesting its own data
			if($this->requests["section"] != "getMyInfo" && $this->requests["section"] != "setMyName"&& $this->requests["section"] != "setMyPass"&& $this->requests["section"] != "signUP")
				if ($this->result["loggedUser"]["permission"] != "admin") {
					$this->result["Code"] = 1005; 
					$this->result["CodeDetails"] = "user has no permission for this module";
					$this->returnJson($this->result);
					exit;
				}
			//section controller
			
			switch ($this->requests["section"]) {
			case 'findUser':		$this->findUser(); 				
				break;
			case 'upgradeUser':		$this->upgradeUser(); 	
				break;
			case 'downgradeUser':	$this->downgradeUser();	
				break;
			case 'deleteUser': 		$this->deleteUser(); 
				break;
			case 'addUser':  		$this->addUser(); 
				break;
			case 'getMyInfo': 		$this->getMyInfo(); 
				break;
			case 'setMyName': 		$this->setMyName();	 
				break;
			case 'setMyPass':		$this->setMyPass();	
				break;
			case 'signUP':		 	$this->signUP();	
				break;
			case 'countUsers':		$this->countUsers();	
				break;
			default:
			   $this->result["Code"] = 1002;
			   $this->result["CodeDetails"] = "wrong section";
			   $this->returnJson($this->result);
			   exit; 
			}
		}
	 
		private function findUser()
		{
			$this->checkEmailIsNotLoggedUser();
			$this->userRequestType = "SELECT"; 
			//sql need it
			$this->sql = "SELECT users.uid AS userID, users.uName AS Name, users.uEmail AS email, role.role AS permission
							FROM users, role
							WHERE users.uEmail= ? AND users.uRole=role.rid";
			$this->requestDatabase(false);
		}

		private function countUsers()
		{
			$this->userRequestType = "SELECT"; 
			//sql need it
			$this->sql = "SELECT COUNT(*) AS adminCount FROM users	WHERE users.urole = '1'";
			$temp = $this->requestDatabase(true);
			$this->result["data"]["adminCount"] = $temp[0]["adminCount"];
			$this->sql = "SELECT COUNT(*) AS labOPCount FROM users	WHERE users.urole = '2'";
			$temp = $this->requestDatabase(true);
			$this->result["data"]["labOPCount"] = $temp[0]["labOPCount"];
			$this->returnJson($this->result);
		}
		
		private function upgradeUser()
		{
			$this->checkEmailIsNotLoggedUser();
			$this->userRequestType = "UPDATE";
			//sql need it
			$this->sql = "UPDATE users SET uRole = '1' WHERE users.uEmail = ?";
			$this->requestDatabase(false);
		}

		private function downgradeUser()
		{
			$this->userRequestType = "UPDATE";
			$this->checkEmailIsNotLoggedUser();
			//sql need it	
			$this->sql = "UPDATE users SET uRole = '2' WHERE users.uEmail = ?";
			$this->requestDatabase(false);
		}
		
		private function deleteUser()
		{
			$this->checkEmailIsNotLoggedUser();
			$this->userRequestType = "DELETE"; 
			//sql need it
			$this->sql = "DELETE FROM users WHERE users.uEmail = ?";
			$this->requestDatabase(false);
		}
		
		private function addUser()
		{
			$this->checkEmailIsNotLoggedUser();
			$this->userRequestType = "INSERT"; 
			$msg = "An account have been created for you on ".$this->dbConfig["Address"]."/nPlease enter and change your temporary password of Test1234";
			mail($this->arrayOfRequest[0],$this->dbConfig["Address"]." Invite",$msg);
	
			//sql need it
			$this->arrayOfRequest[1] = $this->arrayOfRequest[0];
			$this->arrayOfRequest[0] = "TempName";
			$this->arrayOfRequest[2] = md5("Temp1234");
			$this->sql = "INSERT INTO users (`uName`,`uEmail`,`uPass`, `uRole`) VALUES (?,?,?,2)";
			$this->requestDatabase(false);
		}

		private function getMyInfo()
		{
			$this->checkEmailIsLoggedUser();
			$this->userRequestType = "SELECT";
			//sql need it
			$this->sql = "SELECT users.uid AS userID, users.uName AS Name, users.uEmail AS email, role.role AS permission
								FROM users, role
								WHERE users.uEmail= ? AND users.uRole=role.rid";
			$this->requestDatabase(false);
		}

		private function setMyName()
		{
			$this->checkEmailIsLoggedUser();
			$this->checkVariableNotEmpty($this->requests["newName"], "New name");
			$newName = $this->requests["newName"];
			$this->userRequestType = "UPDATE"; 
			
			$this->arrayOfRequest[1] = $this->arrayOfRequest[0];
			$this->arrayOfRequest[0] = $this->requests["newName"];
			$this->sql = "UPDATE users SET uName = ? WHERE users.uEmail = ?";
			$this->counter++;
			$this->requestDatabase(false);
		}
		
		private function setMyPass()
		{
			$this->checkVariableNotEmpty($this->requests["newPass"], "New password");
			$this->checkVariableNotEmpty($this->requests["confPass"], "Confirmation password");
			$this->checkVariableNotEmpty($this->requests["oldPass"], "Old password");
			$oldPass = $this->requests["oldPass"];
			$newPass = $this->requests["newPass"];
			$confPass = $this->requests["confPass"];
			
			$this->userRequestType = "UPDATE";
			
			if(md5($oldPass) != $this->result["loggedUser"]["pass"]){
				$this->result["Code"] = 1010;
				$this->result["CodeDetails"] = "old password is incorrect";
				$this->returnJson($this->result);
				exit;												
			}
			
			if($oldPass == $newPass){
				$this->result["Code"] = 1010;
				$this->result["CodeDetails"] = "New password cannot match old password";
				$this->returnJson($this->result);
				exit;												
			}

			$this->arrayOfRequest[1] = $this->arrayOfRequest[0];
			$this->arrayOfRequest[0] = md5($this->requests["confPass"]);
			$this->sql = "UPDATE users SET uPass = ? WHERE users.uEmail = ?";
			$this->counter++;
			$this->requestDatabase(false);
		}

/*		private function signUP()
		{
			$this->checkVariableNotEmpty($this->requests["name"], "Name");
			$this->checkVariableNotEmpty($this->requests["email"], "Email");
			$this->checkVariableNotEmpty($this->requests["pass"], "New password");
			$this->checkVariableNotEmpty($this->requests["confPass"], "Confirmation password");
			
			$name = $this->requests["name"];
			$email = $this->requests["email"];
			$newPass = $this->requests["pass"];
			$confPass = $this->requests["confPass"];
			
			if($confPass != $newPass){
				$this->result["Code"] = 1010;
				$this->result["CodeDetails"] = "Password does not match";
				$this->returnJson($this->result);
				exit;												
			}

			//check that user exist in invitation list
			$this->arrayOfRequest[0] = $email;
			$this->userRequestType = "SELECT";
			$this->sql = "SELECT userinvite.iexp FROM userinvite WHERE userinvite.iemail= ?";
			if($this->requestDatabase(true) ==null){
				$this->result["Code"] = 1006;
				$this->result["CodeDetails"] = "Invitation not found";
				$this->returnJson($this->result);
				exit;
			}
			
			$this->userRequestType = "INSERT";
			$this->arrayOfRequest[0] = $name;
			$this->arrayOfRequest[1] = $email;
			$this->arrayOfRequest[2] = md5($newPass);
			$this->sql = "INSERT INTO users (`uName`,`uEmail`,`uPass`, `uRole`) VALUES (?,?,?,2)";
			$this->counter = $this->counter +2;
			$this->requestDatabase(false);
		}
*/		 
		//Checks
		private function checkVariableNotEmpty($valueOfVariable, $nameOfVariable)
		{
			//Checking that the variables are not empty and exist, check # 1003, 1004
			if(!(isset ($valueOfVariable))){
				$this->result["Code"] = 1003;
				$this->result["CodeDetails"] = $nameOfVariable. " not found";
				$this->returnJson($this->result);
				exit;						
			}
			else if(empty($valueOfVariable)){
				$this->result["Code"] = 1004;
				$this->result["CodeDetails"] = $nameOfVariable. " cannot be empty";
				$this->returnJson($this->result);
				exit;												
			}
		}

		private function checkEmailIsLoggedUser()
		{
			if($this->arrayOfRequest[0] != $this->result["loggedUser"]["userEmail"]){
			   $this->result["Code"] = 1008;
			   $this->result["CodeDetails"] = "Attempting to get other than the logged user information";
			   $this->returnJson($this->result);
			   exit; 
			}
		}

		private function checkEmailIsNotLoggedUser()
		{
			if($this->arrayOfRequest[0] == $this->result["loggedUser"]["userEmail"]){
			   $this->result["Code"] = 1008;
			   $this->result["CodeDetails"] = "You cannot modify your own information.";
			   $this->returnJson($this->result);
			   exit; 
			}
		}		
		
		private function requestDatabase($returnValue)
		{
			//Try and catch in case there is an error
			try {
				$dbAddress = $this->dbConfig["Address"];
				$dbName = $this->dbConfig["Name"];
				$dbUser = $this->dbConfig["User"];
				$dbPassword = $this->dbConfig["Password"];
				
				$conn = new PDO("mysql:host=$dbAddress;dbname=$dbName", $dbUser, $dbPassword);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$stmt = $conn->prepare($this->sql);
				$stmt->execute($this->arrayOfRequest);
		
				if ($this->userRequestType == "SELECT"){
					// set the resulting array to associative
					$temp = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
					//fetch result into $result
					$temp = ($stmt->fetchAll());	

					if($returnValue == true) return $temp;
					
					//fetch result into $result
					if(($temp)!= null){
						$this->result["data"] = ($temp);
						$this->result["data"] = $this->result["data"][0];
						$this->returnJson($this->result);
						exit;
					}else {
						$this->result["Code"] = 1006;
						$this->result["CodeDetails"] = "user not found";
						$this->returnJson($this->result);
						exit;
					}
				}else{
					if ($stmt->rowCount()<=0){
						$this->result["Code"] = 1006;
						$this->result["CodeDetails"] = "user data has not been updated";
						$this->returnJson($this->result);
						exit;
					}else{$this->returnJson($this->result);}
				}	
			}
			catch(PDOException $e) {//if another error occurred, throw error code 1006
				$this->result["Code"] = 1007;
				$this->result["CodeDetails"] = $e->getMessage();
				$this->returnJson($this->result);
				exit;
			}
		}

		private function returnJson($result)
		{
			$return_values = json_encode($result);
			header('Content-Type: application/json');
			echo $return_values;
		}
	
	}
?>
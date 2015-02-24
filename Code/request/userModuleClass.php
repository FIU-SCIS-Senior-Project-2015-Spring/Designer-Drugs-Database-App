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

		public function __construct($loggedUser, $theRequests, $dbConfig)
		{
			$this->sql = "";
			$this->counter = 1;
			$this->result = array();
			$this->result["Code"] = 2000;
			$this->result["loggedUser"] = $loggedUser;
			$this->arrayOfRequest = array();
			$this->arrayOfRequest[0] = $theRequests["email"]; 
			$this->requests = $theRequests;
			$this->dbConfig = $dbConfig;
			$this->userRequestType = "";

			$this->checkVariableNotEmpty($this->arrayOfRequest[0], "email");
			$this->checkVariableNotEmpty($this->result["loggedUser"], "logged user");
		}
	  
		public function userRequest()
		{
			$this->checkVariableNotEmpty($this->requests["section"], "Section");
			//check if user has admin permission to continue unless requesting its own data
			if($this->requests["section"] != "getMyInfo" && $this->requests["section"] != "setMyName"&& $this->requests["section"] != "setMyPass"&& $this->requests["section"] != "createUser")
				if ($this->result["loggedUser"]["permission"] != "admin") {
					$this->result["Code"] = 1005; 
					$this->result["CodeDetails"] = "user has no permission for this module";
					$this->returnJson($this->result);
					exit;
				}
			//section controller
			
			switch ($this->requests["section"]) {
			case 'findUser':		$this->userRequestType = "SELECT"; $this->findUser(); 				
				break;
			case 'upgradeUser':		$this->userRequestType = "UPDATE"; $this->upgradeUser(); 	
				break;
			case 'downgradeUser':	$this->userRequestType = "UPDATE"; $this->downgradeUser();	
				break;
			case 'deleteUser': 		$this->userRequestType = "DELETE"; $this->deleteUser(); 
				break;
			case 'addUser':  		$this->userRequestType = "INSERT"; $this->addUser(); 
				break;
			case 'getMyInfo': 		$this->userRequestType = "SELECT"; $this->getMyInfo(); 
				break;
			case 'setMyName': 		$this->userRequestType = "UPDATE"; $this->setMyName($this->requests["newName"]);	 
				break;
			case 'setMyPass':		$this->userRequestType = "UPDATE"; $this->setMyPass($this->requests["oldPass"],$this->requests["newPass"],$this->requests["confPass"]);	
				break;
			case 'createUser':		$this->userRequestType = "INSERT"; $this->createUser($this->requests["name"],$this->requests["email"],$this->requests["pass"],$this->requests["confPass"]);	
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
			//sql need it
			$this->sql = "SELECT users.uid AS userID, users.uName AS Name, users.uEmail AS email, role.role AS permission
							FROM users, role
							WHERE users.uEmail= ? AND users.uRole=role.rid";
			$this->requestDatabase(false);
		}
		
		private function upgradeUser()
		{					
			$this->checkEmailIsNotLoggedUser();
			//sql need it
			$this->sql = "UPDATE users SET uRole = '1' WHERE users.uEmail = ?";
			$this->requestDatabase(false);
		}

		private function downgradeUser()
		{				
			$this->checkEmailIsNotLoggedUser();
			//sql need it	
			$this->sql = "UPDATE users SET uRole = '2' WHERE users.uEmail = ?";
			$this->requestDatabase(false);
		}
		
		private function deleteUser()
		{
			$this->checkEmailIsNotLoggedUser();
			//sql need it
			$this->sql = "DELETE FROM users WHERE users.uEmail = ?";
			$this->requestDatabase(false);
		}
		
		private function addUser()
		{
			$this->checkEmailIsNotLoggedUser();
			$date = date("Y-m-d", strtotime("now+2 days")); 
			$msg = "You have been invited to join ".$this->dbConfig["Address"]."/nPlease accept this offer before: ".$date."/n by accessing this link: ";
			$msg .= $this->dbConfig["Address"]."/newUser";
			mail($this->arrayOfRequest[0],$this->dbConfig["Address"]." Invite",$msg);
			$this->arrayOfRequest[1] = $date;
			
			//sql need it
			$this->sql = "INSERT INTO userinvite (`iemail`, `iexp`) VALUES (?,?)";
			$this->counter++;
			$this->requestDatabase(false);
		}

		private function getMyInfo()
		{
			$this->checkEmailIsLoggedUser();
			
			//sql need it
			$this->sql = "SELECT users.uid AS userID, users.uName AS Name, users.uEmail AS email, role.role AS permission
								FROM users, role
								WHERE users.uEmail= ? AND users.uRole=role.rid";
			$this->requestDatabase(false);
		}

		private function setMyName($newName)
		{
			$this->checkEmailIsLoggedUser();
			$this->checkVariableNotEmpty($newName, "New name");
			$this->arrayOfRequest[1] = $this->arrayOfRequest[0];
			$this->arrayOfRequest[0] = $this->requests["newName"];
			$this->sql = "UPDATE users SET uName = ? WHERE users.uEmail = ?";
			$this->counter++;
			$this->requestDatabase(false);
		}
		
		private function setMyPass($oldPass, $newPass, $confPass)
		{
			$this->checkVariableNotEmpty($newPass, "New password");
			$this->checkVariableNotEmpty($confPass, "Confirmation password");
			$this->checkVariableNotEmpty($oldPass, "Old password");
			
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

		private function createUser($name, $email, $newPass, $confPass)
		{
			$this->checkVariableNotEmpty($name, "Name");
			$this->checkVariableNotEmpty($email, "Email");
			$this->checkVariableNotEmpty($newPass, "New password");
			$this->checkVariableNotEmpty($confPass, "Confirmation password");
			
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
<?php
	/* By convention the variable $request must come with an email ans with a section.
	* email specifies the user that is to be modify or changed
	* section specifies what is to be done to that user,
	* lastly also any other variable need it for each special case.
	*/	
	class msgModule
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
			
			$this->checkVariableNotEmpty($this->result["loggedUser"], "logged user");
		}
	  
		public function userRequest()
		{
			$this->checkVariableNotEmpty($this->requests["section"], "Section");
			//check if user has admin permission to continue unless requesting its own data
			if($this->requests["section"] != "newMsg")
				if (($this->result["loggedUser"]["permission"] != "admin") && ($this->result["loggedUser"]["permission"] != "labOP")) {
					$this->result["Code"] = 1005; 
					$this->result["CodeDetails"] = "user has no permission for this module";
					$this->returnJson($this->result);
					exit;
				}
			//section controller
			
			switch ($this->requests["section"]) {
			case 'deleteMsg': 	$this->deleteMsg(); 
				break;
			case 'newMsg':  	$this->newMsg(); 
				break;
			case 'newReply':  	$this->newReply(); 
				break;
			case 'getMsg': 		$this->getMsg(); 
				break;
			case 'modMsg': 		$this->modMsg(); 
				break;
			case 'countMsg': 	$this->countMsg(); 
				break;				
			default:
			   $this->result["Code"] = 1002;
			   $this->result["CodeDetails"] = "wrong section";
			   $this->returnJson($this->result);
			   exit; 
			}
		}
	 		
		private function deleteMsg()
		{
			$this->checkVariableNotEmpty($this->requests["mid"], "Message");
			$this->arrayOfRequest[0] = $this->requests["mid"];
			$this->userRequestType = "DELETE";
			//sql need it
			$this->sql = "DELETE FROM messages WHERE messages.mid = ?";
			$this->requestDatabase(false);
		}
		
		private function countMsg()
		{
			$this->userRequestType = "SELECT"; 
			//sql need it
			$this->sql = "SELECT COUNT(*) AS rmsgCount FROM messages	WHERE messages.mread = '1'";
			$temp = $this->requestDatabase(true);
			$this->result["data"]["rmsgCount"] = $temp[0]["rmsgCount"];
			$this->sql = "SELECT COUNT(*) AS umsgCount FROM messages	WHERE messages.mread = '0'";
			$temp = $this->requestDatabase(true);
			$this->result["data"]["umsgCount"] = $temp[0]["umsgCount"];
			$this->returnJson($this->result);
		}
		
		private function newMsg()
		{
			$this->checkVariableNotEmpty($this->requests["from"], "Sender");
			$this->checkVariableNotEmpty($this->requests["to"], "Recipient");
			$this->checkVariableNotEmpty($this->requests["subject"], "Subject");
			$this->checkVariableNotEmpty($this->requests["text"], "Message body");
			$this->userRequestType = "INSERT"; 
			
			$this->arrayOfRequest[0] = $this->requests["from"];
			$this->arrayOfRequest[1] = $this->requests["to"];
			$this->arrayOfRequest[2] = $this->requests["subject"];
			$this->arrayOfRequest[3] = $this->requests["text"];
			//sql need it
			$this->sql = "INSERT INTO messages (`mfrom`,`mto`, `msubject`, `mtext`) VALUES (?, ?, ?, ?)";
			$this->requestDatabase(false);
		}
		
		private function newReply()
		{
			$this->checkVariableNotEmpty($this->requests["to"], "Recipient");
			$this->checkVariableNotEmpty($this->requests["subject"], "Subject");
			$this->checkVariableNotEmpty($this->requests["text"], "Message body");
			$this->arrayOfRequest[0] = $this->requests["to"];
			$this->arrayOfRequest[1] = $this->requests["subject"];
			$this->arrayOfRequest[2] = $this->requests["text"];
			mail($this->arrayOfRequest[0],$this->arrayOfRequest[1],$this->arrayOfRequest[2]);
			$this->returnJson($this->result);
		}

		private function getMsg()
		{
			$this->userRequestType = "SELECT"; 
			//sql need it
			$this->sql = "SELECT * FROM messages WHERE messages.mto = 'admin'";
			$this->requestDatabase(false);
		}
		
		private function modMsg()
		{
			$this->checkVariableNotEmpty($this->requests["mid"], "Message");
			$this->userRequestType = "UPDATE"; 
			
			$this->arrayOfRequest[0] = $this->requests["mid"];
			//sql need it
			$this->sql = "UPDATE messages SET mread=1 WHERE messages.mid = ?";
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
				
				if(count($this->arrayOfRequest)>0 && count($this->arrayOfRequest[0])>1)
					foreach ($this->arrayOfRequest as $item)
						$stmt->execute($item);
				else
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
						//if(count($this->result["data"]) == 1) $this->result["data"] = $this->result["data"][0];
						$this->returnJson($this->result);
						exit;
					}else {
						$this->result["Code"] = 1006;
						$this->result["CodeDetails"] = "compound not found";
						$this->returnJson($this->result);
						exit;
					}
				}else{
					if($this->userRequestType == "INSERT"){			
						$this->result["data"]["lastRecordId"] = $conn->lastInsertId();
						if($returnValue == true) return $this->result["data"]["lastRecordId"];
					}
					
					if ($stmt->rowCount()<=0){
						$this->result["Code"] = 1006;
						$this->result["CodeDetails"] = "user data has not been updated";
						$this->returnJson($this->result);
						exit;
					}else{
						if($returnValue == true) return;
						$this->returnJson($this->result);
					}
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
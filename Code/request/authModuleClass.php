<?php
	class authModule
	{
		private $sql;
		private $result;
		private $arrayOfRequest;
		private $requests;
		private $dbConfig;

		public function __construct($theRequests, $dbConfig)
		{
			$this->sql = "";
			$this->result = array();
			$this->result["Code"] = 2000;
			$this->arrayOfRequest = array();
			$this->requests = array();
			$this->requests = $theRequests;
			$this->dbConfig = $dbConfig;
		}

		public function authRequest()
		{
			//section controller
			 $this->checkVariableNotEmpty($this->requests["section"], "section");
			
			switch ($this->requests["section"]) {
			case 'testLoginInfo':	$this->testLoginInfo($this->requests["email"], $this->requests["password"]);				
				break;
			case 'testSesionInfo':	return $this->testSesionInfo();	
				break;
			case 'logOut':	$this->logOut();	
				break;			
			default:
			   $this->result["Code"] = 1002;
			   $this->result["CodeDetails"] = "wrong section, section cannot be ". $this->requests["section"].".";
			   $this->returnJson($this->result);
			   exit; 
			}
		}

		
		private function testLoginInfo($email,$pass)
		{
			$this->arrayOfRequest[0] = $email;
			$this->checkVariableNotEmpty($this->arrayOfRequest[0], "Email");
			$this->checkVariableNotEmpty($pass, "Password");	
			$this->arrayOfRequest[1] = md5($pass);
			$this->arrayOfRequest[1] = md5($pass);
			
			
			$this->sql = "SELECT users.uid as userID, users.uName AS Name, users.uEmail AS email, role.role AS permission
							FROM users, role
							WHERE users.uEmail= ? AND users.uPass= ? AND users.uRole=role.rid";
			$this->requestDatabase();			

			//create session
			session_start();
			//fill in session variables
			$_SESSION["userEmail"] = $this->result["loggedIn"]['email'];
			$_SESSION["userID"] = $this->result["loggedIn"]['userID'];
			$this->returnJson($this->result);
		}
		
		private function testSesionInfo()
		{
			session_start();

			$this->arrayOfRequest[0] = $_SESSION["userEmail"];
			$this->arrayOfRequest[1] = $_SESSION["userID"];
			
			$this->checkVariableNotEmpty($this->arrayOfRequest[0],"Session");
			$this->checkVariableNotEmpty($this->arrayOfRequest[1],"Session");	
			
			$this->sql = "SELECT users.uid as userID, users.uEmail as userEmail,users.uPass as pass, role.role AS permission
								FROM users, role
								WHERE users.uEmail= ? AND users.uid= ? AND users.uRole=role.rid";
			$this->requestDatabase();
			return $this->result;
		}
		
		private function logOut()
		{
			session_start();
			session_destroy();
			$this->returnJson($this->result);
		}
		
		//Checks
		private function checkVariableNotEmpty($valueOfVariable, $nameOfVariable)
		{
			//Checking that the variables are not empty and exist, check # 1003, 1004
			if(!(isset ($valueOfVariable))){
				$this->$result["Code"] = 1003;
				$this->$result["CodeDetails"] = $nameOfVariable. " not found";
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
		
		private function requestDatabase()
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
				
				// set the resulting array to associative
				$temp = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
				//fetch result into $result
				$temp = ($stmt->fetchAll());	

				//fetch result into $result
				if(($temp)!= null){
					$this->result["loggedIn"] = ($temp);
					$this->result["loggedIn"] = $this->result["loggedIn"][0];
				}else {
					$this->result["Code"] = 1006;
					$this->result["CodeDetails"] = "user not found";
					$this->returnJson($this->result);
					exit;
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
<?php
	/* By convention the variable $request must come with an email ans with a section.
	* email specifies the user that is to be modify or changed
	* section specifies what is to be done to that user,
	* lastly also any other variable need it for each special case.
	*/	
	class compModule
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
			$this->requests = $theRequests;
			$this->dbConfig = $dbConfig;
			$this->userRequestType = "";
			
			//$this->checkVariableNotEmpty($this->result["loggedUser"], "logged user");
		}
	  
		public function compRequest()
		{
			$this->checkVariableNotEmpty($this->requests["section"], "Section");
			//check if user has admin permission to continue unless requesting its own data
			if($this->requests["section"] != "getComp" && $this->requests["section"] != "getClass"&& $this->requests["section"] != "getPrec")
				if ($this->result["loggedUser"]["permission"] != "admin") {
					$this->result["Code"] = 1005; 
					$this->result["CodeDetails"] = "user has no permission for this module";
					$this->returnJson($this->result);
					exit;
				}
			//section controller
			
			switch ($this->requests["section"]) {
			case 'getComp':		$this->userRequestType = "SELECT"; $this->getComp(); 				
				break;
			case 'addComp':		$this->userRequestType = "INSERT"; $this->addComp(); 	
				break;
			case 'editComp':	$this->userRequestType = "UPDATE"; $this->editComp();	
				break;
			case 'deleteComp':	$this->userRequestType = "DELETE"; $this->deleteComp();	
				break;				
			case 'impComp':		$this->userRequestType = "INSERT"; $this->impComp();	
				break;
			case 'expComp':		$this->userRequestType = "SELECT"; $this->expComp();	
				break;
			case 'getClass':	$this->userRequestType = "SELECT"; $this->getClass(); 				
				break;
			case 'addClass':	$this->userRequestType = "INSERT"; $this->addClass(); 	
				break;
			case 'editClass':	$this->userRequestType = "UPDATE"; $this->editClass();	
				break;
			case 'deleteClass':	$this->userRequestType = "DELETE"; $this->deleteClass();	
				break;				
			case 'getPrec':	$this->userRequestType = "SELECT"; $this->getPrec(); 				
				break;
			case 'addPrec':	$this->userRequestType = "INSERT"; $this->addPrec(); 	
				break;
			case 'editPrec':	$this->userRequestType = "UPDATE"; $this->editPrec();	
				break;
			case 'deletePrec':	$this->userRequestType = "DELETE"; $this->deletePrec();	
				break;				
				
			default:
			   $this->result["Code"] = 1002;
			   $this->result["CodeDetails"] = "wrong section";
			   $this->returnJson($this->result);
			   exit; 
			}
		}
		
		private function getComp()
		{
			$this->checkVariableNotEmpty($this->requests["searchCriteria"], "Search criteria");
			//sql need it
			$this->sql = "SELECT * FROM compounds WHERE compounds.cName= ? OR compounds.cFormula=? OR compounds.cOName = ?";
			$this->arrayOfRequest[0] = $this->requests["searchCriteria"];
			$this->arrayOfRequest[1] = $this->requests["searchCriteria"];
			$this->arrayOfRequest[2] = $this->requests["searchCriteria"];
			$this->requestDatabase(false);
		}		

		private function addComp()
		{
			//sql need it
			$this->sql = "INSERT INTO compounds (`cName`, `cFormula`, `cOName`, `cMass`, `cPrecursor`, `cFrag`, `cRT`, `cCAS`, `cClass`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$this->arrayOfRequest[0] = $requests["cName"];
			$this->arrayOfRequest[1] = $requests["cFormula"];
			$this->arrayOfRequest[2] = $requests["cOName"];
			$this->arrayOfRequest[3] = $requests["cMass"];
			$this->arrayOfRequest[4] = $requests["cPrecursor"];
			$this->arrayOfRequest[5] = $requests["cFrag"];
			$this->arrayOfRequest[6] = $requests["cRT"];
			$this->arrayOfRequest[7] = $requests["cCAS"];
			$this->arrayOfRequest[8] = $requests["cClass"];
			
			$this->checkVariableNotEmpty($requests["cName"], "Name");
			$this->checkVariableNotEmpty($requests["cFormula"], "Formula");
			$this->checkVariableNotEmpty($requests["cOName"], "Other Names");
			$this->checkVariableNotEmpty($requests["cMass"], "Mass");
			$this->checkVariableNotEmpty($requests["cPrecursor"], "Precursor");
			$this->checkVariableNotEmpty($requests["cFrag"], "Frag");
			$this->checkVariableNotEmpty($requests["cRT"], "RT");
			$this->checkVariableNotEmpty($requests["cCAS"], "CAS");
			$this->checkVariableNotEmpty($requests["cClass"], "Class");
			
			$this->requestDatabase(false);			
		}		

		//better if it is split into different catefories, one per each item to be changed
		private function editComp()
		{
			
		}	

		private function deleteComp()
		{
			
		}	

		private function impComp()
		{
		}	

		private function expComp()
		{
		}	

		private function getClass()
		{
		}		

		private function addClass()
		{
		}		

		private function editClass()
		{
		}	

		private function deleteClass()
		{
		}	

		private function getPrec()
		{
		}		

		private function addPrec()
		{
		}		

		private function editPrec()
		{
		}	

		private function deletePrec()
		{
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
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
		}
	  
		public function compRequest()
		{
			$this->checkVariableNotEmpty($this->requests["section"], "Section");
			//check if user has admin permission to continue unless requesting its own data
			if($this->requests["section"] != "getComp" && $this->requests["section"] != "getClass"&& $this->requests["section"] != "getPrec") {
					//load authModule class
					include("authModuleClass.php");	

					////////////check session///////////
					//setup to call the authmodule
					$tempSection = $this->requests;
					$tempSection["section"] = "testSesionInfo";					

					//call authModule
					$myauth = new authModule($tempSection, $this->dbConfig);
					$loggedIn = $myauth->authRequest();
					$this->result["loggedUser"] = $loggedIn["loggedIn"];
										
					$this->checkVariableNotEmpty($this->result["loggedUser"], "login information");
				
				if ((!isset($this->result["loggedUser"]["permission"]))||($this->result["loggedUser"]["permission"] != "admin")) {					
					$this->result["Code"] = 1005; 
					$this->result["CodeDetails"] = "user has no permission for this module";
					$this->returnJson($this->result);
					exit;
				}
			}
			//section controller
		
			switch ($this->requests["section"]) {
			case 'getComp':		$this->userRequestType = "SELECT"; $this->getComp(); 				
				break;
			case 'getCompbyId':	$this->userRequestType = "SELECT"; $this->getCompbyId(); 				
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
			case 'getClassCount':	$this->userRequestType = "SELECT"; $this->getClassCount(); 				
				break;
			case 'addClass':	$this->userRequestType = "INSERT"; $this->addClass(); 	
				break;
			case 'editClass':	$this->userRequestType = "UPDATE"; $this->editClass();	
				break;
			case 'deleteClass':	$this->userRequestType = "DELETE"; $this->deleteClass();	
				break;				
			case 'getTrans':	$this->userRequestType = "SELECT"; $this->getTrans(); 				
				break;
			case 'addTrans':	$this->userRequestType = "INSERT"; $this->addTrans($this->requests["Trans"][0]["cid"]); 	
				break;
			case 'editTrans':	$this->userRequestType = "UPDATE"; $this->editTrans();	
				break;
			case 'deleteTrans':	$this->userRequestType = "DELETE"; $this->deleteTrans();	
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
		
		private function getCompbyId()
		{
			$this->checkVariableNotEmpty($this->requests["id"], "id");
			//sql need it
			$this->sql = "SELECT * FROM compounds WHERE compounds.cid= ?";
			$this->arrayOfRequest[0] = $this->requests["id"];
			$this->requestDatabase(false);
		}
		
		private function addComp()
		{
			//sql need it
			$this->sql = "INSERT INTO compounds (`cName`, `cFormula`, `cOName`, `cMass`, `cPrecursor`, `cFrag`, `cRT`, `cCAS`, `cClass`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$this->arrayOfRequest[0] = $this->requests["cName"];
			$this->arrayOfRequest[1] = $this->requests["cFormula"];
			$this->arrayOfRequest[2] = $this->requests["cOName"];
			$this->arrayOfRequest[3] = $this->requests["cMass"];
			$this->arrayOfRequest[4] = $this->requests["cPrecursor"];
			$this->arrayOfRequest[5] = $this->requests["cFrag"];
			$this->arrayOfRequest[6] = $this->requests["cRT"];
			$this->arrayOfRequest[7] = $this->requests["cCAS"];
			$this->arrayOfRequest[8] = $this->requests["cClass"];
			
			$this->checkVariableNotEmpty($this->requests["cName"], "Name");
			$this->checkVariableNotEmpty($this->requests["cFormula"], "Formula");
			$this->checkVariableNotEmpty($this->requests["cOName"], "Other Names");
			$this->checkVariableNotEmpty($this->requests["cMass"], "Mass");
			$this->checkVariableNotEmpty($this->requests["cFrag"], "Frag");
			$this->checkVariableNotEmpty($this->requests["cRT"], "RT");
			$this->checkVariableNotEmpty($this->requests["cPrecursor"], "Precursor");
			$this->checkVariableNotEmpty($this->requests["cCAS"], "CAS");
			$this->checkVariableNotEmpty($this->requests["cClass"], "Class");

			$this->addTrans($this->requestDatabase(true));
		}		

		//better if it is split into different catefories, one per each item to be changed
		private function editComp()
		{
						//sql need it
			$this->sql = "UPDATE compounds SET cName=?, cFormula=?, cOName=?, cMass=?, cPrecursor=?, cFrag=?, cRT=?, cCAS=?, cClass=? WHERE compounds.cid = ?";
			$this->arrayOfRequest[0] = $this->requests["cName"];
			$this->arrayOfRequest[1] = $this->requests["cFormula"];
			$this->arrayOfRequest[2] = $this->requests["cOName"];
			$this->arrayOfRequest[3] = $this->requests["cMass"];
			$this->arrayOfRequest[4] = $this->requests["cPrecursor"];
			$this->arrayOfRequest[5] = $this->requests["cFrag"];
			$this->arrayOfRequest[6] = $this->requests["cRT"];
			$this->arrayOfRequest[7] = $this->requests["cCAS"];
			$this->arrayOfRequest[8] = $this->requests["cClass"];
			$this->arrayOfRequest[9] = $this->requests["cid"];
			
			$this->checkVariableNotEmpty($this->requests["cName"], "Name");
			$this->checkVariableNotEmpty($this->requests["cFormula"], "Formula");
			$this->checkVariableNotEmpty($this->requests["cOName"], "Other Names");
			$this->checkVariableNotEmpty($this->requests["cMass"], "Mass");
			$this->checkVariableNotEmpty($this->requests["cFrag"], "Frag");
			$this->checkVariableNotEmpty($this->requests["cRT"], "RT");
			$this->checkVariableNotEmpty($this->requests["cPrecursor"], "Precursor");
			$this->checkVariableNotEmpty($this->requests["cCAS"], "CAS");
			$this->checkVariableNotEmpty($this->requests["cClass"], "Class");
			$this->checkVariableNotEmpty($this->requests["cid"], "Compound Id");
			$this->requestDatabase(false);
		}	

		private function deleteComp()
		{
			$this->checkVariableNotEmpty($this->requests["cid"], "Compound id");
			$this->arrayOfRequest[0] = $this->requests["cid"];
			$this->sql = "DELETE FROM transition WHERE transition.cid = ?";
			$this->requestDatabase(true);
			$this->sql = "DELETE FROM compounds WHERE compounds.cid = ?";
			$this->requestDatabase(false);
		}	

		private function impComp()
		{
		}	

		private function expComp()
		{
		}	

		private function getClass()
		{
			$this->sql = "SELECT cid AS id, class AS label FROM class";
			$this->requestDatabase(false);
		}

		private function getClassCount()
		{
			$this->checkVariableNotEmpty($this->requests["cClass"], "Class");
			$this->sql = "SELECT COUNT(*) AS count FROM compounds WHERE compounds.cClass = ?";
			$this->arrayOfRequest[0] = $this->requests["cClass"];
			$this->requestDatabase(false);
		}

		private function addClass()
		{
			$this->checkVariableNotEmpty($this->requests["cClass"], "Class");
			$this->sql = "INSERT INTO class ( `class`) VALUES (?)";
			$this->arrayOfRequest[0] = $this->requests["cClass"];
			$this->requestDatabase(false);
		}		

		private function editClass()
		{
			$this->checkVariableNotEmpty($this->requests["cClass"], "Class");
			$this->checkVariableNotEmpty($this->requests["cid"], "Class id");
			$this->sql = "UPDATE class SET class=? WHERE class.cid = ?";
			$this->arrayOfRequest[0] = $this->requests["cClass"];
			$this->arrayOfRequest[1] = $this->requests["cid"];
			$this->requestDatabase(false);
		}	

		private function deleteClass()
		{
			$this->checkVariableNotEmpty($this->requests["cid"], "Class id");			
			$this->sql = "DELETE FROM class WHERE class.cid = ?";
			$this->arrayOfRequest[0] = $this->requests["cid"];			
			$this->requestDatabase(false);
		}	

		private function getTrans()
		{
			$this->checkVariableNotEmpty($this->requests["CompId"], "Compound id");
			//sql need it
			$this->sql = "SELECT * FROM transition WHERE transition.cid= ?";
			$this->arrayOfRequest[0] = $this->requests["CompId"];
			$this->requestDatabase(false);			
		}		

		private function addTrans($cid)
		{
			$this->userRequestType == "INSERT";
			//sql need it
			$this->sql = "INSERT INTO transition ( `cid`, `tProduct`, `tCE`, `tAbundance`, `tRIInt`) VALUES (?, ?, ?, ?, ?)";
			$this->arrayOfRequest = [];
			
			for ($x = 0; $x < count($this->requests["Trans"]); $x++) {
				$this->requests["Trans"][$x]["cid"] = $cid;
				$this->checkVariableNotEmpty($this->requests["Trans"][$x]["product"], "Product for Transition ".$x);
				$this->checkVariableNotEmpty($this->requests["Trans"][$x]["CE"], "CE for Transition ".$x);
				$this->checkVariableNotEmpty($this->requests["Trans"][$x]["Abd"], "Abundance for Transition ".$x);
				$this->checkVariableNotEmpty($this->requests["Trans"][$x]["Intens"], "Intensity for Transition ".$x);
				
				$this->arrayOfRequest[$x] = [];
				$this->arrayOfRequest[$x][0] = $this->requests["Trans"][$x]["cid"];
				$this->arrayOfRequest[$x][1] = $this->requests["Trans"][$x]["product"];
				$this->arrayOfRequest[$x][2] = $this->requests["Trans"][$x]["CE"];
				$this->arrayOfRequest[$x][3] = $this->requests["Trans"][$x]["Abd"];
				$this->arrayOfRequest[$x][4] = $this->requests["Trans"][$x]["Intens"];
			}
			
			if (count($this->requests["Trans"])>0){
				$this->requestDatabase(false);	
			}else 
				$this->returnJson($this->result);
		}		

		private function editTrans()
		{
			$this->checkVariableNotEmpty($this->requests["Transid"], "Id of transition ");
			$this->checkVariableNotEmpty($this->requests["product"], "Product ");
			$this->checkVariableNotEmpty($this->requests["CE"], "CE ");
			$this->checkVariableNotEmpty($this->requests["Abd"], "Abundance ");
			$this->checkVariableNotEmpty($this->requests["Intens"], "Intensity ");

			$this->sql = "UPDATE transition SET tProduct=?, tCE=?, tAbundance=?, tRIInt=? WHERE transition.tid = ?";

			$this->arrayOfRequest[0] = $this->requests["product"];
			$this->arrayOfRequest[1] = $this->requests["CE"];
			$this->arrayOfRequest[2] = $this->requests["Abd"];
			$this->arrayOfRequest[3] = $this->requests["Intens"];
			$this->arrayOfRequest[4] = $this->requests["Transid"];
			$this->requestDatabase(false);	
		}	

		private function deleteTrans()
		{
			$this->checkVariableNotEmpty($this->requests["Transid"], "Id of transition ");
			$this->sql = "DELETE FROM transition WHERE transition.tid = ?";
			$this->arrayOfRequest[0] = $this->requests["Transid"];
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
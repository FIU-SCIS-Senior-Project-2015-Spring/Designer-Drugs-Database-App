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
			if($this->requests["section"] != "getComp" &&$this->requests["section"] != "getCompbyId" && $this->requests["section"] != "getClass" && $this->requests["section"] != "getTrans" && $this->requests["section"] != "getPrec"&& $this->requests["section"] != "expComp") {
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
				
				if (!isset($this->result["loggedUser"]["permission"])) {					
					$this->result["Code"] = 1005; 
					$this->result["CodeDetails"] = "user has no permission for this module";
					$this->returnJson($this->result);
					exit;
				}
			}
			//section controller
		
			switch ($this->requests["section"]) {
			case 'getComp':		$this->getComp(); 				
				break;
			case 'getCompbyId':	$this->getCompbyId(); 				
				break;
			case 'addComp':		$this->addComp(); 	
				break;
			case 'editComp':	$this->editComp();	
				break;
			case 'deleteComp':	$this->deleteComp();	
				break;				
			case 'impPics':		$this->impPics();	
				break;
			case 'impComp':		$this->impComp();	
				break;
			case 'getTrans':	$this->getTrans(); 				
				break;
			case 'addTrans':	$this->addTrans(); 	
				break;
			case 'editTrans':	$this->editTrans();	
				break;
			case 'deleteTrans':	$this->deleteTrans();	
				break;				
			case 'countComp':	$this->countComp();	
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
			$this->userRequestType = "SELECT"; 
			//sql need it
			$this->sql = "SELECT * FROM compounds WHERE compounds.cName= ? OR compounds.cFormula=? OR compounds.cOName = ?";
			$this->arrayOfRequest[0] = $this->requests["searchCriteria"];
			$this->arrayOfRequest[1] = $this->requests["searchCriteria"];
			$this->arrayOfRequest[2] = $this->requests["searchCriteria"];
			$this->requestDatabase(false);
		}
		
		private function countComp()
		{
			$this->checkisAdminOrLabOP();
			$this->userRequestType = "SELECT";
			
			$this->sql = "SELECT COUNT(*) AS compCount FROM compounds";
			$temp = $this->requestDatabase(true);
			$this->result["data"]["compCount"] = $temp[0]["compCount"];
			$this->returnJson($this->result);
		}
		
		private function getCompbyId()
		{
			$this->checkVariableNotEmpty($this->requests["id"], "id");
			$this->userRequestType = "SELECT"; 
			//sql need it
			$this->sql = "SELECT * FROM compounds WHERE compounds.cid= ?";
			$this->arrayOfRequest[0] = $this->requests["id"];
			$this->requestDatabase(false);
		}
		
		private function addComp()
		{
			$this->checkisAdminOrLabOP();
			$this->userRequestType = "INSERT";
			//sql need it
			$this->sql = "INSERT INTO compounds (`cName`, `cFormula`, `cOName`, `cMass`, `cPrecursor`, `cFrag`, `cCAV`, `cCAS`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
			$this->checkVariableNotEmpty($this->requests["cName"], "Name");
			$this->checkVariableNotEmpty($this->requests["cFormula"], "Formula");
			$this->checkVariableNotEmpty($this->requests["cOName"], "Other Names");
			$this->checkVariableNotEmpty($this->requests["cMass"], "Mass");
			$this->checkVariableNotEmpty($this->requests["cFrag"], "Frag");
			$this->checkVariableNotEmpty($this->requests["cCAV"], "CAV");
			$this->checkVariableNotEmpty($this->requests["cPrecursor"], "Precursor");
			$this->checkVariableNotEmpty($this->requests["cCAS"], "CAS");

			$this->arrayOfRequest[0] = $this->requests["cName"];
			$this->arrayOfRequest[1] = $this->requests["cFormula"];
			$this->arrayOfRequest[2] = $this->requests["cOName"];
			$this->arrayOfRequest[3] = $this->requests["cMass"];
			$this->arrayOfRequest[4] = $this->requests["cPrecursor"];
			$this->arrayOfRequest[5] = $this->requests["cFrag"];
			$this->arrayOfRequest[6] = $this->requests["cCAV"];
			$this->arrayOfRequest[7] = $this->requests["cCAS"];

			$this->addTranswithCompID($this->requestDatabase(true));
		}		

		//better if it is split into different catefories, one per each item to be changed
		private function editComp()
		{
			$this->checkisAdminOrLabOP();
			$this->userRequestType = "UPDATE";
			//sql need it
			$this->sql = "UPDATE compounds SET cName=?, cFormula=?, cOName=?, cMass=?, cPrecursor=?, cFrag=?, cCAV=?, cCAS=? WHERE compounds.cid = ?";

			$this->checkVariableNotEmpty($this->requests["cName"], "Name");
			$this->checkVariableNotEmpty($this->requests["cFormula"], "Formula");
			$this->checkVariableNotEmpty($this->requests["cOName"], "Other Names");
			$this->checkVariableNotEmpty($this->requests["cMass"], "Mass");
			$this->checkVariableNotEmpty($this->requests["cPrecursor"], "Precursor");
						$this->checkVariableNotEmpty($this->requests["cFrag"], "Frag");
			$this->checkVariableNotEmpty($this->requests["cCAV"], "CAV");
			$this->checkVariableNotEmpty($this->requests["cCAS"], "CAS");
			$this->checkVariableNotEmpty($this->requests["cid"], "Compound Id");
			
			$this->arrayOfRequest[0] = $this->requests["cName"];
			$this->arrayOfRequest[1] = $this->requests["cFormula"];
			$this->arrayOfRequest[2] = $this->requests["cOName"];
			$this->arrayOfRequest[3] = $this->requests["cMass"];
			$this->arrayOfRequest[4] = $this->requests["cPrecursor"];
			$this->arrayOfRequest[5] = $this->requests["cFrag"];
			$this->arrayOfRequest[6] = $this->requests["cCAV"];
			$this->arrayOfRequest[7] = $this->requests["cCAS"];
			$this->arrayOfRequest[9] = $this->requests["cid"];
			
			$this->requestDatabase(false);
		}	

		private function deleteComp()
		{
			$this->checkisAdminOrLabOP();
			$this->checkVariableNotEmpty($this->requests["cid"], "Compound id");
			$this->userRequestType = "DELETE";
			
			$this->arrayOfRequest[0] = $this->requests["cid"];
			$this->sql = "DELETE FROM transition WHERE transition.cid = ?";
			$this->requestDatabase(true);
			$this->sql = "DELETE FROM compounds WHERE compounds.cid = ?";
			$this->requestDatabase(false);
		}	

		
////////////IMPORT COMPOUND///////////		
		private function impComp()
		{
			$this->checkisAdmin();
			$this->userRequestType = "INSERT";
			
			$dbAddress = $this->dbConfig["Address"];
			$dbName = $this->dbConfig["Name"];
			$dbUser = $this->dbConfig["User"];
			$dbPassword = $this->dbConfig["Password"];
			$csvMimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
			
			$db = new PDO("mysql:host=$dbAddress;dbname=$dbName", $dbUser, $dbPassword);
				
			if(!isset($_FILES["file"])){
				$this->result["Code"] = 3005; 
				$this->result["CodeDetails"] = "No file found.";
				$this->returnJson($this->result);
				exit;
			}

			if(!in_array($_FILES['file']['type'],$csvMimes)){
				$this->result["Code"] = 3005; 
				$this->result["CodeDetails"] = "File is not a csv file, please import a file with csv extension";
				$this->returnJson($this->result);			
			}			
			
			$fileUpl = $_FILES["file"];
			if ( !$fileUpl["error"] ){
				$file = fopen($fileUpl["tmp_name"], "r");
			} else{
				$this->result["Code"] = 3005; 
				$this->result["CodeDetails"] = "No file found.";
				$this->returnJson($this->result);
				exit;
			}
						
			//Prepared statements for class
			//$psClassCheck = $db->prepare("SELECT cid FROM class WHERE class=:class");
			//$psClassInsert = $db->prepare("INSERT INTO class (class) VALUES (:class)");
			//Prepared statements for compounds
			$psCompoundsCheck = $db->prepare("SELECT cid FROM compounds WHERE cOName=:cOName");
			/*
			$psCompoundsInsert = $db->prepare("INSERT INTO compounds (cName, cFormula, cOName, cMass, cPrecursor, cFrag, cCAS, cClass, cayman) 
															VALUES (:cName, :cFormula, :cOName, :cMass, :cPrecursor, :cFrag, :cCAS, :cClass, :cayman)");
			*/
			$psCompoundsInsert = $db->prepare("INSERT INTO compounds (cName, cFormula, cOName, cMass, cPrecursor, cFrag, cCAS, cCAV) 
															VALUES (:cName, :cFormula, :cOName, :cMass, :cPrecursor, :cFrag, :cCAS, :CAV)");
			//Prepared statements for transition
			$psTransitionInsert = $db->prepare("INSERT INTO transition (cid, tProduct, tCE, tAbundance, tRIInt) 
												VALUES (:cid, :tProduct, :tCE, :tAbundance, :tRIInt)");

			$compound = array();
			$cOName = '';
			$cExisted = array();  // List of compounds that already exist and can not be insert
			$compound = fgetcsv($file); //First row with the titles
			//Take columns number_format
			if (count($compound) < 12) {
				$this->result["Code"] = 3005; 
				$this->result["CodeDetails"] = "File missing a key field";
				$this->returnJson($this->result);
				exit;
			}
			$cNumber = [];
			$cNumber['Other Names'] = array_search('Other Names', $compound);
			$cNumber['Compound Name'] = array_search('Compound Name', $compound);
			$cNumber['CAS'] = array_search('CAS', $compound);
			//$cNumber['Cayman #'] = array_search('Cayman #', $compound);
			//$cNumber['Compound Class'] = array_search('Compound Class', $compound);
			$cNumber['Formula'] = array_search('Formula', $compound);
			$cNumber['Mass'] = array_search('Mass', $compound);
			$cNumber['Precursor'] = array_search('Precursor', $compound);
			$cNumber['Product'] = array_search('Product', $compound);
			//$cNumber['No. of Transitions'] = array_search('No. of Transitions', $compound);
			$cNumber['CAV'] = array_search('CAV', $compound);
			$cNumber['Frag'] = array_search('Frag', $compound);
			$cNumber['CE'] = array_search('CE', $compound);
			$cNumber['Abundance'] = array_search('Abundance', $compound);
			$cNumber['Relative Ion Intensity'] = array_search('Relative Ion Intensity', $compound);
			/*
			if (in_array(false, $cNumber, true)) {
				$this->result["Code"] = 3005; 
				$this->result["CodeDetails"] = "Incomplete Information: missing some key fields,
				please check that the CSV file contains the following fields:
				Name, Other Names,CAS, Cayman #, Compound Class, Formula, Mass, Precursor, Product, No. of Transitions, Frag, CE, Abundance, Relative Ion Intensity .";
				$this->returnJson($this->result);
				exit;
			}
			*/
			$compoundExisted = true;
			while( !feof($file) )
			  {
				$compound = fgetcsv($file);
				if ( ($compound[$cNumber['Other Names']]!='') && ($cOName != $compound[$cNumber['Other Names']]) )
				{
					// New Compound
					$cOName = $compound[$cNumber['Other Names']];
					//Check if already exist this compound
					$psCompoundsCheck->bindParam(':cOName', $compound[$cNumber['Other Names']]);
					$psCompoundsCheck->execute();
					$cid = $psCompoundsCheck->fetch();
					if ( !empty($cid) )
					{
						//Already exist
						array_push($cExisted, $compound[$cNumber['Other Names']]);
						$compoundExisted = true;
					}
					else
					{
						$compoundExisted = false;
						//Check id exist the "class", else insert class and take id
						/*
						if ( $compound[$cNumber['Compound Class']] != '')
						{
							$psClassCheck->bindParam(':class', $compound[$cNumber['Compound Class']]);
							$psClassCheck->execute();
							$cClass = $psClassCheck->fetchColumn(0);
							if (empty($cClass))
							{
								//Insert
								$psClassInsert->bindParam(':class', $compound[$cNumber['Compound Class']]);
								$psClassInsert->execute();
								$cClass = $db->lastInsertId();
							}
						}
						*/
						//Insert in table "compounds"
						/*						
						$psCompoundsInsert->execute(array(':cName'=>$compound[$cNumber['Compound Name']], ':cFormula'=>$compound[$cNumber['Formula']], 
															':cOName'=>$compound[$cNumber['Other Names']], ':cMass'=>$compound[$cNumber['Mass']],
															':cPrecursor'=>$compound[$cNumber['Precursor']], ':cFrag'=>$compound[$cNumber['Frag']], 
															':cCAS'=>$compound[$cNumber['CAS']], ':cClass'=>$cClass,
															':cayman'=>$compound[$cNumber['Cayman #']]));
						*/									
						$psCompoundsInsert->execute(array(':cName'=>$compound[$cNumber['Compound Name']], ':cFormula'=>$compound[$cNumber['Formula']], 
															':cOName'=>$compound[$cNumber['Other Names']], ':cMass'=>$compound[$cNumber['Mass']],
															':cPrecursor'=>$compound[$cNumber['Precursor']], ':cFrag'=>$compound[$cNumber['Frag']], 
															':cCAS'=>$compound[$cNumber['CAS']], ':CAV'=>$compound[$cNumber['CAV']]));
						$cId = $db->lastInsertId();
					}
				}
				if ( ($compound[$cNumber['Formula']]!='') && (!$compoundExisted) )
				{
					//Insert in table Transition
					$psTransitionInsert->execute(array(':cid'=>$cId, ':tProduct'=>$compound[$cNumber['Product']], ':tCE'=>$compound[$cNumber['CE']], 
														':tAbundance'=>$compound[$cNumber['Abundance']], ':tRIInt'=>$compound[$cNumber['Relative Ion Intensity']]));
				}
			  }
			fclose($file);

			//Output
			echo json_encode($cExisted);
		}	
		
		private function diverse_array($vector) { 
			$result = array(); 
			foreach($vector as $key1 => $value1) 
				foreach($value1 as $key2 => $value2) 
					$result[$key2][$key1] = $value2; 
			return $result; 
		} 		
		
		private function impPics()
		{
			$this->checkisAdmin();
			$this->userRequestType = "SELECT";
			
			$dbAddress = $this->dbConfig["Address"];
			$dbName = $this->dbConfig["Name"];
			$dbUser = $this->dbConfig["User"];
			$dbPassword = $this->dbConfig["Password"];
			
			$db = new PDO("mysql:host=$dbAddress;dbname=$dbName", $dbUser, $dbPassword);

			
			$psCompoundCheck = $db->prepare("SELECT cid FROM compounds WHERE cOName=:fileName");
			$destination = '..\img';

			$files = $this->diverse_array($_FILES["files"]);

			foreach ($files as $file) {
				if (!$file["error"]) {
					$tmp_name = $file["tmp_name"];
					$name = $file["name"];
					//Check if the compound exist in the database
					$fileName = substr($name, 0,stripos($name, '.'));
					$psCompoundCheck->bindParam(':fileName', $fileName);
					$psCompoundCheck->execute();
					$cid = $psCompoundCheck->fetch();
					if ( !empty($cid) )
					{
						//The compound exist, save file
						move_uploaded_file($tmp_name, "$destination/$name");
						//php.ini  default=20 linux:etc max_file_upload = 20 Reset Apache
					}
				}
			}

			// http://php.net/manual/en/reserved.variables.files.php#109958
/*			
			$destination = '../img';

			$vector = $_FILES["files"];
			
			$result = array(); 
			foreach($vector as $key1 => $value1) 
				foreach($value1 as $key2 => $value2) 
					$result[$key2][$key1] = $value2; 
			$files =  $result;

			foreach ($files as $file) {
				if (!$file["error"]) {
					$tmp_name = $file["tmp_name"];
					$name = $file["name"];
					move_uploaded_file($tmp_name, "$destination/$name");
					//fopen('../img/index2.jpg', 'r');
				}
			}
			
			$this->returnJson($this->result);
*/		}
		
		private function getTrans()
		{
			$this->checkVariableNotEmpty($this->requests["CompId"], "Compound id");
			$this->userRequestType = "SELECT"; 
			//sql need it
			$this->sql = "SELECT * FROM transition WHERE transition.cid= ?";
			$this->arrayOfRequest[0] = $this->requests["CompId"];
			$this->requestDatabase(false);			
		}		

		private function addTrans()
		{
			$this->checkVariableNotEmpty($this->requests["Trans"][0]["cid"], "Compound id");
			$this->addTranswithCompID($this->requests["Trans"][0]["cid"]);
		}
		
		private function addTranswithCompID($cid)
		{
			$this->checkisAdminOrLabOP();
			$this->userRequestType = "INSERT";

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
			$this->checkisAdminOrLabOP();
			$this->checkVariableNotEmpty($this->requests["Transid"], "Id of transition ");
			$this->checkVariableNotEmpty($this->requests["product"], "Product ");
			$this->checkVariableNotEmpty($this->requests["CE"], "CE ");
			$this->checkVariableNotEmpty($this->requests["Abd"], "Abundance ");
			$this->checkVariableNotEmpty($this->requests["Intens"], "Intensity ");
			$this->userRequestType = "UPDATE";

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
			$this->checkisAdminOrLabOP();
			$this->checkVariableNotEmpty($this->requests["Transid"], "Id of transition ");
			$this->userRequestType = "DELETE"; 
			
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


		private function checkisAdmin()
		{
			if(($this->result["loggedUser"]["permission"] != "admin")){
				$this->result["Code"] = 1005; 
				$this->result["CodeDetails"] = "user has no permission for this module";
				$this->returnJson($this->result);
				exit;
			}
		}


		private function checkisAdminOrLabOP()
		{
			if(($this->result["loggedUser"]["permission"] != "admin") &&($this->result["loggedUser"]["permission"] != "labOP")){
				$this->result["Code"] = 1005; 
				$this->result["CodeDetails"] = "user has no permission for this module";
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
					
					if (($stmt->rowCount()<=0)&&($returnValue == false)){
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
			exit;
		}

		private function returnCSV($result,$title)
		{
			$file_csv = tmpfile();
			fputcsv($file_csv, $title, ',');
			foreach ($result as $line) {
				fputcsv($file_csv, $line, ',');
			}
			rewind($file_csv);
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename="export.csv";');
			/** Send file to browser for download */
			fpassthru($file_csv);
		}		

	}
?>